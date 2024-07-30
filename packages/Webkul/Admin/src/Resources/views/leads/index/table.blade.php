{!! view_render_event('admin.leads.index.table.before') !!}

<x-admin::datagrid src="{{ route('admin.leads.index') }}" />

{!! view_render_event('admin.leads.index.table.after') !!}