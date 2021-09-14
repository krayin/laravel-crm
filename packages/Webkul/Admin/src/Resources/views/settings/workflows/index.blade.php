@extends('ui::datagrid.table')

@section('page_title')
    {{ __('admin::app.settings.workflows.title') }}
@stop

@section('table-header')
    {!! view_render_event('admin.settings.workflows.index.header.before') !!}

    {{ Breadcrumbs::render('settings.workflows') }}

    {{ __('admin::app.settings.workflows.title') }}

    {!! view_render_event('admin.settings.workflows.index.header.after') !!}
@stop

@section('table-action')
    <a href="{{ route('admin.settings.workflows.create') }}" class="btn btn-md btn-primary">{{ __('admin::app.settings.workflows.add-title') }}</a>
@stop