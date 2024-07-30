@php
    $formScope = $formScope ?? '';
@endphp

@foreach ($customAttributes as $attribute)
    @php
        if (isset($customValidations[$attribute->code])) {
            $validations = implode('|', $customValidations[$attribute->code]);
        } else {
            $validations = [];

            if ($attribute->code != 'sku') {
                if ($attribute->is_required) {
                    array_push($validations, 'required');
                }

                if ($attribute->type == 'price') {
                    array_push($validations, 'decimal');
                }

                array_push($validations, $attribute->validation);

                $validations = implode('|', array_filter($validations));
            } else {
                $validations = "{ ";

                if ($attribute->is_required) {
                    $validations .= "required: true, ";
                }

                $validations .= "regex: /^[a-zA-Z0-9]+(?:-[a-zA-Z0-9]+)*$/ }";
            }
        }
    @endphp
    
    @if (view()->exists($view = 'admin::common.custom-attributes.edit.' . $attribute->type))
        <x-admin::form.control-group class="mb-2.5 w-full">
            <x-admin::form.control-group.label
                for="{{ $attribute->code }}"
                :class="$attribute->is_required ? 'required' : ''"
            >
                {{ $attribute->name }}

                @if ($attribute->type == 'price')
                    <span class="currency-code">({{ core()->currencySymbol(config('app.currency')) }})</span>
                @endif
            </x-admin::form.control-group.label>

            @if (isset($attribute))
                @include ($view, [
                    'value' => isset($entity) ? $entity[$attribute->code] : null,
                ])
            @endif

            <x-admin::form.control-group.error :control-name="$attribute->code" />
        </x-admin::form.control-group>
    @endif
@endforeach