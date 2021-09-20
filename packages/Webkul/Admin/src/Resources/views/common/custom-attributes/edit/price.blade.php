<input
    type="text"
    name="{{ $attribute->code }}"
    class="control"
    id="{{ $attribute->code }}"
    value="{{ old($attribute->code) ?: $value}}"
    v-validate="'{{$validations}}'"
    data-vv-as="&quot;{{ $attribute->name }}&quot;"
/>