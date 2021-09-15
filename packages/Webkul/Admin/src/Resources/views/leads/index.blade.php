@extends('ui::datagrid.table')

@section('page_title')
    {{ __('admin::app.leads.title') }}
@stop

@section('table-header')
    {!! view_render_event('admin.leads.index.header.before') !!}

    {{ Breadcrumbs::render('leads') }}

    {{ __('admin::app.leads.title') }}

    {!! view_render_event('admin.leads.index.header.after') !!}
@stop

@php
    $viewType = request()->view_type ?? "kanban";
    
    $tableClass = "\Webkul\Admin\DataGrids\Lead\LeadDataGrid";
@endphp

@push('css')
    <style>
        .modal-container {
            overflow-y: hidden;
        }

        .modal-container .tabs-content {
            overflow-y: scroll;
            max-height: calc(100vh - 300px);
        }

        .modal-container .tabs-content .form-group:last-child {
            margin-bottom: 0;
        }

        .modal-container .modal-header {
            border: 0;
        }

        .modal-container .modal-body {
            padding: 0;
        }

        .modal-container .modal-body .add-more-link {
            display: block;
            padding: 5px 0 0 0;
        }
    </style>
@endpush

@if ($viewType == "table")
    {!! view_render_event('admin.leads.index.list.table.before') !!}

    @include('admin::leads.list.table')

    {!! view_render_event('admin.leads.index.list.table.after') !!}
@else
    @php($showDefaultTable = false)

    {!! view_render_event('admin.leads.index.list.kanban.before') !!}

    @include('admin::leads.list.kanban')

    {!! view_render_event('admin.leads.index.list.kanban.after') !!}
@endif