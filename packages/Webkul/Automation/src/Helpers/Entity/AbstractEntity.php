<?php

namespace Webkul\Automation\Helpers\Entity;

use Carbon\Carbon;
use Webkul\Attribute\Repositories\AttributeRepository;
use Webkul\Automation\Repositories\WebhookRepository;
use Webkul\Automation\Services\WebhookService;

abstract class AbstractEntity
{
    /**
     * Attribute repository instance.
     */
    protected AttributeRepository $attributeRepository;

    /**
     * Create a new repository instance.
     */
    public function __construct(
        protected WebhookService $webhookService,
        protected WebhookRepository $webhookRepository,
    ) {}

    /**
     * Listing of the entities.
     */
    abstract public function getEntity(mixed $entity);

    /**
     * Returns workflow actions.
     */
    abstract public function getActions();

    /**
     * Execute workflow actions.
     */
    abstract public function executeActions(mixed $workflow, mixed $entity): void;

    /**
     * Returns attributes for workflow conditions.
     */
    public function getConditions(): array
    {
        return $this->getAttributes($this->entityType);
    }

    /**
     * Get attributes for entity.
     */
    public function getAttributes(string $entityType, array $skipAttributes = ['textarea', 'image', 'file', 'address']): array
    {
        $attributes = [];

        foreach ($this->attributeRepository->findByField('entity_type', $entityType) as $attribute) {
            if (in_array($attribute->type, $skipAttributes)) {
                continue;
            }

            if ($attribute->lookup_type) {
                $options = [];
            } else {
                $options = $attribute->options;
            }

            $attributes[] = [
                'id' => $attribute->code,
                'type' => $attribute->type,
                'name' => $attribute->name,
                'lookup_type' => $attribute->lookup_type,
                'options' => $options,
            ];
        }

        return $attributes;
    }

    /**
     * Returns placeholders for email templates.
     */
    public function getEmailTemplatePlaceholders(array $entity): array
    {
        $menuItems = [];

        foreach ($this->getAttributes($this->entityType) as $attribute) {
            $menuItems[] = [
                'text' => $attribute['name'],
                'value' => '{%'.$this->entityType.'.'.$attribute['id'].'%}',
            ];
        }

        return [
            'text' => $entity['name'],
            'menu' => $menuItems,
        ];
    }

    /**
     * Escape a substituted value for the context the placeholder sits in.
     *
     * A placeholder's value is entity data, and on the public web-form path it is supplied by an
     * anonymous submitter, so it is never safe to splice in raw:
     *
     * - `html`: the email templates are rendered with `{!! $body !!}`, so an unescaped value becomes
     *   markup in the delivered mail — forged content, phishing links, tracking pixels.
     * - `url`: the webhook endpoint is a URL template, so an unencoded value can introduce `?`, `&`,
     *   `#` or `/../` and restructure the path and query the server then requests.
     * - `json`: the query params, payload and headers are encoded first and substituted after, so an
     *   unescaped quote or backslash breaks out of its JSON string.
     */
    protected function escapePlaceholderValue(string $value, string $context): string
    {
        return match ($context) {
            'url' => rawurlencode($value),
            'json' => substr(json_encode($value, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES), 1, -1),
            'html' => htmlspecialchars($value, ENT_QUOTES, 'UTF-8'),
            default => $value,
        };
    }

    /**
     * Replace placeholders with values.
     *
     * `$context` says how a substituted value is escaped — see `escapePlaceholderValue()`. It
     * defaults to `html` because every caller but the webhook renders into an email body.
     */
    public function replacePlaceholders(mixed $entity, string $content, string $context = 'html'): string
    {
        foreach ($this->getAttributes($this->entityType, []) as $attribute) {
            $value = '';

            switch ($attribute['type']) {
                case 'price':
                    $value = core()->formatBasePrice($entity->{$attribute['id']});

                    break;

                case 'boolean':
                    $value = $entity->{$attribute['id']} ? trans('admin::app.common.yes') : trans('admin::app.common.no');

                    break;

                case 'select':
                case 'radio':
                case 'lookup':
                    if ($attribute['lookup_type']) {
                        $option = $this->attributeRepository->getLookUpEntity($attribute['lookup_type'], $entity->{$attribute['id']});
                    } else {
                        $option = $attribute['options']->where('id', $entity->{$attribute['id']})->first();
                    }

                    $value = $option ? $option->name : '';

                    break;

                case 'multiselect':
                case 'checkbox':
                    if ($attribute['lookup_type']) {
                        $options = $this->attributeRepository->getLookUpEntity($attribute['lookup_type'], explode(',', $entity->{$attribute['id']}));
                    } else {
                        $options = $attribute['options']->whereIn('id', explode(',', $entity->{$attribute['id']}));
                    }

                    $optionsLabels = [];

                    foreach ($options as $key => $option) {
                        $optionsLabels[] = $option->name;
                    }

                    $value = implode(', ', $optionsLabels);

                    break;

                case 'email':
                case 'phone':
                    if (! is_array($entity->{$attribute['id']})) {
                        break;
                    }

                    $optionsLabels = [];

                    foreach ($entity->{$attribute['id']} as $item) {
                        $optionsLabels[] = $item['value'].' ('.$item['label'].')';
                    }

                    $value = implode(', ', $optionsLabels);

                    break;

                case 'address':
                    if (! $entity->{$attribute['id']} || ! count(array_filter($entity->{$attribute['id']}))) {
                        break;
                    }

                    $value = $entity->{$attribute['id']}['address'].'<br>'
                             .$entity->{$attribute['id']}['postcode'].'  '.$entity->{$attribute['id']}['city'].'<br>'
                             .core()->state_name($entity->{$attribute['id']}['state']).'<br>'
                             .core()->country_name($entity->{$attribute['id']}['country']).'<br>';

                    break;

                case 'date':
                    if ($entity->{$attribute['id']}) {
                        $value = ! is_object($entity->{$attribute['id']})
                            ? Carbon::parse($entity->{$attribute['id']})
                            : $entity->{$attribute['id']}->format('D M d, Y');
                    } else {
                        $value = 'N/A';
                    }

                    break;

                case 'datetime':
                    if ($entity->{$attribute['id']}) {
                        $value = ! is_object($entity->{$attribute['id']})
                            ? Carbon::parse($entity->{$attribute['id']})
                            : $entity->{$attribute['id']}->format('D M d, Y H:i A');
                    } else {
                        $value = 'N/A';
                    }

                    break;

                default:
                    $value = $entity->{$attribute['id']};

                    break;
            }

            $value = $this->escapePlaceholderValue((string) $value, $context);

            $content = strtr($content, [
                '{%'.$this->entityType.'.'.$attribute['id'].'%}' => $value,
                '{% '.$this->entityType.'.'.$attribute['id'].' %}' => $value,
            ]);
        }

        return $content;
    }

    /**
     * Trigger webhook.
     *
     * @return void
     */
    public function triggerWebhook(int $webhookId, mixed $entity)
    {
        $webhook = $this->webhookRepository->findOrFail($webhookId);

        /**
         * The endpoint is a URL and the other three are already-encoded JSON, so each is escaped
         * for its own context rather than substituted raw.
         */
        $payload = [
            'method' => $webhook->method,
            'query_params' => $this->replacePlaceholders($entity, json_encode($webhook->query_params), 'json'),
            'end_point' => $this->replacePlaceholders($entity, $webhook->end_point, 'url'),
            'payload' => $this->replacePlaceholders($entity, json_encode($webhook->payload), 'json'),
            'headers' => $this->replacePlaceholders($entity, json_encode($webhook->headers), 'json'),
        ];

        $this->webhookService->triggerWebhook($payload);
    }
}
