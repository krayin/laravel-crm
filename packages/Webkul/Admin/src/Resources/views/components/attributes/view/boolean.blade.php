<x-admin::form.control-group.controls.inline.boolean
    ::name="'{{ $attribute->code }}'"
    :value="json_encode($value)"
    rules="required"
    position="left"
    :label="$attribute->name"
    ::errors="errors"
    :placeholder="$attribute->name"
    :url="$url"
    :allow-edit="$allowEdit"
/>
