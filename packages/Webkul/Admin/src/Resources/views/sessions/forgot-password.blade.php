@extends('admin::layouts.anonymous-master')

@section('page_title')
    {{ __('admin::app.sessions.forgot-password.title') }}
@stop

@section('content')

    <div class="panel">
        <div class="panel-body">

            <div class="form-container">
                <h1>{{ __('admin::app.sessions.forgot-password.title') }}</h1>

                <form method="POST" action="{{ route('admin.forgot_password.store') }}" @submit.prevent="onSubmit">
                    {!! view_render_event('admin.sessions.forgot_password.form_controls.before') !!}

                    @csrf

                    <div class="form-group" :class="[errors.has('email') ? 'has-error' : '']">
                        <label for="email">{{ __('admin::app.sessions.forgot-password.email') }}</label>

                        <input
                            type="text"
                            name="email"
                            class="control"
                            id="email"
                            value="{{ old('email') }}"
                            v-validate="'required'"
                            data-vv-as="&quot;{{ __('admin::app.sessions.forgot-password.email') }}&quot;"
                        />

                        <span class="control-error" v-if="errors.has('email')">
                            @{{ errors.first('email') }}
                        </span>
                    </div>

                    <a href="{{ route('admin.session.create') }}">{{ __('admin::app.sessions.forgot-password.back-to-login') }}</a>

                    {!! view_render_event('admin.sessions.forgot_password.form_controls.after') !!}
                    
                    <div class="button-group">
                        {!! view_render_event('admin.sessions.forgot_password.form_buttons.before') !!}

                        <button class="btn btn-xl btn-primary">
                            {{ __('admin::app.sessions.forgot-password.send-reset-password-email') }}
                        </button>

                        {!! view_render_event('admin.sessions.forgot_password.form_buttons.after') !!}
                    </div>
                </form>
            </div>

        </div>
    </div>

@stop