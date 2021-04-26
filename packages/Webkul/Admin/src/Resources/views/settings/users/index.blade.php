@extends('ui::datagrid.table')

@section('table-header')
    {{ __('admin::app.settings.users.title') }}
@stop

@section('table-action')
    <a href="{{ route('admin.settings.users.create') }}" class="btn btn-sm btn-primary">
        {{ __('admin::app.settings.users.create_user') }}
    </a>
@stop