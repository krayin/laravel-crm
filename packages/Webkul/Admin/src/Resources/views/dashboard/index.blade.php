<x-admin::layouts>
<p>Dashboard</p>
    <x-admin::lookup 
        end-point="{{ route('admin.settings.attributes.lookup') }}/persons"
        name="organizations"
        placeholder="Search Organizations"
    />
</x-admin::layouts>