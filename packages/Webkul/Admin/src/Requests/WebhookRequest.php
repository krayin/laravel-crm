<?php

namespace Webkul\Admin\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Webkul\Automation\Services\WebhookService;

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
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules()
    {
        return [
            'name' => 'required|string|max:255',
            'entity_type' => 'required|string|max:255',
            'description' => 'nullable|string|max:255',
            'method' => 'required|string|max:255',
            'end_point' => [
                'required',
                'string',
                'max:255',
                fn ($attribute, $value, $fail) => $this->validateEndpoint($value, $fail),
            ],
            'query_params' => 'nullable',
            'headers' => 'nullable',
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

    /**
     * Reject webhook endpoints that could be used for Server-Side Request Forgery. Endpoints that
     * contain workflow placeholders (e.g. `{%leads.id%}`) are only resolved when the webhook fires,
     * so their final host is validated by the webhook service at trigger time instead.
     */
    protected function validateEndpoint(string $value, callable $fail): void
    {
        if (str_contains($value, '{%')) {
            return;
        }

        if (! app(WebhookService::class)->isAllowedEndpoint($value)) {
            $fail(trans('admin::app.settings.webhooks.index.invalid-endpoint'));
        }
    }
}
