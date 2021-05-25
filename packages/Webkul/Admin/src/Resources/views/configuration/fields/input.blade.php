<input
    class="control"
    id="{{ $fieldName }}"
    name="{{ $fieldName }}"
    type="{{ $field['type'] }}"
    v-validate="'{{ $validations }}'"
    data-vv-as="&quot;{{ trans($field['title']) }}&quot;"
    value="{{ old($name) ?: core()->getConfigData($name) }}"
/>