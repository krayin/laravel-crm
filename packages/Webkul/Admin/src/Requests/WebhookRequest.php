<?php

namespace Webkul\Admin\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class WebhookRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules()
    {
        return [
            'name'         => 'required|string|max:255',
            'entity_type'  => 'required|string|max:255',
            'description'  => 'nullable|string|max:255',
            'method'       => 'required|string|max:255',
            'end_point'    => 'required|string|max:255',
            'query_params' => 'nullable',
            'headers'      => 'nullable',
            'payload_type' => [
                'required',
                'string',
                'max:255',
                Rule::in(['default', 'x-www-form-urlencoded', 'raw']),
            ],
            'raw_payload_type' => [
                'string',
                'max:255',
                Rule::in(['json', 'text']),
            ],
            'payload' => 'nullable',
        ];
    }
}
