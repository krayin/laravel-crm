@php
    $option = $attribute->lookup_type
        ? app('Webkul\Attribute\Repositories\AttributeRepository')->getLookUpEntity($attribute->lookup_type, $value)
        : $attribute->options()->where('id', $value)->first();
@endphp

<x-admin::form.control-group.controls.inline.select
    ::name="'{{ $attribute->code }}'"
    :value="$value"
    :options="$attribute->options()->get()->toArray()"
    rules="required"
    position="left"
    @on-change="onChanged"
/>
