@extends('admin::layouts.master')

@section('page_title')
    {{ __('admin::app.activities.title') }}
@stop

@section('content-wrapper')
    <div class="content full-page">
        <table-component data-src="{{ route('admin.activities.index') }}">
            <template v-slot:table-header>
                {!! view_render_event('admin.activities.index.header.before') !!}

                {{ Breadcrumbs::render('activities') }}

                {{ __('admin::app.activities.title') }}

                {!! view_render_event('admin.activities.index.header.before') !!}
            </template>
        <table-component>
    </div>
@stop
