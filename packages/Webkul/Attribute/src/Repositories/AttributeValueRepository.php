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
     * @param int  $entityId
     * @return void
     */
    public function save(array $data, $entityId)
    {
        $conditions = ['entity_type' => $data['entity_type']];

        if (isset($data['quick_add'])) {
            $conditions['quick_add'] = 1;
        }
        
        $attributes = $this->attributeRepository->findWhere($conditions);

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

            if ($attribute->type === 'email' || $attribute->type === 'phone') {
                $data[$attribute->code] = $this->sanitizeEmailAndPhone($data[$attribute->code]);
            }

            if ($attribute->type === 'image' || $attribute->type === 'file') {
                $data[$attribute->code] = gettype($data[$attribute->code]) === 'object'
                    ? request()->file($attribute->code)->store($data['entity_type'] . '/' . $entityId)
                    : null;
            }

            $attributeValue = $this->findOneWhere([
                'entity_id'    => $entityId,
                'entity_type'  => $data['entity_type'],
                'attribute_id' => $attribute->id,
            ]);

            if (! $attributeValue) {
                $this->create([
                    'entity_id'    => $entityId,
                    'entity_type'  => $data['entity_type'],
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
        $query = $this->resetScope()->model
            ->where('attribute_id', $attribute->id)
            ->where('entity_type', $entityType)
            ->where('entity_id', '!=', $entityId);

        if (in_array($attribute->type, ['email', 'phone'])) {
            $query->where($this->model::$attributeTypeFields[$attribute->type], 'like', "%{$value}%");
        } else {
            $query->where($this->model::$attributeTypeFields[$attribute->type], $value);
        }

        return $query->get()->count() ? false : true;
    }

    /**
     * Removed null values from email and phone fields.
     * 
     * @param  array  $data
     * @return array
     */
    public function sanitizeEmailAndPhone($data)
    {
        foreach ($data as $key => $row) {
            if (is_null($row['value'])) {
                unset($data[$key]);
            }
        }

        return $data;
    }
}