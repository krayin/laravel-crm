<?php
 
namespace Webkul\Attribute\Traits;

use Webkul\Attribute\Repositories\AttributeRepository;
 
trait CustomAttribute {

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
     * Get an attribute from the model.
     *
     * @param string $key
     *
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
                if (in_array($attribute->code, $hiddenAttributes)) {
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

        $attributeValue = $this->attribute_values()->where('attribute_id', $attribute->id)->first();

        return $attributeValue[$this->attributeTypeFields[$attribute->type]] ?? null;
    }
}