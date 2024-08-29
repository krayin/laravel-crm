<x-admin::form.control-group.control
    type="inline"
    ::name="'{{ $attribute->code }}'"
    ::value="'{{ $value }}'"
    position="left"
    rules="required"
    :label="$attribute->name"
    :placeholder="$attribute->name"
    ::errors="errors"
    :url="$url"
    :allow-edit="$allowEdit"
/>
