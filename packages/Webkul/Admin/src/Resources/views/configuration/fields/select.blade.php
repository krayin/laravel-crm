<select
    name="{{ $fieldName }}"
    class="control"
    id="{{ $fieldName }}"
    v-validate="'{{ $validations }}'"
    data-vv-as="&quot;{{ trans($field['title']) }}&quot;"
>
    @php
        $selectedOption = core()->getConfigData($name) ?? '';
    @endphp

    @if (isset($field['data_source']))
        @foreach ($value as $key => $option)

            <option value="{{ $key }}" {{ $key == $selectedOption ? 'selected' : ''}}>
                {{ trans($option) }}
            </option>

        @endforeach
    @else
        @foreach ($field['options'] as $option)
            @php
                if (! isset($option['value'])) {
                    $value = null;
                } else {
                    $value = $option['value'];

                    if (! $value) {
                        $value = 0;
                    }
                }
            @endphp

            <option value="{{ $value }}" {{ $value == $selectedOption ? 'selected' : ''}}>
                {{ trans($option['title']) }}
            </option>
        @endforeach
    @endif

</select>