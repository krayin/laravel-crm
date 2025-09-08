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

        $payload = isset($data) ? $data : null;

        $options = $this->buildRequestOptions($data['method'], $payload);
       
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
     * Build workflow payload.
     */
    protected function buildWorkflowPayload(mixed $workflow): array
    {
        return [
            'name'        => $workflow->name,
            'description' => $workflow->description,
            'entity_type' => $workflow->entity_type,
            'event'       => $workflow->event,
        ];
    }

    /**
     * Build request options based on method and content type.
     */
    protected function buildRequestOptions(string $method, mixed $data): array
    {
        $options = [];

        if (
            $data !== null
            && ! in_array(strtoupper($method), ['GET', 'HEAD'])
        ) {
            $options['json'] = [
                'data' => $data['payload'],
            ];
        }

        /**
         * If workflow data is present, merge it into the JSON payload
         */
        if (isset($data['workflow'])) {
            $options['json'] = array_merge($options['json'], $this->buildWorkflowPayload($data['workflow']));
        }

        /**
         * Remove any key-value pairs from the payload array where the key ends with '_id'
         */
        $options['json'] = $this->removeIdKeys($options['json'], ['_id', 'user', 'attribute_values']);

        return $options;
    }

    /**
     * Recursively remove any key-value pairs where the key is in the $keysToRemove array (at any depth).
     *
     * @param array $array The array to process
     * @param array $keysToRemove The keys to remove (exact match)
     * @return array
     */
    protected function removeIdKeys(array $array, array $keysToRemove = []): array
    {
        foreach ($array as $key => &$value) {
            if (is_array($value)) {
                $value = $this->removeIdKeys($value, $keysToRemove);
            }

            /**
             * Remove if key exactly matches or ends with any of the keysToRemove
             */
            foreach ($keysToRemove as $removeKey) {
                if ($key === $removeKey 
                    || (is_string($removeKey)
                    && is_string($key)
                    && str_ends_with($key, $removeKey))) {
                        unset($array[$key]);
                    
                        break;
                }
            }
        }

        return $array;
    }
}
