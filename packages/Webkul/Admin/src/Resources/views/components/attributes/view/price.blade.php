<x-admin::form.control-group class="!mb-0">
    <x-admin::form.control-group.control
        type="inline"
        :name="$attribute->name"
        :value="$value"
        rules="required|decimal:4"
        position="left"
        :label="$attribute->name"
        ::errors="errors"
        :placeholder="$attribute->name"
    />
</x-admin::form.control-group>