<input
    type="{{ $field['type'] }}"
    name="{{ $fieldName }}"
    class="control"
    id="{{ $fieldName }}"
    value="{{ old($name) ?: core()->getConfigData($name) }}"
    v-validate="'{{ $validations }}'"
    data-vv-as="&quot;{{ trans($field['title']) }}&quot;"
/>