<x-admin::form.control-group class="!mb-0">
    <x-admin::form.control-group.control
        type="phone"
        ::name="'{{ $attribute->code }}'"
        :value="$value"
        rules="required|decimal:4"
        position="left"
        @on-save="onChanged"
    />
</x-admin::form.control-group>