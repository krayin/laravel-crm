<x-admin::form.control-group.controls.inline.text
    type="inline"
    ::name="'{{ $attribute->code }}'"
    :value="$value ?? ''"
    :value-label="$value == '' ? '--' : $value"
    position="left"
    rules="required|{{ $attribute->validation }}"
    :label="$label ?? $attribute->name"
    :placeholder="$label ?? $attribute->name"
    ::errors="errors"
    :url="$url"
    :allow-edit="$allowEdit"
/>
