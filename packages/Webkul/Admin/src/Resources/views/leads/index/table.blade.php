{!! view_render_event('admin.leads.index.table.before') !!}

<x-admin::datagrid :src="route('admin.leads.index')">
    <!-- DataGrid Shimmer -->
    <x-admin::shimmer.datagrid />

    {{--
        Column settings sits on the left, beside search, because that is where the kanban board puts
        its card settings. Rendering it on the right meant the control jumped across the toolbar
        when the user switched between the two views, even though it does the same job in both.

        The view switcher stays on the right in both views, which is where the kanban toolbar
        already places it.
    --}}
    <x-slot:toolbar-left-after>
        <x-admin::datagrid.column-settings />
    </x-slot>

    <x-slot:toolbar-right-after>
        @include('admin::leads.index.view-switcher')
    </x-slot>
</x-admin::datagrid>

{!! view_render_event('admin.leads.index.table.after') !!}