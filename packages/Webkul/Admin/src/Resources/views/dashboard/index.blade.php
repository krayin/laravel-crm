<x-admin::layouts>
<p>Dashboard</p>
<x-admin::form.control-group>
    <x-admin::form.control-group.label class="required">
        @lang('Price')
    </x-admin::form.control-group.label>

    <x-admin::form.control-group.control
        type="inline"
        id="price"
        name="price"
        value="$2502"
        position="right"
        @onChange="onPriceChange"
        @onCancelled="onPriceEditingCancelled"
    />

    <x-admin::form.control-group.error control-name="price" />
</x-admin::form.control-group>
</x-admin::layouts>