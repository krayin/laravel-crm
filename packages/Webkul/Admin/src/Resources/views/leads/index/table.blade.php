{!! view_render_event('admin.leads.index.table.before') !!}

<x-admin::datagrid src="{{ route('admin.leads.index') }}">
    <x-slot:toolbar-right-before>
        @include('admin::leads.index.view-switcher')
    </x-slot>
</x-admin::datagrid>

{!! view_render_event('admin.leads.index.table.after') !!}