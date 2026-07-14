<x-admin::form.control-group.controls.inline.text
    type="inline"
    ::name="'{{ $attribute->code }}'"
    :value="$value"
    position="left"
    rules="required"
    :label="$label ?? $attribute->name"
    :placeholder="$label ?? $attribute->name"
    ::errors="errors"
    :url="$url"
    :allow-edit="$allowEdit"
/>
