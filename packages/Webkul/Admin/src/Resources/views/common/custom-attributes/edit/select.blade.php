<select v-validate="'{{$validations}}'" class="control" id="{{ $attribute->code }}" name="{{ $attribute->code }}" data-vv-as="&quot;{{ $attribute->name }}&quot;">

    @php
        $options = $attribute->lookup_type
            ? app('Webkul\Attribute\Repositories\AttributeRepository')->getLookUpOptions($attribute->lookup_type)
            : $attribute->options()->orderBy('sort_order')->get();

        $selectedOption = old($attribute->code) ?: $value;
    @endphp

    <option value="" selected="selected" disabled="disabled">{{ __('admin::app.settings.attributes.select') }}</option>

    @foreach ($options as $option)
        <option value="{{ $option->id }}" {{ $option->id == $selectedOption ? 'selected' : ''}}>
            {{ $option->name }}
        </option>
    @endforeach

</select>