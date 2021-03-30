@extends('ui::datagrid.table')

@php
    $result = app('\Webkul\Admin\DataGrids\AdminDataGrid')->data();
@endphp

@section('table-header')
    Leads
@stop
