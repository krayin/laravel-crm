@php
    if (! empty($value)) {
        if ($value instanceof \Carbon\Carbon) {
            $value = $value->format('Y-m-d');
        } elseif (is_string($value)) {
            $value = \Carbon\Carbon::parse($value)->format('Y-m-d');
        }
    }
@endphp

<x-admin::form.control-group.control
    type="date"
    :id="$attribute->code"
    :name="$attribute->code"
    :value="$value"
    :rules="$validations.'|regex:^\d{4}-\d{2}-\d{2}$'"
    :label="$attribute->name"
/>
