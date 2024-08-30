<x-admin::form.control-group.controls.inline.email
    ::name="'{{ $attribute->code }}'"
    :value="$value"
    rules="required|decimal:4"
    position="left"
    :label="$attribute->name"
    ::errors="errors"
    :placeholder="$attribute->name"
    :url="$url"
    :allow-edit="$allowEdit"
/>
