<input
    type="text"
    name="{{ $attribute->code }}"
    class="control"
    id="{{ $attribute->code }}"
    value="{{ old($attribute->code) ?: $value }}"
    @if ($attribute->code == 'sku') v-validate="{{$validations}}" @else v-validate="'{{$validations}}'" @endif
    data-vv-as="&quot;{{ $attribute->name }}&quot;"
/>