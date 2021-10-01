@extends('admin::layouts.master')

@section('page_title')
    {{ __('admin::app.settings.users.create-title') }}
@stop

@section('content-wrapper')
    <div class="content full-page adjacent-center">
        {!! view_render_event('admin.settings.users.create.header.before') !!}

        <div class="page-header">
            
            {{ Breadcrumbs::render('settings.users.create') }}

            <div class="page-title">
                <h1>{{ __('admin::app.settings.users.create-title') }}</h1>
            </div>
        </div>

        {!! view_render_event('admin.settings.users.create.header.after') !!}

        <form method="POST" action="{{ route('admin.settings.users.store') }}" @submit.prevent="onSubmit">
            <div class="page-content">
                <div class="form-container">

                    <div class="panel">
                        <div class="panel-header">
                            {!! view_render_event('admin.settings.users.create.form_buttons.before') !!}

                            <button type="submit" class="btn btn-md btn-primary">
                                {{ __('admin::app.settings.users.save-btn-title') }}
                            </button>

                            <a href="{{ route('admin.settings.users.index') }}">
                                {{ __('admin::app.settings.users.back') }}
                            </a>

                            {!! view_render_event('admin.settings.users.create.form_buttons.after') !!}
                        </div>

                        <tabs>
                            <tab name="{{ __('admin::app.settings.users.general') }}" :selected="true">
                                {!! view_render_event('admin.settings.users.create.form_controls.general.before') !!}

                                @csrf()
                                
                                <div class="form-group" :class="[errors.has('name') ? 'has-error' : '']">
                                    <label class="required">
                                        {{ __('admin::app.settings.users.name') }}
                                    </label>

                                    <input
                                        type="text"
                                        name="name"
                                        class="control"
                                        value="{{ old('name') }}"
                                        placeholder="{{ __('admin::app.settings.users.name') }}"
                                        v-validate="'required'"
                                        data-vv-as="{{ __('admin::app.settings.users.name') }}"
                                    />

                                    <span class="control-error" v-if="errors.has('name')">
                                        @{{ errors.first('name') }}
                                    </span>
                                </div>

                                <div class="form-group" :class="[errors.has('email') ? 'has-error' : '']">
                                    <label class="required">
                                        {{ __('admin::app.settings.users.email') }}
                                    </label>

                                    <input
                                        type="email"
                                        name="email"
                                        class="control"
                                        value="{{ old('email') }}"
                                        placeholder="{{ __('admin::app.settings.users.email') }}"
                                        v-validate="'required|email'"
                                        data-vv-as="{{ __('admin::app.settings.users.email') }}"
                                    />

                                    <span class="control-error" v-if="errors.has('email')">
                                        @{{ errors.first('email') }}
                                    </span>
                                </div>

                                <div class="form-group">
                                    <label class="required">
                                        {{ __('admin::app.settings.users.status') }}
                                    </label>

                                    <label class="switch">
                                        <input
                                            type="checkbox"
                                            name="status"
                                            class="control"
                                            id="status"
                                            {{ old('status') ? 'checked' : '' }}
                                        />
                                        <span class="slider round"></span>
                                    </label>
                                </div>

                                <div class="form-group" :class="[errors.has('password') ? 'has-error' : '']">
                                    <label class="required">
                                        {{ __('admin::app.settings.users.password') }}
                                    </label>
    
                                    <input
                                        type="password"
                                        name="password"
                                        class="control"
                                        ref="password"
                                        placeholder="{{ __('admin::app.settings.users.password') }}"
                                        v-validate="'required|min:6'"
                                        data-vv-as="{{ __('admin::app.settings.users.password') }}"
                                    />
    
                                    <span class="control-error" v-if="errors.has('password')">
                                        @{{ errors.first('password') }}
                                    </span>
                                </div>
    
                                <div class="form-group" :class="[errors.has('confirm_password') ? 'has-error' : '']">
                                    <label class="required">
                                        {{ __('admin::app.settings.users.confirm_password') }}
                                    </label>
    
                                    <input
                                        type="password"
                                        class="control"
                                        name="confirm_password"
                                        placeholder="{{ __('admin::app.settings.users.confirm_password') }}"
                                        v-validate="'required|confirmed:password'"
                                        data-vv-as="{{ __('admin::app.settings.users.confirm_password') }}"
                                    />
    
                                    <span class="control-error" v-if="errors.has('confirm_password')">
                                        @{{ errors.first('confirm_password') }}
                                    </span>
                                </div>

                                {!! view_render_event('admin.settings.users.create.form_controls.general.after') !!}
                            </tab>
    
                            <tab name="{{ __('admin::app.settings.users.permission') }}">
                                {!! view_render_event('admin.settings.users.create.form_controls.permission.before') !!}

                                <div class="form-group">
                                    <label>
                                        {{ __('admin::app.settings.users.groups') }}
                                    </label>

                                    <select name="groups[]" class="control" multiple>
                                        @foreach ($groups as $group)
                                            <option value="{{ $group->id }}" {{ old('groups') && in_array($group->id, old('groups')) ? 'selected' : '' }}>
                                                {{ $group->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="form-group" :class="[errors.has('role') ? 'has-error' : '']">
                                    <label>
                                        {{ __('admin::app.settings.users.role') }}
                                    </label>
    
                                    <select
                                        name="role_id"
                                        class="control"
                                        data-vv-as="{{ __('admin::app.settings.users.role') }}"
                                        v-validate="'required'"
                                    >
                                        @foreach ($roles as $role)
                                            <option value="{{ $role->id }}" {{ old('role_id') == $role->id ? 'selected' : '' }}>
                                                {{ $role->name }}
                                            </option>
                                        @endforeach
                                    </select>
    
                                    <span class="control-error" v-if="errors.has('role_id')">
                                        @{{ errors.first('role_id') }}
                                    </span>
                                </div>
    
                                <div class="form-group" :class="[errors.has('view_permission') ? 'has-error' : '']">
                                    <label>
                                        {{ __('admin::app.settings.users.view-permission') }}
                                    </label>
    
                                    <select
                                        name="view_permission"
                                        class="control"
                                        v-validate="'required'"
                                        data-vv-as="{{ __('admin::app.settings.users.view-permission') }}"
                                    >
                                        <option value="global" {{ old('view_permission') == 'global' ? 'selected' : '' }}>
                                            {{ __('admin::app.settings.users.global') }}
                                        </option>

                                        <option value="group" {{ old('view_permission') == 'group' ? 'selected' : '' }}>
                                            {{ __('admin::app.settings.users.group') }}
                                        </option>

                                        <option value="individual" {{ old('view_permission') == 'individual' ? 'selected' : '' }}>
                                            {{ __('admin::app.settings.users.individual') }}
                                        </option>
                                    </select>
    
                                    <span class="control-error" v-if="errors.has('view_permission')">
                                        @{{ errors.first('view_permission') }}
                                    </span>
                                </div>

                                {!! view_render_event('admin.settings.users.create.form_controls.permission.after') !!}
                            </tab>
                        </tabs>
                    </div>
                    
                </div>
            </div>
        </form>
    </div>
@stop