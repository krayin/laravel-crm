@extends('ui::datagrid.table')

@section('page_title')
    {{ __('admin::app.settings.attributes.title') }}
@stop

@section('table-header')
    {!! view_render_event('admin.settings.attributes.index.header.before') !!}

    {{ Breadcrumbs::render('settings.attributes') }}

    {{ __('admin::app.settings.attributes.title') }}

    {!! view_render_event('admin.settings.attributes.index.header.after') !!}
@stop

@section('table-action')
    <a href="{{ route('admin.settings.attributes.create') }}" class="btn btn-md btn-primary">
        {{ __('admin::app.settings.attributes.create-title') }}
    </a>
@stop
