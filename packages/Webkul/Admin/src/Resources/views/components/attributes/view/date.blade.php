<x-admin::form.control-group.controls.inline.date
    ::name="'{{ $attribute->code }}'"
    :value="$value"
    rules="required"
    position="left"
    @on-change="onChanged"
/>
