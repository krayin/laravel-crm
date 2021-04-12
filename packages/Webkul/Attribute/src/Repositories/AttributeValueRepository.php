<?php

namespace Webkul\Attribute\Repositories;

use Illuminate\Container\Container;
use Illuminate\Support\Facades\Storage;
use Webkul\Core\Eloquent\Repository;

class AttributeValueRepository extends Repository
{
    /**
     * AttributeRepository object
     *
     * @var \Webkul\Attribute\Repositories\AttributeRepository
     */
    protected $attributeRepository;

    /**
     * Create a new repository instance.
     *
     * @param  \Webkul\Attribute\Repositories\AttributeRepository $attributeRepository
     * @param  \Illuminate\Container\Container  $container
     * @return void
     */
    public function __construct(
        AttributeRepository $attributeRepository,
        Container $container
    )
    {
        $this->attributeRepository = $attributeRepository;

        parent::__construct($container);
    }

    /**
     * Specify Model class name
     *
     * @return mixed
     */
    function model()
    {
        return 'Webkul\Attribute\Contracts\AttributeValue';
    }

    /**
     * @param array  $data
     * @param int    $entityId
     * @param string $entityType
     * @return void
     */
    public function save(array $data, $entityId, $entityType)
    {
        $attributes = $this->attributeRepository->findWhere([
            'entity_type' => request('entity_type'),
            'quick_add'   => request()->has('quick_add') ? 1 : 0,
        ]);

        foreach ($attributes as $attribute) {
            $typeColumn = $this->model::$attributeTypeFields[$attribute->type];
            
            if ($attribute->type === 'boolean') {
                $data[$attribute->code] = isset($data[$attribute->code]) && $data[$attribute->code] ? 1 : 0;
            }

            if (! isset($data[$attribute->code])) {
                continue;
            }

            if ($attribute->type === 'price'
                && isset($data[$attribute->code])
                && $data[$attribute->code] === ''
            ) {
                $data[$attribute->code] = null;
            }

            if ($attribute->type === 'date' && $data[$attribute->code] === '') {
                $data[$attribute->code] = null;
            }

            if ($attribute->type === 'multiselect' || $attribute->type === 'checkbox') {
                $data[$attribute->code] = implode(',', $data[$attribute->code]);
            }

            if ($attribute->type === 'image' || $attribute->type === 'file') {
                $data[$attribute->code] = gettype($data[$attribute->code]) === 'object'
                    ? request()->file($attribute->code)->store('entity/' . $entityId)
                    : null;
            }

            $attributeValue = $this->findOneWhere([
                'entity_id'    => $entityId,
                'entity_type'  => $entityType,
                'attribute_id' => $attribute->id,
            ]);

            if (! $attributeValue) {
                $this->create([
                    'entity_id'    => $entityId,
                    'entity_type'  => $entityType,
                    'attribute_id' => $attribute->id,
                    $typeColumn    => $data[$attribute->code],
                ]);
            } else {
                $this->update([
                    $typeColumn => $data[$attribute->code],
                ], $attributeValue->id);

                if ($attribute->type == 'image' || $attribute->type == 'file') {
                    Storage::delete($attributeValue->text_value);
                }
            }
        }
    }

    /**
     * @param  int  $entityId
     * @param  string  $entityType
     * @param  \Webkul\Attribute\Contracts\Attribute  $attribute
     * @param  string  $value
     * @return boolean
     */
    public function isValueUnique($entityId, $entityType, $attribute, $value)
    {
        $result = $this->resetScope()->model
            ->where($this->model::$attributeTypeFields[$attribute->type], $value)
            ->where('attribute_id', $attribute->id)
            ->where('entity_type', $entityType)
            ->where('entity_id', '!=', $entityId)->get();

        return $result->count() ? false : true;
    }
}