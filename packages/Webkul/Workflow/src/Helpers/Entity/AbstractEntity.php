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
     * @param  array  $skipAttributes
     * @return array
     */
    public function getAttributes($entityType, $skipAttributes = ['textarea', 'image', 'file', 'address'])
    {
        $attributes = [];

        foreach ($this->attributeRepository->findByField('entity_type', $entityType) as $attribute) {
            if (in_array($attribute->type, $skipAttributes)) {
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
     * Returns placeholders for email templates
     * 
     * @param  array  $entity
     * @return array
     */
    public function getEmailTemplatePlaceholders($entity)
    {
        $menuItems = [];

        foreach ($this->getAttributes($this->entityType) as $attribute) {
            $menuItems[] = [
                'text'  => $attribute['name'],
                'value' => '{%' . $this->entityType . '.' . $attribute['id'] . '%}',
            ];
        }

        return [
            'text' => $entity['name'],
            'menu' => $menuItems,
        ];
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

                    $value = $option ? $option->name : __('admin::app.common.not-available');

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
                        $optionsLabels[] = $item['value'] . ' (' . $item['label'] . ')';
                    }

                    $value = implode(', ', $optionsLabels);

                    break;

                case 'address':
                    if (! $entity->{$attribute['id']} || ! count(array_filter($entity->{$attribute['id']}))) {
                        break;
                    }

                    $value = $entity->{$attribute['id']}['address']. "<br>"
                             . $entity->{$attribute['id']}['postcode'] . '  ' . $entity->{$attribute['id']}['city'] . "<br>"
                             . core()->state_name($entity->{$attribute['id']}['state']) . "<br>"
                             . core()->country_name($entity->{$attribute['id']}['country']) . "<br>";

                    break;
                
                case 'date':
                    if ($entity->{$attribute['id']}) {
                        $value = $entity->{$attribute['id']}->format("D M d, Y");
                    } else {
                        $value = 'N/A';
                    }

                    break;

                case 'datetime':
                    if ($entity->{$attribute['id']}) {
                        $value = $entity->{$attribute['id']}->format("D M d, Y H:i A");
                    } else {
                        $value = 'N/A';
                    }
                
                    break;

                default:
                    $value = $entity->{$attribute['id']};

                    break;
            }

            $content = strtr($content, [
                '{%' . $this->entityType . '.' . $attribute['id'] . '%}'   => $value,
                '{% ' . $this->entityType . '.' . $attribute['id'] . ' %}' => $value,
            ]);
        }

        return $content;
    }

    abstract public function getEntity($entity);

    abstract public function getActions();

    abstract public function executeActions($workflow, $entity);
}