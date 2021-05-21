@php
    $lookUpEntity = app('Webkul\Attribute\Repositories\AttributeRepository')->getLookUpEntity($attribute->code, $value);
@endphp

{{ $lookUpEntity ? $lookUpEntity->name : __('admin::app.common.not-available') }}