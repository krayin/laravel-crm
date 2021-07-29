@extends('ui::datagrid.table')

@section('page_title')
    {{ __('admin::app.settings.roles.title') }}
@stop

@section('table-header')
    {!! view_render_event('admin.settings.roles.index.header.before') !!}

    {{ Breadcrumbs::render('settings.roles') }}

    {{ __('admin::app.settings.roles.title') }}

    {!! view_render_event('admin.settings.roles.index.header.after') !!}
@stop

@section('table-action')
    <a href="{{ route('admin.settings.roles.create') }}" class="btn btn-md btn-primary">
        {{ __('admin::app.settings.roles.add-title') }}
    </a>
@stop
