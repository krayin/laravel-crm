@extends('ui::datagrid.table')

@section('page_title')
    {{ __('admin::app.settings.users.title') }}
@stop

@section('table-header')
    {!! view_render_event('admin.settings.users.index.header.before') !!}

    {{ Breadcrumbs::render('settings.users') }}

    {{ __('admin::app.settings.users.title') }}

    {!! view_render_event('admin.settings.users.index.header.after') !!}

@stop

@section('table-action')
    <a href="{{ route('admin.settings.users.create') }}" class="btn btn-md btn-primary">
        {{ __('admin::app.settings.users.create-title') }}
    </a>
@stop