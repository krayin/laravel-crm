@php
    $isLeadValue = $attribute->entity_type === 'leads'
        && $attribute->code === 'lead_value';

    $displayLabel = $value;

    if ($isLeadValue && is_numeric($value)) {
        $displayLabel = number_format((float) $value, 0);
    }
@endphp

<x-admin::form.control-group.controls.inline.text
    type="inline"
    ::name="'{{ $attribute->code }}'"
    :value="$value ?? ''"
    :value-label="$value == '' ? '--' : $displayLabel"
    position="left"
    rules="required|{{ $attribute->validation }}"
    :label="$attribute->name"
    :placeholder="$attribute->name"
    ::errors="errors"
    :url="$url"
    :allow-edit="$allowEdit"
/>
