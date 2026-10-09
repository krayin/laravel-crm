<?php

namespace Webkul\Automation\Services;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Message;
use Psr\Http\Message\RequestInterface;
use Webkul\Contact\Repositories\PersonRepository;

class WebhookService
{
    /**
     * The GuzzleHttp client instance.
     */
    protected Client $client;

    /**
     * Create a new webhook service instance.
     */
    public function __construct(protected PersonRepository $personRepository)
    {
        $stack = HandlerStack::create();

        /**
         * Validate and pin every outgoing request, the initial one and each redirect hop alike.
         * `on_redirect` cannot do the pinning: it runs after the request options for the next hop
         * have been fixed, so a pin added there would never be applied.
         */
        $stack->push($this->ssrfGuardMiddleware());

        $this->client = new Client([
            'handler' => $stack,
            'timeout' => 30,
            'connect_timeout' => 10,
            'verify' => true,
            'http_errors' => false,
            'allow_redirects' => [
                'max' => 5,
                'strict' => true,
                'referer' => false,
                'protocols' => ['http', 'https'],
            ],
        ]);
    }

    /**
     * Middleware that re-resolves and re-validates the target of every request passing through the
     * client, then pins the connection to the addresses it just vetted.
     *
     * Pinning is what closes the DNS rebinding race: without it the client performs its own second
     * lookup, which an attacker controlling the host's DNS can answer with an internal address
     * after the check has already passed.
     */
    protected function ssrfGuardMiddleware(): callable
    {
        return function (callable $handler) {
            return function (RequestInterface $request, array $options) use ($handler) {
                $endpoint = $this->resolveSafeEndpoint((string) $request->getUri());

                if ($endpoint === null) {
                    throw new \RuntimeException('Blocked request to a disallowed webhook endpoint.');
                }

                if (defined('CURLOPT_RESOLVE')) {
                    /**
                     * Overwrite rather than merge: a `+` union keeps an existing entry, so a
                     * CURLOPT_RESOLVE supplied by the caller — or left over from an earlier hop —
                     * would win over the pin computed here and could aim the connection anywhere.
                     */
                    $options['curl'] ??= [];

                    $options['curl'][CURLOPT_RESOLVE] = $this->buildResolveOptions($endpoint);
                }

                return $handler($request, $options);
            };
        };
    }

    /**
     * Trigger the webhook.
     */
    public function triggerWebhook(mixed $data): array
    {
        if (
            ! isset($data['method'])
            || ! isset($data['end_point'])
        ) {
            return [
                'status' => 'error',
                'response' => 'Missing required fields: method or end_point',
            ];
        }

        $headers = isset($data['headers']) ? $this->parseJsonField($data['headers']) : [];
        $payload = isset($data['payload']) ? $data['payload'] : null;
        $data['end_point'] = $this->appendQueryParams($data['end_point'], $data['query_params'] ?? '');

        if (! $this->isSafeEndpoint($data['end_point'])) {
            return [
                'status' => 'error',
                'response' => 'The webhook endpoint URL is not allowed.',
            ];
        }

        $formattedHeaders = $this->formatHeaders($headers);

        $options = $this->buildRequestOptions($data['method'], $formattedHeaders, $payload);

        try {
            $response = $this->client->request(
                strtoupper($data['method']),
                $data['end_point'],
                $options,
            );

            return [
                'status' => 'success',
                'response' => $response->getBody()->getContents(),
                'status_code' => $response->getStatusCode(),
                'headers' => $response->getHeaders(),
            ];
        } catch (RequestException $e) {
            return [
                'status' => 'error',
                'response' => $e->hasResponse() ? Message::toString($e->getResponse()) : $e->getMessage(),
                'status_code' => $e->hasResponse() ? $e->getResponse()->getStatusCode() : null,
            ];
        } catch (\Throwable $e) {
            return [
                'status' => 'error',
                'response' => $e->getMessage(),
            ];
        }
    }

    /**
     * Guard against Server-Side Request Forgery (SSRF). Only plain HTTP(S) endpoints whose host
     * resolves exclusively to public addresses are permitted. Endpoints that resolve to private,
     * reserved, loopback, or link-local ranges (e.g. cloud metadata services) are rejected.
     */
    protected function isSafeEndpoint(string $endPoint): bool
    {
        return $this->resolveSafeEndpoint($endPoint) !== null;
    }

    /**
     * Resolve an endpoint's host and validate every address it answers with, returning the host,
     * port and vetted addresses — or null when the endpoint must not be requested.
     *
     * Validating the host and then letting the HTTP client resolve it again is a time-of-check to
     * time-of-use gap: whoever controls the host's DNS can answer with a public address while this
     * check runs and an internal one (169.254.169.254, 127.0.0.1) when the client connects. The
     * caller pins the connection to the addresses returned here so the second lookup cannot happen.
     */
    protected function resolveSafeEndpoint(string $endPoint): ?array
    {
        $scheme = strtolower((string) parse_url($endPoint, PHP_URL_SCHEME));

        $host = parse_url($endPoint, PHP_URL_HOST);

        if (
            ! in_array($scheme, ['http', 'https'])
            || empty($host)
        ) {
            return null;
        }

        $host = trim($host, '[]');

        if (filter_var($host, FILTER_VALIDATE_IP)) {
            $ips = [$host];
        } else {
            $records = array_merge(
                @dns_get_record($host, DNS_A) ?: [],
                @dns_get_record($host, DNS_AAAA) ?: [],
            );

            $ips = array_values(array_filter(array_map(
                fn ($record) => $record['ip'] ?? $record['ipv6'] ?? null,
                $records,
            )));
        }

        if (empty($ips)) {
            return null;
        }

        foreach ($ips as $ip) {
            if (! $this->isPublicAddress($ip)) {
                return null;
            }
        }

        $port = parse_url($endPoint, PHP_URL_PORT) ?: ($scheme === 'https' ? 443 : 80);

        return [
            'host' => $host,
            'port' => (int) $port,
            'ips' => $ips,
        ];
    }

    /**
     * Decide whether a single address is a public one the webhook may reach.
     *
     * An IPv4-mapped IPv6 address (`::ffff:127.0.0.1`, and its hex form `::ffff:7f00:1`) is reduced
     * to the IPv4 address it carries before the range check, because on some builds the range flags
     * do not look through the mapping while the network stack still connects to the mapped target.
     */
    protected function isPublicAddress(string $ip): bool
    {
        if (! filter_var($ip, FILTER_VALIDATE_IP)) {
            return false;
        }

        $mapped = $this->unmapIpv4($ip);

        if ($mapped !== null) {
            $ip = $mapped;
        }

        return (bool) filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE);
    }

    /**
     * Return the IPv4 address carried by an IPv4-mapped IPv6 address, or null when there is none.
     */
    protected function unmapIpv4(string $ip): ?string
    {
        $packed = @inet_pton($ip);

        if (
            $packed === false
            || strlen($packed) !== 16
        ) {
            return null;
        }

        // ::ffff:0:0/96 — the first 10 bytes are zero, the next two are 0xff.
        if (substr($packed, 0, 10) !== str_repeat("\0", 10)) {
            return null;
        }

        if (substr($packed, 10, 2) !== "\xff\xff") {
            return null;
        }

        return inet_ntop(substr($packed, 12, 4)) ?: null;
    }

    /**
     * Build the curl `host:port:ip` entries that pin a request to the addresses already validated,
     * leaving the Host header (and TLS SNI/certificate validation) untouched.
     */
    protected function buildResolveOptions(array $endpoint): array
    {
        $entries = [];

        foreach ($endpoint['ips'] as $ip) {
            $entries[] = $endpoint['host'].':'.$endpoint['port'].':'.$ip;
        }

        return $entries;
    }

    /**
     * Parse JSON field safely.
     */
    protected function parseJsonField(mixed $field): array
    {
        if (is_array($field)) {
            return $field;
        }

        if (is_string($field)) {
            $decoded = json_decode($field, true);

            if (
                json_last_error() === JSON_ERROR_NONE
                && is_array($decoded)
            ) {
                return $decoded;
            }
        }

        return [];
    }

    /**
     * Build request options based on method and content type.
     */
    protected function buildRequestOptions(string $method, array $headers, mixed $payload): array
    {
        $options = [];

        if (! empty($headers)) {
            $options['headers'] = $headers;
        }

        if (
            $payload !== null
            && ! in_array(strtoupper($method), ['GET', 'HEAD'])
        ) {
            $contentType = $this->getContentType($headers);

            switch ($contentType) {
                case 'application/json':
                    $options['json'] = $this->prepareJsonPayload($payload);

                    break;

                case 'application/x-www-form-urlencoded':
                    $options['form_params'] = $this->prepareFormPayload($payload);

                    break;

                case 'multipart/form-data':
                    $options['multipart'] = $this->prepareMultipartPayload($payload);

                    break;

                case 'text/plain':
                case 'text/xml':
                case 'application/xml':
                    $options['body'] = $this->prepareRawPayload($payload);

                    break;

                default:
                    $options = array_merge($options, $this->autoDetectPayloadFormat($payload));

                    break;
            }
        }

        return $options;
    }

    /**
     * Prepare JSON payload.
     */
    protected function prepareJsonPayload(mixed $payload): mixed
    {
        if (is_string($payload)) {
            $decoded = json_decode($payload, true);

            if (json_last_error() === JSON_ERROR_NONE) {
                return $decoded;
            }

            return $payload;
        }

        if (is_array($payload)) {
            return $this->formatPayload($payload);
        }

        return $payload;
    }

    /**
     * Prepare form payload.
     */
    protected function prepareFormPayload(mixed $payload): array
    {
        if (is_string($payload)) {
            $decoded = json_decode($payload, true);

            if (
                json_last_error() === JSON_ERROR_NONE
                && is_array($decoded)
            ) {
                return $this->formatPayload($decoded);
            }

            parse_str($payload, $parsed);

            return $parsed ?: [];
        }

        if (is_array($payload)) {
            return $this->formatPayload($payload);
        }

        return [];
    }

    /**
     * Prepare multipart payload.
     */
    protected function prepareMultipartPayload(mixed $payload): array
    {
        $formattedPayload = $this->prepareFormPayload($payload);

        return $this->buildMultipartData($formattedPayload);
    }

    /**
     * Prepare raw payload.
     */
    protected function prepareRawPayload(mixed $payload): string
    {
        if (is_string($payload)) {
            return $payload;
        }

        if (is_array($payload)) {
            return json_encode($payload);
        }

        return (string) $payload;
    }

    /**
     * Auto-detect payload format when no content-type is specified.
     */
    protected function autoDetectPayloadFormat(mixed $payload): array
    {
        if (is_string($payload)) {
            $decoded = json_decode($payload, true);

            if (json_last_error() === JSON_ERROR_NONE) {
                return ['json' => $decoded];
            }

            if (
                strpos($payload, '=') !== false
                && strpos($payload, '&') !== false
            ) {
                parse_str($payload, $parsed);

                return ['form_params' => $parsed];
            }

            return ['body' => $payload];
        }

        if (is_array($payload)) {
            $formatted = $this->formatPayload($payload);

            return ['json' => $formatted];
        }

        return ['body' => (string) $payload];
    }

    /**
     * Get content type from headers.
     */
    protected function getContentType(array $headers): string
    {
        foreach ($headers as $key => $value) {
            if (strtolower($key) === 'content-type') {
                $contentType = strtolower(trim(explode(';', $value)[0]));

                return $contentType;
            }
        }

        return '';
    }

    /**
     * Build multipart data array.
     */
    protected function buildMultipartData(array $payload): array
    {
        $multipart = [];

        foreach ($payload as $key => $value) {
            $multipart[] = [
                'name' => $key,
                'contents' => is_array($value) ? json_encode($value) : (string) $value,
            ];
        }

        return $multipart;
    }

    /**
     * Format headers array.
     */
    protected function formatHeaders(array $headers): array
    {
        if (empty($headers)) {
            return [];
        }

        $formattedHeaders = [];

        if ($this->isKeyValuePairArray($headers)) {
            foreach ($headers as $header) {
                if (
                    isset($header['key'])
                    && array_key_exists('value', $header)
                ) {
                    if (
                        isset($header['disabled'])
                        && $header['disabled']
                    ) {
                        continue;
                    }

                    if (
                        isset($header['enabled'])
                        && ! $header['enabled']
                    ) {
                        continue;
                    }

                    $formattedHeaders[$header['key']] = $header['value'];
                }
            }
        } else {
            $formattedHeaders = $headers;
        }

        return $formattedHeaders;
    }

    /**
     * Format any incoming payload into a clean associative array.
     */
    protected function formatPayload(mixed $payload): array
    {
        if (empty($payload)) {
            return [];
        }

        if (
            is_array($payload)
            && isset($payload['key'])
            && array_key_exists('value', $payload)
        ) {
            return [$payload['key'] => $payload['value']];
        }

        if (
            is_array($payload)
            && array_is_list($payload)
            && $this->isKeyValuePairArray($payload)
        ) {
            $formatted = [];

            foreach ($payload as $item) {
                if (
                    isset($item['key'])
                    && array_key_exists('value', $item)
                ) {
                    if (
                        isset($item['disabled'])
                        && $item['disabled']
                    ) {
                        continue;
                    }

                    if (
                        isset($item['enabled'])
                        && ! $item['enabled']
                    ) {
                        continue;
                    }

                    $formatted[$item['key']] = $item['value'];
                }
            }

            return $formatted;
        }

        return is_array($payload) ? $payload : [];
    }

    /**
     * Check if array is a key-value pair array.
     */
    protected function isKeyValuePairArray(array $array): bool
    {
        if (empty($array)) {
            return false;
        }

        if (
            isset($array['key'])
            && array_key_exists('value', $array)
        ) {
            return true;
        }

        if (array_is_list($array)) {
            return collect($array)->every(fn ($item) => is_array($item) && isset($item['key']) && array_key_exists('value', $item)
            );
        }

        return false;
    }

    /**
     * Append query parameters to the endpoint URL.
     */
    protected function appendQueryParams(string $endPoint, string $queryParamsJson): string
    {
        $queryParams = json_decode($queryParamsJson, true);

        if (
            json_last_error() !== JSON_ERROR_NONE
            || ! is_array($queryParams)
        ) {
            return $endPoint;
        }

        $queryArray = [];

        foreach ($queryParams as $param) {
            if (
                isset($param['key'])
                && array_key_exists('value', $param)
            ) {
                $queryArray[$param['key']] = $param['value'];
            }
        }

        $queryString = http_build_query($queryArray);

        $glue = str_contains($endPoint, '?') ? '&' : '?';

        return $endPoint.($queryString ? $glue.$queryString : '');
    }
}
