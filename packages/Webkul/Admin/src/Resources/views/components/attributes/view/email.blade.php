<x-admin::form.control-group.controls.inline.email
    ::name="'{{ $attribute->code }}'"
    :value="$value"
    rules="required|decimal:4"
    position="left"
    :label="$label ?? $attribute->name"
    ::errors="errors"
    :placeholder="$label ?? $attribute->name"
    :url="$url"
    :allow-edit="$allowEdit"
/>
