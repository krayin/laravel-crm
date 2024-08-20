<?php

namespace Webkul\Attribute\Traits;

use Webkul\Attribute\Models\AttributeValueProxy;
use Webkul\Attribute\Repositories\AttributeRepository;

trait CustomAttribute
{
    /**
     * @var array
     */
    public static $attributeTypeFields = [
        'text'        => 'text_value',
        'textarea'    => 'text_value',
        'price'       => 'float_value',
        'boolean'     => 'boolean_value',
        'select'      => 'integer_value',
        'multiselect' => 'text_value',
        'checkbox'    => 'text_value',
        'email'       => 'json_value',
        'address'     => 'json_value',
        'phone'       => 'json_value',
        'lookup'      => 'integer_value',
        'datetime'    => 'datetime_value',
        'date'        => 'date_value',
        'file'        => 'text_value',
        'image'       => 'text_value',
    ];

    /**
     * Get the attribute values that owns the entity.
     */
    public function attribute_values()
    {
        return $this->morphMany(AttributeValueProxy::modelClass(), 'entity');
    }

    /**
     * Get an attribute from the model.
     *
     * @param  string  $key
     * @return mixed
     */
    public function getAttribute($key)
    {
        if (! method_exists(static::class, $key) && ! isset($this->attributes[$key])) {
            if (isset($this->id)) {
                $this->attributes[$key] = '';

                $attribute = app(AttributeRepository::class)->getAttributeByCode($key);

                $this->attributes[$key] = $this->getCustomAttributeValue($attribute);

                return $this->getAttributeValue($key);
            }
        }

        return parent::getAttribute($key);
    }

    /**
     * @return array
     */
    public function attributesToArray()
    {
        $attributes = parent::attributesToArray();

        $hiddenAttributes = $this->getHidden();

        if (isset($this->id)) {
            $customAttributes = $this->getCustomAttributes();

            foreach ($customAttributes as $attribute) {
                if (in_array($attribute->code, $hiddenAttributes) && isset($this->attributes[$attribute->code])) {
                    continue;
                }

                $attributes[$attribute->code] = $this->getCustomAttributeValue($attribute);
            }
        }

        return $attributes;
    }

    /**
     * Check in loaded family attributes.
     *
     * @return object
     */
    public function getCustomAttributes()
    {
        static $attributes;

        if ($attributes) {
            return $attributes;
        }

        return $attributes = app(AttributeRepository::class)->where('entity_type', $this->getTable())->get();
    }

    /**
     * Get an product attribute value.
     *
     * @return mixed
     */
    public function getCustomAttributeValue($attribute)
    {
        if (! $attribute) {
            return;
        }

        $attributeValue = $this->attribute_values->where('attribute_id', $attribute->id)->first();

        return $attributeValue[self::$attributeTypeFields[$attribute->type]] ?? null;
    }

    /**
     * Create a new instance of the given model.
     *
     * @param  array  $attributes
     * @return Collection
     */
    public function getLookUpAttributes($attributes)
    {
        $attributes = app(AttributeRepository::class)->scopeQuery(function ($query) use ($attributes) {
            return $query->distinct()
                ->where('type', 'lookup')
                ->where('entity_type', request('entity_type'))
                ->whereIn('code', array_keys($attributes, '', false));
        })->get();

        return $attributes;
    }

    /**
     * Create a new instance of the given model.
     *
     * @param  array  $attributes
     * @param  bool  $exists
     * @return static
     */
    public function newInstance($attributes = [], $exists = false)
    {
        // $attributes = $this->getLookUpAttributes($attributes);

        // Play with data here

        return parent::newInstance($attributes, $exists);
    }

    /**
     * Fill the model with an array of attributes.
     *
     * @return $this
     *
     * @throws \Illuminate\Database\Eloquent\MassAssignmentException
     */
    public function fill(array $attributes)
    {
        // Play with data here

        return parent::fill($attributes);
    }

    // Delete model's attribute values
    public static function boot()
    {
        parent::boot();

        static::deleting(function ($entity) {
            $entity->attribute_values()->delete();
        });
    }
}
