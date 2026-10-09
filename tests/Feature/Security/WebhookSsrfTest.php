<?php

use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use Webkul\Automation\Services\WebhookService;
use Webkul\Contact\Repositories\PersonRepository;

/**
 * Workflow webhooks make server-side requests to an operator-supplied URL, so the endpoint is
 * validated against private, reserved, loopback and link-local ranges before the request is made.
 *
 * That check resolved the host and then let the HTTP client resolve it a second time, which is a
 * time-of-check to time-of-use gap: whoever controls the host's DNS can answer with a public
 * address while the check runs and 169.254.169.254 or 127.0.0.1 when the client connects. The
 * service now pins the connection to the addresses it validated, so the second lookup cannot
 * happen.
 *
 * These tests reach the protected methods through a subclass rather than a live DNS server, so they
 * run anywhere; the rebinding case is covered by asserting the pin is actually produced and carried
 * on the request.
 */
function webhookService(): object
{
    return new class(app(PersonRepository::class)) extends WebhookService
    {
        public function callIsSafeEndpoint(string $endPoint): bool
        {
            return $this->isSafeEndpoint($endPoint);
        }

        public function callResolveSafeEndpoint(string $endPoint): ?array
        {
            return $this->resolveSafeEndpoint($endPoint);
        }

        public function callIsPublicAddress(string $ip): bool
        {
            return $this->isPublicAddress($ip);
        }

        public function callBuildResolveOptions(array $endpoint): array
        {
            return $this->buildResolveOptions($endpoint);
        }
    };
}

it('rejects a loopback endpoint', function () {
    expect(webhookService()->callIsSafeEndpoint('http://127.0.0.1/hook'))->toBeFalse();
});

it('rejects the cloud metadata address', function () {
    expect(webhookService()->callIsSafeEndpoint('http://169.254.169.254/latest/meta-data/'))->toBeFalse();
});

it('rejects private ranges', function (string $endPoint) {
    expect(webhookService()->callIsSafeEndpoint($endPoint))->toBeFalse();
})->with([
    'http://10.0.0.1/hook',
    'http://192.168.1.1/hook',
    'http://172.16.0.1/hook',
    'http://[::1]/hook',
]);

it('rejects a non http scheme', function (string $endPoint) {
    expect(webhookService()->callIsSafeEndpoint($endPoint))->toBeFalse();
})->with([
    'file:///etc/passwd',
    'gopher://127.0.0.1/',
    'ftp://198.51.100.7/',
]);

/**
 * An IPv4-mapped IPv6 address carries an IPv4 target that the network stack will connect to. The
 * range flags do not look through the mapping on every build, so the address is reduced to its
 * IPv4 form before the check.
 */
it('rejects an ipv4 mapped ipv6 address that maps into a blocked range', function (string $ip) {
    expect(webhookService()->callIsPublicAddress($ip))->toBeFalse();
})->with([
    '::ffff:127.0.0.1',
    '::ffff:169.254.169.254',
    '::ffff:10.0.0.1',
    '::ffff:192.168.0.1',
    '::ffff:7f00:1',
    '::ffff:a9fe:a9fe',
]);

it('still accepts a public ipv4 mapped ipv6 address', function () {
    expect(webhookService()->callIsPublicAddress('::ffff:93.184.216.34'))->toBeTrue();
});

it('accepts a public literal address', function () {
    expect(webhookService()->callIsSafeEndpoint('http://93.184.216.34/hook'))->toBeTrue();
});

/**
 * The pin is what closes the rebinding race. Without the `host:port:ip` entries the client performs
 * its own second lookup, which is the window the attack uses.
 */
it('returns the validated addresses so the request can be pinned to them', function () {
    $endpoint = webhookService()->callResolveSafeEndpoint('http://93.184.216.34/hook');

    expect($endpoint)->not->toBeNull();
    expect($endpoint['host'])->toBe('93.184.216.34');
    expect($endpoint['port'])->toBe(80);
    expect($endpoint['ips'])->toBe(['93.184.216.34']);
});

it('defaults the pinned port to the scheme and honours an explicit one', function () {
    $service = webhookService();

    expect($service->callResolveSafeEndpoint('https://93.184.216.34/hook')['port'])->toBe(443);
    expect($service->callResolveSafeEndpoint('http://93.184.216.34:8080/hook')['port'])->toBe(8080);
});

it('builds curl resolve entries that keep the host name intact', function () {
    $entries = webhookService()->callBuildResolveOptions([
        'host' => 'webhook.example.com',
        'port' => 443,
        'ips' => ['93.184.216.34', '2606:2800:220:1:248:1893:25c8:1946'],
    ]);

    expect($entries)->toBe([
        'webhook.example.com:443:93.184.216.34',
        'webhook.example.com:443:2606:2800:220:1:248:1893:25c8:1946',
    ]);
});

it('refuses to trigger a webhook pointed at an internal address', function () {
    $result = app(WebhookService::class)->triggerWebhook([
        'method' => 'GET',
        'end_point' => 'http://169.254.169.254/latest/meta-data/',
    ]);

    expect($result['status'])->toBe('error');
    expect($result['response'])->toBe('The webhook endpoint URL is not allowed.');
});

/**
 * The guard sits in the handler stack, so it also covers a request that reaches the client without
 * passing `triggerWebhook`'s own up-front check.
 */
it('blocks an internal request at the client even when the up front check is bypassed', function () {
    $service = app(WebhookService::class);

    $client = (new ReflectionClass(WebhookService::class))->getProperty('client');
    $client->setAccessible(true);

    expect(fn () => $client->getValue($service)->request('GET', 'http://127.0.0.1/hook'))
        ->toThrow(RuntimeException::class, 'Blocked request to a disallowed webhook endpoint.');
});

/**
 * The regression test for the rebinding race itself.
 *
 * A request that leaves the client carrying no `CURLOPT_RESOLVE` entry is resolved a second time by
 * curl, which is exactly the window the attack uses: DNS answers public during the check and
 * internal at connect time. Asserting the pin is attached — and names the address that was
 * validated — is what proves the second lookup can no longer happen.
 */
it('pins the connection to the validated address so no second dns lookup can occur', function () {
    $seen = new stdClass;
    $seen->curl = null;

    $service = new class(app(PersonRepository::class), $seen) extends WebhookService
    {
        public function __construct($personRepository, private stdClass $seen)
        {
            parent::__construct($personRepository);

            /**
             * Same guard, mock transport: the options the guard produced are captured without any
             * real network traffic.
             */
            $stack = HandlerStack::create(
                new MockHandler([new Response(200, [], 'ok')])
            );

            $stack->push($this->ssrfGuardMiddleware());

            $stack->push(function (callable $next) {
                return function ($request, array $options) use ($next) {
                    $this->seen->curl = $options['curl'] ?? [];

                    return $next($request, $options);
                };
            });

            $this->client = new Client(['handler' => $stack, 'http_errors' => false]);
        }

        public function fire(string $url)
        {
            return $this->client->request('GET', $url);
        }
    };

    $service->fire('http://93.184.216.34:8080/hook');

    expect($seen->curl)->toHaveKey(CURLOPT_RESOLVE);

    expect($seen->curl[CURLOPT_RESOLVE])->toBe(['93.184.216.34:8080:93.184.216.34']);
});

/**
 * Build a service whose transport is mocked with `$responses`, recording the URL and pin of every
 * request that reaches the transport.
 */
function probedWebhookService(array $responses, stdClass $seen): object
{
    return new class(app(PersonRepository::class), $responses, $seen) extends WebhookService
    {
        public function __construct($personRepository, array $responses, private stdClass $seen)
        {
            parent::__construct($personRepository);

            $stack = HandlerStack::create(new MockHandler($responses));

            $stack->push($this->ssrfGuardMiddleware());

            $stack->push(function (callable $next) {
                return function ($request, array $options) use ($next) {
                    $this->seen->rows[] = [
                        'url' => (string) $request->getUri(),
                        'pin' => $options['curl'][CURLOPT_RESOLVE] ?? null,
                    ];

                    return $next($request, $options);
                };
            });

            $this->client = new Client([
                'handler' => $stack,
                'http_errors' => false,
                'allow_redirects' => [
                    'max' => 5,
                    'strict' => true,
                    'referer' => false,
                    'protocols' => ['http', 'https'],
                ],
            ]);
        }

        public function fire(string $url, array $options = [])
        {
            return $this->client->request('GET', $url, $options);
        }
    };
}

/**
 * The guard sits in the handler stack rather than in `on_redirect`, so each redirect hop is
 * re-resolved and re-validated in its own right. A 302 pointing at loopback must not be followed.
 */
it('blocks a redirect that points at an internal address', function () {
    $seen = new stdClass;
    $seen->rows = [];

    $service = probedWebhookService([
        new Response(302, ['Location' => 'http://127.0.0.1/internal']),
        new Response(200, [], 'INTERNAL-SECRET'),
    ], $seen);

    expect(fn () => $service->fire('http://93.184.216.34/hook'))
        ->toThrow(RuntimeException::class, 'Blocked request to a disallowed webhook endpoint.');

    // Only the first hop reached the transport; the internal one never did.
    expect($seen->rows)->toHaveCount(1);
    expect($seen->rows[0]['url'])->toBe('http://93.184.216.34/hook');
});

/**
 * A followed redirect must be pinned to its *own* address. Carrying the previous hop's pin forward
 * would either break the request or, worse, send it somewhere the guard never vetted.
 */
it('pins each redirect hop to its own validated address', function () {
    $seen = new stdClass;
    $seen->rows = [];

    $service = probedWebhookService([
        new Response(302, ['Location' => 'http://198.51.100.7/second']),
        new Response(200, [], 'ok'),
    ], $seen);

    $service->fire('http://93.184.216.34/hook');

    expect($seen->rows)->toHaveCount(2);

    expect($seen->rows[0]['pin'])->toBe(['93.184.216.34:80:93.184.216.34']);
    expect($seen->rows[1]['pin'])->toBe(['198.51.100.7:80:198.51.100.7']);
});

/**
 * The pin is assigned, not merged. A `+` union keeps the left operand's existing key, so a
 * CURLOPT_RESOLVE arriving with the request options would otherwise win over the validated pin and
 * redirect the connection to an arbitrary address.
 */
it('overrides a caller supplied resolve entry instead of deferring to it', function () {
    $seen = new stdClass;
    $seen->rows = [];

    $service = probedWebhookService([new Response(200, [], 'ok')], $seen);

    $service->fire('http://93.184.216.34/hook', [
        'curl' => [CURLOPT_RESOLVE => ['93.184.216.34:80:127.0.0.1']],
    ]);

    expect($seen->rows[0]['pin'])->toBe(['93.184.216.34:80:93.184.216.34']);
});
