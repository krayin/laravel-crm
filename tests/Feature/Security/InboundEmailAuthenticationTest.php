<?php

use Illuminate\Support\Facades\RateLimiter;
use Webkul\Email\InboundEmailProcessor\Contracts\InboundEmailProcessor;

/**
 * The inbound parse endpoint is reachable without a session: the route drops the `user`
 * middleware and bootstrap/app.php exempts it from CSRF. Whatever the endpoint checks for
 * itself is therefore the only thing between the public internet and the CRM inbox.
 *
 * These tests assert the boundary rather than the parsing, so they do not need the
 * mailparse extension: a request that is not authorised must be turned away before the
 * processor runs.
 */
beforeEach(function () {
    $this->processorReached = false;

    app()->bind(InboundEmailProcessor::class, function () {
        return new class($this) implements InboundEmailProcessor
        {
            public function __construct(private $test) {}

            public function processMessagesFromAllFolders() {}

            public function processMessage($content = null): void
            {
                $this->test->processorReached = true;
            }
        };
    });

    // rejections are logged at most a few times a minute per client
    RateLimiter::clear('inbound-parse-rejected:127.0.0.1');
});

function forgedEmail(): string
{
    return implode("\r\n", [
        'From: "Chief Executive" <ceo@victim.test>',
        'To: sales@victim.test',
        'Subject: Please wire the payment',
        'Message-ID: <'.bin2hex(random_bytes(8)).'@attacker.test>',
        '',
        'Account details attached.',
    ]);
}

function inboundParseUrl(): string
{
    return '/'.trim(config('app.admin_path'), '/').'/mail/inbound-parse';
}

it('rejects an inbound parse request that carries no token', function () {
    config(['mail-receiver.inbound_token' => 'the-configured-token']);

    $response = test()->post(inboundParseUrl(), ['email' => forgedEmail()]);

    expect($response->getStatusCode())->toBe(403);
    expect($this->processorReached)->toBeFalse();
});

it('rejects an inbound parse request that carries the wrong token', function () {
    config(['mail-receiver.inbound_token' => 'the-configured-token']);

    $response = test()->withHeader('X-Inbound-Token', 'not-the-token')
        ->post(inboundParseUrl(), ['email' => forgedEmail()]);

    expect($response->getStatusCode())->toBe(403);
    expect($this->processorReached)->toBeFalse();
});

/**
 * 503 rather than 403: a provider retries a 5xx and discards the message on a 4xx, so an
 * installation that upgrades before setting the token must not lose customer mail.
 */
it('answers a retryable status while no token is configured', function () {
    config(['mail-receiver.inbound_token' => null]);

    $response = test()->post(inboundParseUrl(), ['email' => forgedEmail()]);

    expect($response->getStatusCode())->toBe(503);
    expect($this->processorReached)->toBeFalse();
});

it('accepts a request carrying the configured token', function () {
    config(['mail-receiver.inbound_token' => 'the-configured-token']);

    $response = test()->withHeader('X-Inbound-Token', 'the-configured-token')
        ->post(inboundParseUrl(), ['email' => forgedEmail()]);

    expect($response->getStatusCode())->toBe(200);
    expect($this->processorReached)->toBeTrue();
});

it('accepts the token as basic auth, which is what SendGrid allows in the URL', function () {
    config(['mail-receiver.inbound_token' => 'the-configured-token']);

    $response = test()->withHeaders([
        'Authorization' => 'Basic '.base64_encode('krayin:the-configured-token'),
    ])->post(inboundParseUrl(), ['email' => forgedEmail()]);

    expect($response->getStatusCode())->toBe(200);
    expect($this->processorReached)->toBeTrue();
});

/**
 * The route is registered under config('app.admin_path'), so the CSRF exemption in
 * bootstrap/app.php has to be built from the same value.
 *
 * Note this test cannot prove the exemption itself: Laravel's ValidateCsrfToken returns
 * early when runningUnitTests() is true, so CSRF never fires in this suite whatever the
 * exempt list says. What it does prove is that the endpoint answers on the configured
 * path, which is the half that is testable here. Run the suite with
 * APP_ADMIN_PATH=backoffice to exercise a non-default path.
 */
it('serves the webhook on the configured admin path', function () {
    config(['mail-receiver.inbound_token' => 'the-configured-token']);

    $response = test()->withHeader('X-Inbound-Token', 'the-configured-token')
        ->post(inboundParseUrl(), ['email' => forgedEmail()]);

    expect($response->getStatusCode())->toBe(200);
    expect($this->processorReached)->toBeTrue();
});
