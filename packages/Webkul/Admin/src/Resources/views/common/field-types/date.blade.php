<date>
    <input type="text" name="{{ $attribute->code }}" class="control" v-validate="{{ $attribute->is_required ? 'required' : '' }}" value="{{ old($attribute->code) ?: $value }}" data-vv-as="&quot;{{ $attribute->name }}&quot;"/>
</date>