@extends('admin::layouts.master')

@section('page_title')
    {{ __('admin::app.mail.' . request('route')) }}
@stop

@section('content-wrapper')
    <div class="content full-page">
        <table-component data-src="{{ route('admin.mail.index') }}">
            <template v-slot:table-header>
                <h1>
                    {!! view_render_event('admin.mail.index.header.before') !!}

                    {{ Breadcrumbs::render('mail.route', request('route')) }}

                    {{ __('admin::app.mail.' . request('route')) }}

                    {!! view_render_event('admin.mail.index.header.after') !!}
                </h1>
            </template>
        <table-component>
    </div>
@stop
