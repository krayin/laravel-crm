<?php

namespace Webkul\Workflow\Helpers\Entity;

abstract class AbstractEntity
{
    /**
     * Returns attributes for workflow conditions
     *
     * @return array
     */
    public function getConditions()
    {
        return $this->getAttributes($this->entityType);
    }

    /**
     * Returns attributes
     *
     * @param  string  $entityType
     * @return array
     */
    public function getAttributes($entityType)
    {
        $attributes = [];

        foreach ($this->attributeRepository->findByField('entity_type', $entityType) as $attribute) {
            if (in_array($attribute->type, ['textarea', 'image', 'file', 'address'])) {
                continue;
            }

            $attributeType = $attribute->type;

            if ($attribute->validation == 'decimal') {
                $attributeType = 'decimal';
            }

            if ($attribute->validation == 'numeric') {
                $attributeType = 'integer';
            }

            if ($attribute->lookup_type) {
                $options = $this->attributeRepository->getLookUpOptions($attribute->lookup_type);
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
     * Replace placeholders with values
     * 
     * @param  array  $entity
     * @param  array  $values
     * @return string
     */
    public function replacePlaceholders($entity, $content)
    {
        $attributes = $this->attributeRepository->findByField('entity_type', $this->entityType);

        foreach ($attributes as $attribute) {
            $value = '';
            
            switch ($attribute->type) {
                case 'price':
                    $value = core()->formatBasePrice($entity->{$attribute->code});

                    break;

                case 'boolean':
                    $value = $entity->{$attribute->code} ? __('admin::app.common.yes') : __('admin::app.common.no');

                    break;

                case 'select':
                case 'radio':
                case 'lookup':
                    if ($attribute->lookup_type) {
                        $option = $this->attributeRepository->getLookUpEntity($attribute->lookup_type, $entity->{$attribute->code});
                    } else {
                        $option = $attribute->options()->where('id', $entity->{$attribute->code})->first();
                    }

                    $value = $option ? $option->name : __('admin::app.common.not-available');

                    break;

                case 'multiselect':
                case 'checkbox':
                    if ($attribute->lookup_type) {
                        $options = $this->attributeRepository->getLookUpEntity($attribute->lookup_type, explode(',', $entity->{$attribute->code}));
                    } else {
                        $options = $attribute->options()->where('id', $entity->{$attribute->code})->get();
                    }

                    $optionsLabels = [];

                    foreach ($options as $key => $option) {
                        $optionsLabels[] = $option->name;
                    }

                    $value = implode(', ', $optionsLabels);

                    break;

                case 'email':
                case 'phone':
                    if (! is_array($entity->{$attribute->code})) {
                        break;
                    }

                    $optionsLabels = [];

                    foreach ($entity->{$attribute->code} as $item) {
                        $optionsLabels[] = $item['value'] . ' (' . $item['label'] . ')';
                    }

                    $value = implode(', ', $optionsLabels);

                    break;

                case 'address':
                    if (! $entity->{$attribute->code} || ! count(array_filter($entity->{$attribute->code}))) {
                        break;
                    }

                    $value = $entity->{$attribute->code}['address']. "<br>"
                             . $entity->{$attribute->code}['postcode'] . '  ' . $entity->{$attribute->code}['city'] . "<br>"
                             . core()->state_name($entity->{$attribute->code}['state']) . "<br>"
                             . core()->country_name($entity->{$attribute->code}['country']) . "<br>";

                    break;
                
                default:
                    $value = $entity->{$attribute->code};

                    break;
            }

            $content = strtr($content, [
                '{%' . $this->entityType . '.' . $attribute->code . '%}'   => $value,
                '{% ' . $this->entityType . '.' . $attribute->code . ' %}' => $value,
            ]);
        }

        return $content;
    }

    abstract public function getActions();

    abstract public function executeActions($workflow, $entity);
}