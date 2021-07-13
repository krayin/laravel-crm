@extends('ui::datagrid.table')

@section('page_title')
    {{ __('admin::app.activities.title') }}
@stop

@section('table-header')
    {!! view_render_event('admin.activities.index.header.before') !!}

    {{ Breadcrumbs::render('activities') }}

    {{ __('admin::app.activities.title') }}

    {!! view_render_event('admin.activities.index.header.before') !!}
@stop

@php
    $tableClass = "\Webkul\Admin\DataGrids\Activity\ActivityDataGrid";
@endphp