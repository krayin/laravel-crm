<?php

namespace Webkul\Admin\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Factory as ValidationFactory;

class PipelineForm extends FormRequest
{
    /**
     * Constructor.
     *
     * @param  \Illuminate\Validation\Factory  $validationFactory
     * @return void
     */
    public function __construct(ValidationFactory $validationFactory)
    {
        $validationFactory->extend(
            'unique_key',
            function ($attribute, $value, $parameters) {
                $key = last(explode('.', $attribute));

                $stages = collect(request()->get('stages'))->filter(function ($stage) use ($key, $value) {
                    return $stage[$key] === $value;
                });

                if ($stages->count() > 1) {
                    return false;
                }

                return true;
            }
        );
    }

    /**
     * Determine if the user is authorized to make this request.
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
        return [
            'name' => 'required',
            'stages.*.name' => 'unique_key',
            'stages.*.code' => 'unique_key',
        ];
    }
}
