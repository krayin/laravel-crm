@php
    if (! empty($value)) {
        if ($value instanceof \Carbon\Carbon) {
            $value = $value->format('Y-m-d');
        } elseif (is_string($value)) {
            $value = \Carbon\Carbon::parse($value)->format('Y-m-d');
        }
    }
@endphp

<x-admin::form.control-group.controls.inline.date
    ::name="'{{ $attribute->code }}'"
    ::value="'{{ $value }}'"
    rules="required"
    position="left"
    :label="$attribute->name"
    ::errors="errors"
    :placeholder="$attribute->name"
    :url="$url"
    :allow-edit="$allowEdit"
/>