<x-admin::form.control-group.control
    type="select"
    id="{{ $attribute->code }}"
    name="{{ $attribute->code }}"
    rules="{{ $validations }}"
    :label="$attribute->name"
    :placeholder="$attribute->name"
>
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
</x-admin::form.control-group.control>