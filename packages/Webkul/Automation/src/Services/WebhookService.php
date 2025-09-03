<?php

namespace Webkul\Automation\Services;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Psr7\Message;
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
        $this->client = new Client([
            'timeout'         => 30,
            'connect_timeout' => 10,
            'verify'          => true,
            'http_errors'     => false,
        ]);
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
                'status'   => 'error',
                'response' => 'Missing required fields: method or end_point',
            ];
        }

        $headers = isset($data['headers']) ? $this->parseJsonField($data['headers']) : [];
        $payload = isset($data['payload']) ? $data['payload'] : null;
        $data['end_point'] = $this->appendQueryParams($data['end_point'], $data['query_params'] ?? '');

        $formattedHeaders = $this->formatHeaders($headers);

        $options = $this->buildRequestOptions($data['method'], $formattedHeaders, $payload);

        try {
            $response = $this->client->request(
                strtoupper($data['method']),
                $data['end_point'],
                $options,
            );

            return [
                'status'      => 'success',
                'response'    => $response->getBody()->getContents(),
                'status_code' => $response->getStatusCode(),
                'headers'     => $response->getHeaders(),
            ];
        } catch (RequestException $e) {
            return [
                'status'      => 'error',
                'response'    => $e->hasResponse() ? Message::toString($e->getResponse()) : $e->getMessage(),
                'status_code' => $e->hasResponse() ? $e->getResponse()->getStatusCode() : null,
            ];
        }
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
                'name'     => $key,
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
