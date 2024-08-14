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
        $this->client = new Client;
    }

    /**
     * Trigger the webhook.
     */
    public function triggerWebhook(mixed $data): array
    {
        $options = [
            'headers'     => $this->formatHeaders(json_decode($data['headers'], true)),
            'form_params' => $this->formatPayload(json_decode($data['payload'], true)),
        ];

        try {
            $response = $this->client->request(
                $data['method'],
                $data['end_point'],
                $options,
            );

            return [
                'status'   => 'success',
                'response' => $response->getBody()->getContents(),
            ];
        } catch (RequestException $e) {
            return [
                'status'   => 'error',
                'response' => $e->hasResponse() ? Message::toString($e->getResponse()) : $e->getMessage(),
            ];
        }
    }

    /**
     * Format headers array.
     */
    protected function formatHeaders(array $headers): array
    {
        $formattedHeaders = [];

        foreach ($headers as $header) {
            $formattedHeaders[$header['key']] = $header['value'];
        }

        return $formattedHeaders;
    }

    /**
     * Format payload array.
     */
    private function formatPayload($payload): array|string
    {
        if (! is_array($payload)) {
            $payload = json_decode($payload, true);
        }

        $formattedPayload = [];

        if (
            isset($payload['key'])
            && isset($payload['value'])
        ) {
            $formattedPayload[$payload['key']] = $payload['value'];
        } else {
            foreach ($payload as $item) {
                $formattedPayload[$item['key']] = $item['value'];
            }
        }

        return $formattedPayload;
    }
}
