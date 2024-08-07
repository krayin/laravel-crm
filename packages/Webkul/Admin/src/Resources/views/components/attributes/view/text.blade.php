<x-admin::form.control-group class="!mb-0">
    <x-admin::form.control-group.control
        type="inline"
        ::name="'{{ $attribute->code }}'"
        ::value="'{{ $value }}'"
        position="left"
        @on-change="onChanged"
    />
</x-admin::form.control-group>
