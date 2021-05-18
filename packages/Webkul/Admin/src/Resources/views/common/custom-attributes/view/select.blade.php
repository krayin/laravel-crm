@php
    $option = $attribute->lookup_type
        ? app('Webkul\Attribute\Repositories\AttributeRepository')->getLookUpEntity($attribute->code, $value)
        : $attribute->options()->where('id', $value)->first();
@endphp

{{ $option ? $option->name : __('admin::app.common.not-available') }}