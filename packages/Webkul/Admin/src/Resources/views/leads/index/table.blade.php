{!! view_render_event('admin.leads.index.table.before') !!}

<x-admin::datagrid :src="route('admin.leads.index')">
    <!-- DataGrid Shimmer -->
    <x-admin::shimmer.datagrid />

    <x-slot:toolbar-right-after>
        @include('admin::leads.index.view-switcher')

        <x-admin::datagrid.column-settings />
    </x-slot>
</x-admin::datagrid>

{!! view_render_event('admin.leads.index.table.after') !!}