<textarea
    class="control"
    id="{{ $fieldName }}"
    name="{{ $fieldName }}"
    v-validate="'{{ $validations }}'"
    data-vv-as="&quot;{{ trans($field['title']) }}&quot;"
>
    {{ old($name) ?: core()->getConfigData($name) }}
</textarea>