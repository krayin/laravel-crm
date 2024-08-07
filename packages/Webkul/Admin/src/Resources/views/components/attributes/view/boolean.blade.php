<x-admin::form.control-group.controls.inline.boolean
    ::name="'{{ $attribute->code }}'"
    :value="json_encode($value)"
    rules="required"
    position="left"
    @on-change="onChanged"
/>
