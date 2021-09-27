@extends('admin::layouts.master')

@section('page_title')
    {{ __('admin::app.quotes.title') }}
@stop

@section('content-wrapper')
    <div class="content full-page">
        <table-component data-src="{{ route('admin.quotes.index') }}">
            <template v-slot:table-header>
                <h1>
                    {!! view_render_event('admin.quotes.index.header.before') !!}

                    {{ Breadcrumbs::render('quotes') }}

                    {{ __('admin::app.quotes.title') }}

                    {!! view_render_event('admin.quotes.index.header.after') !!}
                </h1>
            </template>

            @if (bouncer()->hasPermission('quotes.create'))
                <template v-slot:table-action>
                    <a href="{{ route('admin.quotes.create') }}" class="btn btn-md btn-primary">{{ __('admin::app.quotes.create-title') }}</a>
                </template>
            @endif
        <table-component>
    </div>
@stop
