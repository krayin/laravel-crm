@extends('admin::layouts.master')

@section('page_title')
    {{ __('admin::app.settings.users.edit-title') }}
@stop

@section('content-wrapper')
    <div class="content full-page adjacent-center">
        {!! view_render_event('admin.settings.users.edit.header.before', ['admin' => $admin]) !!}

        <div class="page-header">
            
            {{ Breadcrumbs::render('settings.users.edit', $admin) }}

            <div class="page-title">
                <h1>{{ __('admin::app.settings.users.edit-title') }}</h1>
            </div>
        </div>

        {!! view_render_event('admin.settings.users.edit.header.after', ['admin' => $admin]) !!}

        <form method="POST" action="{{ route('admin.settings.users.update', ['id' => $admin->id]) }}" @submit.prevent="onSubmit">
            <div class="page-content">
                <div class="form-container">
                    <div class="panel">
                        <div class="panel-header">
                            {!! view_render_event('admin.settings.users.edit.form_buttons.before', ['admin' => $admin]) !!}

                            <button type="submit" class="btn btn-md btn-primary">
                                {{ __('admin::app.settings.users.save-btn-title') }}
                            </button>

                            <a href="{{ route('admin.settings.users.index') }}">
                                {{ __('admin::app.settings.users.back') }}
                            </a>

                            {!! view_render_event('admin.settings.users.edit.form_buttons.after', ['admin' => $admin]) !!}
                        </div>

                        <tabs>
                            <tab name="{{ __('admin::app.settings.users.general') }}" :selected="true">
                                {!! view_render_event('admin.settings.users.edit.form_controls.general.before', ['admin' => $admin]) !!}

                                @csrf()
                                
                                <input name="_method" type="hidden" value="PUT">
                                
                                <div class="form-group" :class="[errors.has('name') ? 'has-error' : '']">
                                    <label class="required">
                                        {{ __('admin::app.settings.users.name') }}
                                    </label>

                                    <input
                                        type="text"
                                        name="name"
                                        class="control"
                                        value="{{ old('name') ?? $admin->name }}"
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
                                        value="{{ old('email') ?? $admin->email }}"
                                        placeholder="{{ __('admin::app.settings.users.email') }}"
                                        v-validate="'required|email'"
                                        data-vv-as="{{ __('admin::app.settings.users.email') }}"
                                    />

                                    <span class="control-error" v-if="errors.has('email')">
                                        @{{ errors.first('email') }}
                                    </span>
                                </div>

                                @if ($admin->id != auth()->guard('user')->user()->id)
                                    <div class="form-group" :class="[errors.has('status') ? 'has-error' : '']">
                                        <label class="required">
                                            {{ __('admin::app.settings.users.status') }}
                                        </label>

                                        <label class="switch">
                                            <input
                                                type="checkbox"
                                                name="status"
                                                class="control"
                                                id="status"
                                                {{ (old('status') || $admin->status) ? 'checked' : '' }}
                                            />

                                            <span class="slider round"></span>
                                        </label>
                                    </div>
                                @endif

                                <div class="form-group" :class="[errors.has('password') ? 'has-error' : '']">
                                    <label>
                                        {{ __('admin::app.settings.users.password') }}
                                    </label>

                                    <input
                                        type="password"
                                        name="password"
                                        class="control"
                                        ref="password"
                                        placeholder="{{ __('admin::app.settings.users.password') }}"
                                        v-validate="'min:6'"
                                        data-vv-as="{{ __('admin::app.settings.users.password') }}"
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
                                        name="confirm_password"
                                        class="control"
                                        placeholder="{{ __('admin::app.settings.users.confirm_password') }}"
                                        v-validate="'confirmed:password'"
                                        data-vv-as="{{ __('admin::app.settings.users.confirm_password') }}"
                                    />

                                    <span class="control-error" v-if="errors.has('confirm_password')">
                                        @{{ errors.first('confirm_password') }}
                                    </span>
                                </div>

                                {!! view_render_event('admin.settings.users.edit.form_controls.general.after', ['admin' => $admin]) !!}
                            </tab>

                            <tab name="{{ __('admin::app.settings.users.permission') }}">
                                {!! view_render_event('admin.settings.users.edit.form_controls.permission.before', ['admin' => $admin]) !!}

                                <div class="form-group">
                                    <label>
                                        {{ __('admin::app.settings.users.groups') }}
                                    </label>

                                    <?php $selectedOptionIds = old('groups') ?: $admin->groups->pluck('id')->toArray() ?>
                                    
                                    <select name="groups[]" class="control" multiple>

                                        @foreach ($groups as $group)
                                            <option value="{{ $group->id }}" {{ in_array($group->id, $selectedOptionIds) ? 'selected' : '' }}>
                                                {{ $group->name }}
                                            </option>
                                        @endforeach

                                    </select>
                                </div>

                                <div class="form-group" :class="[errors.has('role') ? 'has-error' : '']">
                                    <label>
                                        {{ __('admin::app.settings.users.role') }}
                                    </label>

                                    <?php $selectedOption = old('role_id') ?: $admin->role_id ?>

                                    <select
                                        name="role_id"
                                        class="control"
                                        v-validate="'required'"
                                        data-vv-as="{{ __('admin::app.settings.users.role') }}"
                                    >

                                        @foreach ($roles as $role)
                                            <option value="{{ $role->id }}" {{ $selectedOption == $role->id ? 'selected' : '' }}>
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

                                    <?php $selectedOption = old('view_permission') ?: $admin->view_permission ?>

                                    <select
                                        name="view_permission"
                                        class="control"
                                        v-validate="'required'"
                                        data-vv-as="{{ __('admin::app.settings.users.view-permission') }}"
                                    >

                                        <option value="global" {{ old('view_permission') ?? $selectedOption == 'global' ? 'selected' : '' }}>
                                            {{ __('admin::app.settings.users.global') }}
                                        </option>

                                        <option value="group" {{ old('view_permission') ?? $selectedOption == 'group' ? 'selected' : '' }}>
                                            {{ __('admin::app.settings.users.group') }}
                                        </option>

                                        <option value="individual" {{ old('view_permission') ?? $selectedOption == 'individual' ? 'selected' : '' }}>
                                            {{ __('admin::app.settings.users.individual') }}
                                        </option>
                                    </select>

                                    <span class="control-error" v-if="errors.has('view_permission')">
                                        @{{ errors.first('view_permission') }}
                                    </span>
                                </div>

                                {!! view_render_event('admin.settings.users.edit.form_controls.permission.after', ['admin' => $admin]) !!}
                            </tab>
                        </tabs>
                    </div>
                </div>
            </div>
        </form>
    </div>
@stop