<x-admin::form.control-group.controls.inline.address
    ::name="'{{ $attribute->code }}'"
    :value="$value"
    rules="required"
    position="left"
    :label="$attribute->name"
    ::errors="errors"
    :placeholder="$attribute->name"
    @on-change="onChanged"
/>
