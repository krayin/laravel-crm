<textarea
    name="{{ $attribute->code }}"
    class="control"
    id="{{ $attribute->code }}"
    v-validate="'{{$validations}}'"
    data-vv-as="&quot;{{ $attribute->name }}&quot;"
>{{ old($attribute->code) ?: $value}}</textarea>