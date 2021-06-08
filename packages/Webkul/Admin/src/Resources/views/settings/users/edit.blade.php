@extends('admin::layouts.master')

@section('page_title')
    {{ __('admin::app.settings.users.edit_user') }}
@stop

@section('content-wrapper')
    <div class="content full-page adjacent-center">
        <div class="page-header">
            <h6 class="breadcrumbs">
                Home/Leads/ 
            </h6>

            <div class="page-title">
                <h1>{{ __('admin::app.settings.users.edit_user') }}</h1>
            </div>
        </div>

        <form method="POST" action="{{ route('admin.settings.users.update', ['id' => $admin->id]) }}" @submit.prevent="onSubmit">
            <div class="page-content">
                <div class="form-container">
                    <div class="panel">
                        <div class="panel-header">
                            <button type="submit" class="btn btn-md btn-primary">
                                {{ __('admin::app.settings.users.update-btn-title') }}
                            </button>

                            <a href="{{ route('admin.settings.users.index') }}">
                                {{ __('admin::app.settings.users.back') }}
                            </a>
                        </div>

                        <div class="panel-body">
                            @csrf()
                            
                            <input name="_method" type="hidden" value="PUT">
                            
                            <div class="form-group" :class="[errors.has('name') ? 'has-error' : '']">
                                <label>
                                    {{ __('admin::app.settings.users.name') }}
                                </label>

                                <input
                                    type="text"
                                    name="name"
                                    class="control"
                                    v-validate="'required'"
                                    value="{{ $admin->name }}"
                                    data-vv-as="{{ __('admin::app.settings.users.name') }}"
                                    placeholder="{{ __('admin::app.settings.users.name') }}"
                                />

                                <span class="control-error" v-if="errors.has('name')">
                                    @{{ errors.first('name') }}
                                </span>
                            </div>

                            <div class="form-group" :class="[errors.has('email') ? 'has-error' : '']">
                                <label>
                                    {{ __('admin::app.settings.users.email') }}
                                </label>

                                <input
                                    type="email"
                                    name="email"
                                    class="control"
                                    value="{{ $admin->email }}"
                                    v-validate="'required|email'"
                                    data-vv-as="{{ __('admin::app.settings.users.email') }}"
                                    placeholder="{{ __('admin::app.settings.users.email') }}"
                                />

                                <span class="control-error" v-if="errors.has('email')">
                                    @{{ errors.first('email') }}
                                </span>
                            </div>

                            <div class="form-group" :class="[errors.has('status') ? 'has-error' : '']">
                                <label>
                                    {{ __('admin::app.settings.users.status') }}
                                </label>

                                <label class="switch">
                                    <input
                                        id="status"
                                        type="checkbox"
                                        name="status"
                                        class="control"
                                        {{ $admin->status ? 'checked' : '' }}
                                    />
                                    <span class="slider round"></span>
                                </label>
                            </div>

                            <div class="form-group" :class="[errors.has('role') ? 'has-error' : '']">
                                <label>
                                    {{ __('admin::app.settings.users.role') }}
                                </label>

                                <select name="role_id" class="control" data-vv-as="{{ __('admin::app.settings.users.role') }}" v-validate="'required'">
                                    @foreach ($roles as $role)
                                        <option value="{{ $role->id }}" {{ $admin->role_id == $role->id ? 'selected' : '' }}>
                                            {{ $role->name }}
                                        </option>
                                    @endforeach
                                </select>

                                <span class="control-error" v-if="errors.has('role_id')">
                                    @{{ errors.first('role_id') }}
                                </span>
                            </div>

                            <div class="form-group" :class="[errors.has('lead_view_permission') ? 'has-error' : '']">
                                <label>
                                    {{ __('admin::app.settings.users.lead-view') }}
                                </label>

                                <select name="lead_view_permission" class="control" v-validate="'required'" data-vv-as="{{ __('admin::app.settings.users.lead-view') }}">
                                    <option value="global" {{ old('lead_view_permission') ?? $admin->lead_view_permission == 'global' ? 'selected' : '' }}>{{ __('admin::app.settings.users.global') }}</option>
                                    <option value="individual" {{ old('lead_view_permission') ?? $admin->lead_view_permission == 'individual' ? 'selected' : '' }}>{{ __('admin::app.settings.users.individual') }}</option>
                                </select>

                                <span class="control-error" v-if="errors.has('lead_view_permission')">
                                    @{{ errors.first('lead_view_permission') }}
                                </span>
                            </div>

                            <div class="form-group" :class="[errors.has('password') ? 'has-error' : '']">
                                <label>
                                    {{ __('admin::app.settings.users.password') }}
                                </label>

                                <input
                                    ref="password"
                                    type="password"
                                    name="password"
                                    class="control"
                                    v-validate="'min:6'"
                                    data-vv-as="{{ __('admin::app.settings.users.password') }}"
                                    placeholder="{{ __('admin::app.settings.users.password') }}"
                                />

                                <span class="control-error" v-if="errors.has('password')">
                                    @{{ errors.first('password') }}
                                </span>
                            </div>

                            <div class="form-group" :class="[errors.has('confirm_password') ? 'has-error' : '']">
                                <label>
                                    {{ __('admin::app.settings.users.confirm_password') }}
                                </label>

                                <input
                                    type="password"
                                    class="control"
                                    name="confirm_password"
                                    v-validate="'confirmed:password'"
                                    data-vv-as="{{ __('admin::app.settings.users.confirm_password') }}"
                                    placeholder="{{ __('admin::app.settings.users.confirm_password') }}"
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