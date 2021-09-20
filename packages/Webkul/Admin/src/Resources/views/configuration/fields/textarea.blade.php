<textarea
    name="{{ $fieldName }}"
    class="control"
    id="{{ $fieldName }}"
    v-validate="'{{ $validations }}'"
    data-vv-as="&quot;{{ trans($field['title']) }}&quot;"
>{{ old($name) ?: core()->getConfigData($name) }}</textarea>