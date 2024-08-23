<?php

namespace Webkul\Admin\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Webkul\Attribute\Repositories\AttributeRepository;
use Webkul\Attribute\Repositories\AttributeValueRepository;
use Webkul\Core\Contracts\Validations\Decimal;

class AttributeForm extends FormRequest
{
    /**
     * @var array
     */
    protected $rules = [];

    /**
     * Create a new form request instance.
     *
     * @return void
     */
    public function __construct(
        protected AttributeRepository $attributeRepository,
        protected AttributeValueRepository $attributeValueRepository
    ) {}

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
        $attributes = $this->attributeRepository->scopeQuery(function ($query) {
            $query = $query->whereIn('code', array_keys(request()->all()))
                ->where('entity_type', request('entity_type'));

            if (request()->has('quick_add')) {
                $query = $query->where('quick_add', 1);
            }

            return $query;
        })->get();

        foreach ($attributes as $attribute) {
            $validations = [];

            if ($attribute->type == 'boolean') {
                continue;
            } elseif ($attribute->type == 'address') {
                if (! $attribute->is_required) {
                    continue;
                }

                $validations = [
                    $attribute->code.'.address'  => 'required',
                    $attribute->code.'.country'  => 'required',
                    $attribute->code.'.state'    => 'required',
                    $attribute->code.'.city'     => 'required',
                    $attribute->code.'.postcode' => 'required',
                ];
            } elseif ($attribute->type == 'email') {
                $validations = [
                    $attribute->code              => [$attribute->is_required ? 'required' : 'nullable'],
                    $attribute->code.'.*.value'   => [$attribute->is_required ? 'required' : 'nullable', 'email'],
                    $attribute->code.'.*.label'   => $attribute->is_required ? 'required' : 'nullable',
                ];
            } elseif ($attribute->type == 'phone') {
                $validations = [
                    $attribute->code              => [$attribute->is_required ? 'required' : 'nullable'],
                    $attribute->code.'.*.value'   => [$attribute->is_required ? 'required' : 'nullable'],
                    $attribute->code.'.*.label'   => $attribute->is_required ? 'required' : 'nullable',
                ];
            } else {
                $validations[$attribute->code] = [$attribute->is_required ? 'required' : 'nullable'];

                if ($attribute->type == 'text' && $attribute->validation) {
                    array_push($validations[$attribute->code],
                        $attribute->validation == 'decimal'
                        ? new Decimal
                        : $attribute->validation
                    );
                }

                if ($attribute->type == 'price') {
                    array_push($validations[$attribute->code], new Decimal);
                }

                if ($attribute->type == 'image' && ! request($attribute->code.'.delete')) {
                    array_push($validations[$attribute->code], 'mimes:bmp,jpeg,jpg,png,webp');
                }
            }

            if ($attribute->is_unique) {
                array_push($validations[in_array($attribute->type, ['email', 'phone'])
                    ? $attribute->code.'.*.value'
                    : $attribute->code
                ], function ($field, $value, $fail) use ($attribute) {
                    if (! $this->attributeValueRepository->isValueUnique($this->id, $attribute->entity_type, $attribute, request($field))) {
                        $fail('The value has already been taken.');
                    }
                });
            }

            $this->rules = array_merge($this->rules, $validations);
        }

        return $this->rules;
    }
}
