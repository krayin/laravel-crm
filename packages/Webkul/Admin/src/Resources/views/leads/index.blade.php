@extends('ui::datagrid.table')

@section('table-header')
    {{ __('admin::app.leads.title') }}
@stop

@php
    $tableClass = "\Webkul\Admin\DataGrids\Lead\LeadDataGrid";
@endphp

@section('css')
    <style>
        .modal-container .modal-header {
            border: 0;
        }

        .modal-container .modal-body {
            padding: 0;
        }
    </style>
@stop