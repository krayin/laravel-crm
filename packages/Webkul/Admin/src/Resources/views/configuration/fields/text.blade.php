<input
    type="text"
    class="control"
    id="{{ $fieldName }}"
    name="{{ $fieldName }}"
    v-validate="'{{ $validations }}'"
    data-vv-as="&quot;{{ trans($field['title']) }}&quot;"
    value="{{ old($name) ?: (core()->getConfigData($name) ? core()->getConfigData($name) : (isset($field['default_value']) ? $field['default_value'] : '')) }}"
/>