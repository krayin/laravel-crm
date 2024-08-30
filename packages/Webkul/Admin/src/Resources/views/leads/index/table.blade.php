{!! view_render_event('krayin.admin.leads.index.table.before') !!}

<x-admin::datagrid src="{{ route('admin.leads.index') }}">
    <x-slot:toolbar-right-after>
        @include('admin::leads.index.view-switcher')
    </x-slot>
</x-admin::datagrid>

{!! view_render_event('krayin.admin.leads.index.table.after') !!}