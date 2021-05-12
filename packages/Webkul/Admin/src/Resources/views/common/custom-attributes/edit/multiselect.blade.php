<select v-validate="'{{$validations}}'" class="control" id="{{ $attribute->code }}" name="{{ $attribute->code }}[]" data-vv-as="&quot;{{ $attribute->name }}&quot;" multiple>

    @php
        $options = $attribute->lookup_type
            ? app('Webkul\Attribute\Repositories\AttributeRepository')->getLookUpOptions($attribute->id)
            : $attribute->options()->orderBy('sort_order')->get();

        $selectedOption = old($attribute->code) ?: $value;
    @endphp

    <option value=""></option>

    @foreach ($options as $option)
        <option value="{{ $option->id }}" {{ in_array($option->id, explode(',', $selectedOption)) ? 'selected' : ''}}>
            {{ $option->name }}
        </option>
    @endforeach

</select>
