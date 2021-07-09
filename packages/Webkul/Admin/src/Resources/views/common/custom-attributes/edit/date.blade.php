<date>
    <input type="text" name="{{ $attribute->code }}" class="control" v-validate="'{{$validations}}'" value="{{ old($attribute->code) ?: $value }}" data-vv-as="&quot;{{ $attribute->name }}&quot;"/>
</date>