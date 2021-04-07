@extends('ui::datagrid.table')

@section('table-header')
    {{ __('admin::app.settings.roles.title') }}
@stop

@section('table-action')
    <a href="{{ route('admin.settings.roles.create') }}" class="btn btn-sm btn-primary">
        {{ __('admin::app.settings.roles.create_role') }}
    </a>
@stop
