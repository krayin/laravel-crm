<?php

namespace Webkul\Admin\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Arr;

class ConfigurationForm extends FormRequest
{
    /**
     * Determine if the Configuration is authorized to make this request.
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
     * The validation for each field is always resolved from the trusted server-side configuration
     * definition, never from the client supplied `keys[]` payload. This prevents an attacker from
     * relaxing an upload rule (for example to `nullable`) to bypass the allowed file types.
     *
     * @return array
     */
    public function rules()
    {
        $rules = [];

        /**
         * Resolve the rules for the submitted fields from the registered configuration.
         */
        foreach (request()->input('keys', []) as $item) {
            $data = json_decode($item, true);

            foreach ($data['fields'] ?? [] as $field) {
                $key = $data['key'].'.'.$field['name'];

                if ($this->has($key.'.delete')) {
                    continue;
                }

                $rules[$key] = $this->resolveValidation($key);
            }
        }

        /**
         * Every uploaded file must also be validated against its trusted rule, even when the client
         * omits the field from `keys[]`. Files for keys that are not registered configuration fields
         * are rejected outright.
         */
        foreach (array_keys(Arr::dot(request()->allFiles())) as $key) {
            $configField = core()->getConfigField($key);

            $rules[$key] = $configField['validation'] ?? 'prohibited';
        }

        return $rules;
    }

    /**
     * Resolve the validation rule for a configuration field from its registered definition.
     */
    protected function resolveValidation(string $key): string
    {
        $configField = core()->getConfigField($key);

        if ($configField === null) {
            return $this->hasFile($key) ? 'prohibited' : 'nullable';
        }

        return $configField['validation'] ?? 'nullable';
    }
}
