@foreach ($customAttributes as $attribute)

    @php
        $validations = [];

        if ($attribute->is_required) {
            array_push($validations, 'required');
        }

        if ($attribute->type == 'price') {
            array_push($validations, 'decimal');
        }

        array_push($validations, $attribute->validation);

        $validations = implode('|', array_filter($validations));
    @endphp

    @if (view()->exists($typeView = 'admin::common.field-types.' . $attribute->type))

        <div class="control-group {{ $attribute->type }}"
            @if ($attribute->type == 'multiselect') :class="[errors.has('{{ $attribute->code }}[]') ? 'has-error' : '']"
            @else :class="[errors.has('{{ $attribute->code }}') ? 'has-error' : '']" @endif>

            <label for="{{ $attribute->code }}" {{ $attribute->is_required ? 'class=required' : '' }}>
                {{ $attribute->name }}

                @if ($attribute->type == 'price')
                    <span class="currency-code">($)</span>
                @endif

            </label>

            @include ($typeView, ['value' => isset($entity) ? $entity[$attribute->code] : null])

            <span class="control-error"
                @if ($attribute->type == 'multiselect') v-if="errors.has('{{ $attribute->code }}[]')"
                @else  v-if="errors.has('{{ $attribute->code }}')"  @endif>
                
                @if ($attribute->type == 'multiselect')
                    @{{ errors.first('{!! $attribute->code !!}[]') }}
                @else
                    @{{ errors.first('{!! $attribute->code !!}') }}
                @endif
            </span>
        </div>

    @endif

@endforeach