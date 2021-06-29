@extends('ui::datagrid.table')

@section('table-header')
    {{ __('admin::app.activities.title') }}
@stop

@php
    $tableClass = "\Webkul\Admin\DataGrids\Activity\ActivityDataGrid";
@endphp

@section('table-section')
    <table-component table-class="{{ $tableClass }}" tabs="true"><table-component>
@show