@extends('admin::layouts.master')

@section('page_title')
    {{ __('admin::app.leads.title') }}
@stop

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

@section('content-wrapper')
    @php
        $viewType = request()->view_type ?? "kanban";
    @endphp

    @if ($viewType == "table")
        {!! view_render_event('admin.leads.index.table.before') !!}

        @include('admin::leads.index.table')

        {!! view_render_event('admin.leads.index.table.after') !!}
    @else
        {!! view_render_event('admin.leads.index.kanban.before') !!}

        @include('admin::leads.index.kanban')

        {!! view_render_event('admin.leads.index.kanban.after') !!}
    @endif
@stop
