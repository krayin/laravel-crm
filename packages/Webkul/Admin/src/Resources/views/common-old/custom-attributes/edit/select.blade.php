@php
    $options = $attribute->lookup_type
        ? app('Webkul\Attribute\Repositories\AttributeRepository')->getLookUpOptions($attribute->lookup_type)
        : $attribute->options()->orderBy('sort_order')->get();

    $selectedOption = old($attribute->code) ?: $value;
@endphp

<x-admin::form.control-group>
    <x-admin::form.control-group.control
        type="select"
        :id="$attribute->code"
        :name="$attribute->code"
        :rules="$validations"
        :label="$attribute->name"
    >
        <option value="" selected="selected" disabled="disabled">@lang('admin::app.common.custom-attributes.select')</option>

        @foreach ($options as $option)
            <option value="{{ $option->id }}" {{ $option->id == $selectedOption ? 'selected' : ''}}>
                {{ $option->name }}
            </option>
        @endforeach
    </x-admin::form.control-group.control>
</x-admin::form.control-group>
