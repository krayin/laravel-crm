<x-admin::form.control-group.controls.inline.datetime
    ::name="'{{ $attribute->code }}'"
    :value="$value"
    rules="required"
    position="left"
    @on-change="onChanged"
/>
