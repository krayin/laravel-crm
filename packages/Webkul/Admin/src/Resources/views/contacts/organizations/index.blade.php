@extends('admin::layouts.master')

@section('page_title')
    {{ __('admin::app.contacts.organizations.title') }}
@stop

@section('content-wrapper')
    <div class="content full-page">
        <table-component data-src="{{ route('admin.contacts.organizations.index') }}">
            <template v-slot:table-header>
                <h1>
                    {!! view_render_event('admin.contacts.organizations.index.header.before') !!}

                    {{ Breadcrumbs::render('contacts.organizations') }}

                    {{ __('admin::app.contacts.organizations.title') }}

                    {!! view_render_event('admin.contacts.organizations.index.header.after') !!}
                </h1>
            </template>

            @if (bouncer()->hasPermission('contacts.organizations.create'))
                <template v-slot:table-action>
                    <a href="{{ route('admin.contacts.organizations.create') }}" class="btn btn-md btn-primary">{{ __('admin::app.contacts.organizations.create-title') }}</a>
                </template>
            @endif
        <table-component>
    </div>
@stop
