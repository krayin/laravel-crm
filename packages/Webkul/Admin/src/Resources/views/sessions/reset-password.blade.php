@extends('admin::layouts.anonymous-master')

@section('page_title')
    {{ __('admin::app.sessions.reset-password.title') }}
@stop

@section('content')

    <div class="panel">
        <div class="panel-body">

            <div class="form-container">
                <h1>{{ __('admin::app.sessions.reset-password.title') }}</h1>

                <form method="POST" action="{{ route('admin.reset_password.store') }}" @submit.prevent="onSubmit">
                    {!! view_render_event('admin.sessions.reset_password.form_controls.before') !!}

                    @csrf

                    <input type="hidden" name="token" value="{{ $token }}">

                    <div class="form-group" :class="[errors.has('email') ? 'has-error' : '']">
                        <label for="email">{{ __('admin::app.sessions.reset-password.email') }}</label>

                        <input
                            type="text"
                            name="email"
                            class="control"
                            id="email"
                            value="{{ old('email') }}"
                            v-validate="'required|email'"
                            data-vv-as="&quot;{{ __('admin::app.sessions.reset-password.email') }}&quot;"
                        />

                        <span class="control-error" v-if="errors.has('email')">
                            @{{ errors.first('email') }}
                        </span>
                    </div>

                    <div class="form-group" :class="[errors.has('password') ? 'has-error' : '']">
                        <label for="password">{{ __('admin::app.sessions.reset-password.password') }}</label>

                        <input
                            type="password"
                            name="password"
                            class="control"
                            id="password"
                            ref="password"
                            v-validate="'required|min:6'"
                            data-vv-as="&quot;{{ __('admin::app.sessions.reset-password.password') }}&quot;"
                        />

                        <span class="control-error" v-if="errors.has('password')">
                            @{{ errors.first('password') }}
                        </span>
                    </div>

                    <div class="form-group" :class="[errors.has('password_confirmation') ? 'has-error' : '']">
                        <label for="password_confirmation">{{ __('admin::app.sessions.reset-password.confirm-password') }}</label>

                        <input
                            type="password"
                            name="password_confirmation"
                            class="control"
                            id="password_confirmation"
                            v-validate="'required|min:6|confirmed:password'"
                            data-vv-as="&quot;{{ __('admin::app.sessions.reset-password.confirm-password') }}&quot;"
                        />

                        <span class="control-error" v-if="errors.has('password_confirmation')">
                            @{{ errors.first('password_confirmation') }}
                        </span>
                    </div>

                    {!! view_render_event('admin.sessions.reset_password.form_controls.after') !!}
                    
                    <div class="button-group">
                        {!! view_render_event('admin.sessions.reset_password.form_buttons.before') !!}

                        <button type="submit" class="btn btn-xl btn-primary">
                            {{ __('admin::app.sessions.reset-password.reset-password') }}
                        </button>

                        {!! view_render_event('admin.sessions.reset_password.form_buttons.after') !!}
                    </div>
                </form>
            </div>

        </div>
    </div>

@stop