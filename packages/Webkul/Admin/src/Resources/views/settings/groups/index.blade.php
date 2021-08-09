@extends('ui::datagrid.table')

@section('page_title')
    {{ __('admin::app.settings.groups.title') }}
@stop

@section('table-header')
    {!! view_render_event('admin.settings.groups.index.header.before') !!}

    {{ Breadcrumbs::render('settings.groups') }}

    {{ __('admin::app.settings.groups.title') }}

    {!! view_render_event('admin.settings.groups.index.header.after') !!}
@stop

@section('table-action')
    <a href="{{ route('admin.settings.groups.create') }}" class="btn btn-md btn-primary">
        {{ __('admin::app.settings.groups.add-title') }}
    </a>
@stop
