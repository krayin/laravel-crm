@php
    $user = auth()->guard('user')->user();

    $options = $attribute->lookup_type
        ? app('Webkul\Attribute\Repositories\AttributeRepository')->getLookUpOptions($attribute->lookup_type, [
            'query'           => '',
            'user_id'         => $user->id,
            'view_permission' => $user->view_permission,
        ])
        : $attribute->options()->orderBy('sort_order')->get();

    $selectedOption = old($attribute->code) ?: $value;
@endphp

<x-admin::form.control-group.controls.inline.multiselect
    ::name="'{{ $attribute->code }}'"
    ::value="'{{ $selectedOption }}'"
    :data="$options"
    rules="required"
    position="left"
    :label="$attribute->name"
    ::errors="errors"
    :placeholder="$attribute->name"
    :url="$url"
    :allow-edit="$allowEdit"
/>
