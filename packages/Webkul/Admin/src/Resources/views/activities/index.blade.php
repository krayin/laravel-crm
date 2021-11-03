@extends('admin::layouts.master')

@section('page_title')
    {{ __('admin::app.activities.title') }}
@stop

@section('content-wrapper')
    @php
        $viewType = request()->view_type ?? "table";
    @endphp

    @if ($viewType == "table")
        {!! view_render_event('admin.activities.index.table.before') !!}

        @include('admin::activities.index.table')

        {!! view_render_event('admin.activities.index.table.after') !!}
    @else
        {!! view_render_event('admin.activities.index.calendar.before') !!}

        @include('admin::activities.index.calendar')

        {!! view_render_event('admin.activities.index.calendar.after') !!}
    @endif
@stop
