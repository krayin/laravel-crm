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

    @if (view()->exists($typeView = 'admin::common.custom-attributes.edit.' . $attribute->type))

        <div
            class="form-group {{ $attribute->type }}"
            @if ($attribute->type == 'multiselect') :class="[errors.has('{{ $formScope . $attribute->code }}[]') ? 'has-error' : '']"
            @else :class="[errors.has('{{ $formScope . $attribute->code }}') ? 'has-error' : '']" @endif
        >

            <label for="{{ $attribute->code }}" {{ $attribute->is_required ? 'class=required' : '' }}>
                {{ $attribute->name }}

                @if ($attribute->type == 'price')
                    <span class="currency-code">({{ core()->currencySymbol(config('app.currency')) }})</span>
                @endif

            </label>

            @include ($typeView, ['value' => isset($entity) ? $entity[$attribute->code] : null])

            <span
                class="control-error"
                @if ($attribute->type == 'multiselect') v-if="errors.has('{{ $formScope . $attribute->code }}[]')"
                @else  v-if="errors.has('{{ $formScope . $attribute->code }}')"  @endif
            >
                
                @if ($attribute->type == 'multiselect')
                    @{{ errors.first('{!! $formScope . $attribute->code !!}[]') }}
                @else
                    @{{ errors.first('{!! $formScope . $attribute->code !!}') }}
                @endif
            </span>
        </div>

    @endif

@endforeach