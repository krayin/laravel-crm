<select v-validate="'{{$validations}}'" class="control" id="{{ $attribute->code }}" name="{{ $attribute->code }}[]" data-vv-as="&quot;{{ $attribute->name }}&quot;" multiple>

    @foreach ($attribute->options()->orderBy('sort_order')->get() as $option)
        <option value="{{ $option->id }}" {{ in_array($option->id, explode(',', $value)) ? 'selected' : ''}}>
            {{ $option->name }}
        </option>
    @endforeach

</select>
