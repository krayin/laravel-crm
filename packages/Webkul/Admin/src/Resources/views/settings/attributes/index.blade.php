@extends('ui::datagrid.table')

@section('page_title')
    {{ __('admin::app.settings.attributes.title') }}
@stop

@section('table-header')
    {{ Breadcrumbs::render('settings.attributes') }}

    {{ __('admin::app.settings.attributes.title') }}
@stop

@section('table-action')
    <a href="{{ route('admin.settings.attributes.create') }}" class="btn btn-sm btn-primary">
        {{ __('admin::app.settings.attributes.add-title') }}
    </a>
@stop
