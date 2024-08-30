<?php

namespace Webkul\Automation\Helpers\Entity;

use Carbon\Carbon;
use Webkul\Attribute\Repositories\AttributeRepository;
use Webkul\Automation\Repositories\WebhookRepository;
use Webkul\Automation\Services\WebhookService;

abstract class AbstractEntity
{
    /**
     * Create a new repository instance
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
     * Listing of the entities
     */
    abstract public function getEntity(mixed $entity);

    /**
     * Returns workflow actions
     */
    abstract public function getActions();

    /**
     * Execute workflow actions
     */
    abstract public function executeActions(mixed $workflow, mixed $entity): void;

    /**
     * Returns attributes for workflow conditions
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
                'id'           => $attribute->code,
                'type'         => $attribute->type,
                'name'         => $attribute->name,
                'lookup_type'  => $attribute->lookup_type,
                'options'      => $options,
            ];
        }

        return $attributes;
    }

    /**
     * Returns placeholders for email templates
     */
    public function getEmailTemplatePlaceholders(array $entity): array
    {
        $menuItems = [];

        foreach ($this->getAttributes($this->entityType) as $attribute) {
            $menuItems[] = [
                'text'  => $attribute['name'],
                'value' => '{%'.$this->entityType.'.'.$attribute['id'].'%}',
            ];
        }

        return [
            'text' => $entity['name'],
            'menu' => $menuItems,
        ];
    }

    /**
     * Replace placeholders with values
     */
    public function replacePlaceholders(mixed $entity, string $content): string
    {
        foreach ($this->getAttributes($this->entityType, []) as $attribute) {
            $value = '';

            switch ($attribute['type']) {
                case 'price':
                    $value = core()->formatBasePrice($entity->{$attribute['id']});

                    break;

                case 'boolean':
                    $value = $entity->{$attribute['id']} ? __('admin::app.common.yes') : __('admin::app.common.no');

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
                        if (! is_object($entity->{$attribute['id']})) {
                            $entity->{$attribute['id']} = Carbon::parse($entity->{$attribute['id']});
                        }

                        $value = $entity->{$attribute['id']}->format('D M d, Y');
                    } else {
                        $value = 'N/A';
                    }

                    break;

                case 'datetime':
                    if ($entity->{$attribute['id']}) {
                        if (! is_object($entity->{$attribute['id']})) {
                            $entity->{$attribute['id']} = Carbon::parse($entity->{$attribute['id']});
                        }

                        $value = $entity->{$attribute['id']}->format('D M d, Y H:i A');
                    } else {
                        $value = 'N/A';
                    }

                    break;

                default:
                    $value = $entity->{$attribute['id']};

                    break;
            }

            $content = strtr($content, [
                '{%'.$this->entityType.'.'.$attribute['id'].'%}'   => $value,
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

        $payload = [
            'method'    => $webhook->method,
            'end_point' => $this->replacePlaceholders($entity, $webhook->end_point),
            'payload'   => $this->replacePlaceholders($entity, json_encode($webhook->payload)),
            'headers'   => $this->replacePlaceholders($entity, json_encode($webhook->headers)),
        ];

        $this->webhookService->triggerWebhook($payload);
    }
}
