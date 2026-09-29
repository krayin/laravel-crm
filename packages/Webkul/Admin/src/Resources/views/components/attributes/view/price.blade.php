<x-admin::form.control-group.controls.inline.text
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
    ::value-label="'{{ core()->formatBasePrice($value, 2) }}'"
    ::value-formatter="(value) => value === '' || value === null || Number.isNaN(Number(value)) ? '' : $admin.formatPrice(Number(value))"
/>
