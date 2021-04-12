<?php

namespace Webkul\Attribute\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Webkul\Core\Contracts\Validations\Decimal;
use Webkul\Attribute\Repositories\AttributeRepository;
use Webkul\Attribute\Repositories\AttributeValueRepository;

class AttributeForm extends FormRequest
{
    /**
     * AttributeRepository object
     *
     * @var \Webkul\Attribute\Repositories\AttributeRepository
     */
    protected $attributeRepository;

    /**
     * AttributeValueRepository object
     *
     * @var \Webkul\Attribute\Repositories\AttributeValueRepository
     */
    protected $attributeValueRepository;

    /**
     * @var array
     */
    protected $rules = [];

    /**
     * Create a new form request instance.
     *
     * @param  \Webkul\Attribute\Repositories\AttributeRepository $attributeRepository
     * @param  \Webkul\Attribute\Repositories\AttributeValueRepository $attributeValueRepository
     * @return void
     */
    public function __construct(
        AttributeRepository $attributeRepository,
        AttributeValueRepository $attributeValueRepository
    )
    {
        $this->attributeRepository = $attributeRepository;

        $this->attributeValueRepository = $attributeValueRepository;
    }

    /**
     * Determine if the product is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $attributes = $this->attributeRepository->findWhere([
            'entity_type' => request('entity_type'),
            'quick_add'   => request()->has('quick_add') ? 1 : 0,
        ]);

        foreach ($attributes as $attribute) {
            if ($attribute->type == 'boolean') {
                continue;
            }

            $validations = [];

            if (! isset($this->rules[$attribute->code])) {
                array_push($validations, $attribute->is_required ? 'required' : 'nullable');
            } else {
                $validations = $this->rules[$attribute->code];
            }

            if ($attribute->type == 'text' && $attribute->validation) {
                array_push($validations,
                    $attribute->validation == 'decimal'
                    ? new Decimal
                    : $attribute->validation
                );
            }

            if ($attribute->type == 'price') {
                array_push($validations, new Decimal);
            }

            if ($attribute->is_unique) {
                array_push($validations, function ($field, $value, $fail) use ($attribute) {
                    if (! $this->attributeValueRepository->isValueUnique($this->id, request('entity_type'), $attribute, request($attribute->code))) {
                        $fail('The :attribute has already been taken.');
                    }
                });
            }

            $this->rules[$attribute->code] = $validations;
        }

        return $this->rules;
    }
}
