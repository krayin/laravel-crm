@extends('admin::layouts.master')

@section('page_title')
    {{ __('admin::app.contacts.persons.title') }}
@stop

@section('content-wrapper')
    <div class="content full-page">
        <table-component data-src="{{ route('admin.contacts.persons.index') }}">
            <template v-slot:table-header>
                <h1>
                    {!! view_render_event('admin.contacts.persons.index.persons.before') !!}

                    {{ Breadcrumbs::render('contacts.persons') }}

                    {{ __('admin::app.contacts.persons.title') }}

                    {!! view_render_event('admin.contacts.persons.index.persons.after') !!}
                </h1>
            </template>

            @if (bouncer()->hasPermission('contacts.persons.create'))
                <template v-slot:table-action>
                    <a href="{{ route('admin.contacts.persons.create') }}" class="btn btn-md btn-primary">{{ __('admin::app.contacts.persons.create-title') }}</a>
                </template>
            @endif
        <table-component>
    </div>
@stop
