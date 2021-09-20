<input
    type="text"
    name="{{ $fieldName }}"
    class="control"
    id="{{ $fieldName }}"
    value="{{ old($name) ?: (core()->getConfigData($name) ? core()->getConfigData($name) : (isset($field['default_value']) ? $field['default_value'] : '')) }}"
    v-validate="'{{ $validations }}'"
    data-vv-as="&quot;{{ trans($field['title']) }}&quot;"
/>