@extends('admin::layouts.master')

@section('page_title')
    {{ __('admin::app.leads.title') }}
@stop

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
