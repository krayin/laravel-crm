<x-admin::form.control-group.controls.inline.address
    ::name="'{{ $attribute->code }}'"
    :value="$value"
    rules="required"
    position="left"
    :label="$label ?? $attribute->name"
    ::errors="errors"
    :placeholder="$label ?? $attribute->name"
    :url="$url"
    :allow-edit="$allowEdit"
/>
