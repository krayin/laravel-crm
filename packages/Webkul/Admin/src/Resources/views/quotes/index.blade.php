@extends('admin::layouts.master')

@section('page_title')
    {{ __('admin::app.products.title') }}
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
        <table-component>
    </div>
@stop
