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
    <div class="content full-page">
        @php
            $viewType = request()->view_type ?? "kanban";
        @endphp

        @if ($viewType == "table")
            {!! view_render_event('admin.leads.index.list.table.before') !!}

                @include('admin::leads.list.table')

            {!! view_render_event('admin.leads.index.list.table.after') !!}
        @else
            {!! view_render_event('admin.leads.index.list.kanban.before') !!}

                @include('admin::leads.list.kanban')

            {!! view_render_event('admin.leads.index.list.kanban.after') !!}
        @endif
    </div>
@stop
