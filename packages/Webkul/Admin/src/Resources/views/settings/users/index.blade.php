@extends('ui::datagrid.table')

@section('page_title')
    {{ __('admin::app.settings.users.title') }}
@stop

@section('table-header')
    {{ Breadcrumbs::render('settings.users') }}

    {{ __('admin::app.settings.users.title') }}
@stop

@section('table-action')
    <a href="{{ route('admin.settings.users.create') }}" class="btn btn-sm btn-primary">
        {{ __('admin::app.settings.users.add-title') }}
    </a>
@stop