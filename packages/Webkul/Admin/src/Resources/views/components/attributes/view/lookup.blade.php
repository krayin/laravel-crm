@php
    $lookUpEntity = app('Webkul\Attribute\Repositories\AttributeRepository')->getLookUpEntity($attribute->lookup_type, $value);
@endphp

<x-admin::form.control-group class="!mb-0">
    <x-admin::form.control-group.control
        type="lookup"
        ::name="'{{ $attribute->code }}'"
        :value="$lookUpEntity->name"
        :attribute="$attribute"
        position="left"
        @on-change="onChanged"
    />
</x-admin::form.control-group>
