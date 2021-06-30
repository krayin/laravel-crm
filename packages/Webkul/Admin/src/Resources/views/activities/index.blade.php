@extends('ui::datagrid.table')

@section('page_title')
    {{ __('admin::app.activities.title') }}
@stop

@section('table-header')
    {{ Breadcrumbs::render('activities') }}

    {{ __('admin::app.activities.title') }}
@stop

@php
    $tableClass = "\Webkul\Admin\DataGrids\Activity\ActivityDataGrid";
@endphp