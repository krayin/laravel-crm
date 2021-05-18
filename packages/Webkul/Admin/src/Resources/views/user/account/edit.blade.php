@extends('admin::layouts.master')

@section('page_title')
    {{ __('admin::app.user.account.my_account') }}
@stop

@section('css')
    <style>
        .panel-header,
        .panel-body {
            margin: 0 auto;
            max-width: 800px;
        }
    </style>
@stop

@section('content-wrapper')
    <div class="content full-page adjacent-center">

        <form method="POST" action="{{ route('admin.user.account.update') }}" @submit.prevent="onSubmit">
            <div class="page-content">
                <div class="form-container">

                    <div class="panel">
                        <div class="panel-header">
                            <button type="submit" class="btn btn-md btn-primary">
                                {{ __('admin::app.user.account.update_details') }}
                            </button>

                            <a href="{{ route('admin.dashboard.index') }}">{{ __('admin::app.common.back') }}</a>
                        </div>
        
                        <div class="panel-body">
                            @csrf()

                            <input name="_method" type="hidden" value="PUT">
                
                            <div class="form-group" :class="[errors.has('name') ? 'has-error' : '']">
                                <label for="name" class="required">
                                    {{ __('admin::app.user.account.name') }}
                                </label>
                    
                                <input
                                    id="name"
                                    name="name"
                                    type="text"
                                    class="control"
                                    v-validate="'required'"
                                    value="{{ old('name') ?: $user->name }}"
                                    data-vv-as="{{ __('admin::app.user.account.name') }}"
                                />

                                <span class="control-error" v-if="errors.has('name')">
                                    @{{ errors.first('name') }}
                                </span>
                            </div>

                            <div class="form-group" :class="[errors.has('email') ? 'has-error' : '']">
                                <label for="email" class="required">
                                    {{ __('admin::app.user.account.email') }}
                                </label>
                    
                                <input
                                    id="email"
                                    name="email"
                                    type="text"
                                    class="control"
                                    v-validate="'required'"
                                    value="{{ old('email') ?: $user->email }}"
                                    data-vv-as="{{ __('admin::app.user.account.email') }}"
                                />

                                <span class="control-error" v-if="errors.has('email')">
                                    @{{ errors.first('email') }}
                                </span>
                            </div>

                            <div class="form-group" :class="[errors.has('current_password') ? 'has-error' : '']">
                                <label for="current_password" class="required">
                                    {{ __('admin::app.user.account.current_password') }}
                                </label>
                    
                                <input
                                    type="password"
                                    class="control"
                                    id="current_password"
                                    v-validate="'required'"
                                    name="current_password"
                                    data-vv-as="{{ __('admin::app.user.account.current_password') }}"
                                />

                                <span class="control-error" v-if="errors.has('current_password')">
                                    @{{ errors.first('current_password') }}
                                </span>
                            </div>

                            <div class="form-group" :class="[errors.has('password') ? 'has-error' : '']">
                                <label for="password">
                                    {{ __('admin::app.user.account.password') }}
                                </label>
                    
                                <input
                                    type="password"
                                    id="password"
                                    name="password"
                                    class="control"
                                    ref="password"
                                    data-vv-as="{{ __('admin::app.user.account.password') }}"
                                />

                                <span class="control-error" v-if="errors.has('password')">
                                    @{{ errors.first('password') }}
                                </span>
                            </div>

                            <div class="form-group" :class="[errors.has('confirm_password') ? 'has-error' : '']">
                                <label for="confirm_password">
                                    {{ __('admin::app.user.account.confirm_password') }}
                                </label>
                    
                                <input
                                    type="password"
                                    class="control"
                                    id="confirm_password"
                                    name="password_confirmation"
                                    v-validate="'confirmed:password'"
                                    data-vv-as="{{ __('admin::app.user.account.confirm_password') }}"
                                />

                                <span class="control-error" v-if="errors.has('confirm_password')">
                                    @{{ errors.first('confirm_password') }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
@stop