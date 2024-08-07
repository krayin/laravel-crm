<x-admin::form.control-group.controls.inline.address
    ::name="'{{ $attribute->code }}'"
    :value="$value"
    rules="required"
    position="left"
    @on-change="onChanged"
/>
