<x-admin::form.control-group.controls.inline.date
    ::name="'{{ $attribute->code }}'"
    ::value="'{{ $value?->format('Y-m-d') }}'"
    rules="required"
    position="left"
    :label="$attribute->name"
    ::errors="errors"
    :placeholder="$attribute->name"
    :url="$url"
    :allow-edit="$allowEdit"
/>
