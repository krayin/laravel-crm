@extends('admin::layouts.master')

@section('page_title')
    {{ __('admin::app.products.title') }}
@stop

@section('content-wrapper')
    <div class="content full-page">
        <table-component data-src="{{ route('admin.products.index') }}">
            <template v-slot:table-header>
                <h1>
                    {!! view_render_event('admin.products.index.header.before') !!}

                    {{ Breadcrumbs::render('products') }}

                    {{ __('admin::app.products.title') }}

                    {!! view_render_event('admin.products.index.header.after') !!}
                </h1>
            </template>

            @if (bouncer()->hasPermission('products.create'))
                <template v-slot:table-action>
                    <a href="{{ route('admin.products.create') }}" class="btn btn-md btn-primary">{{ __('admin::app.products.create-title') }}</a>
                </template>
            @endif
        <table-component>
    </div>
@stop
