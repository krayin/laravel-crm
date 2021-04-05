@extends('admin::layouts.master')

@section('page_title')
    {{ __('admin::app.settings.users.create_user') }}
@stop

@section('content-wrapper')
    <div class="content full-page dashboard panel">
        <h6 class="breadcrumbs">
            Home/Leads/ 
        </h6>

        <h1>{{ __('admin::app.settings.users.create_user') }}</h1>

        <div class="panel-body">
            <form action="{{ route('admin.settings.users.store') }}" method="POST">
                @csrf()
                
                <div :class="`control-group ${errors.has('name') ? 'has-error' : ''}`">
                    <label>
                        {{ __('admin::app.settings.users.name') }}
                    </label>

                    <input
                        type="text"
                        name="name"
                        class="control"
                        v-validate="'required'"
                        data-vv-as="{{ __('admin::app.settings.users.name') }}"
                        placeholder="{{ __('admin::app.settings.users.name') }}"
                    />

                    <span class="control-error" v-if="errors.has('name')">
                        @{{ errors.first('name') }}
                    </span>
                </div>

                <div :class="`control-group ${errors.has('email') ? 'has-error' : ''}`">
                    <label>
                        {{ __('admin::app.settings.users.email') }}
                    </label>

                    <input
                        type="email"
                        name="email"
                        class="control"
                        data-vv-as="{{ __('admin::app.settings.users.email') }}"
                        placeholder="{{ __('admin::app.settings.users.email') }}"
                    />

                    <span class="control-error" v-if="errors.has('name')">
                        @{{ errors.first('name') }}
                    </span>
                </div>

                <div :class="`control-group ${errors.has('status') ? 'has-error' : ''}`">
                    <label>
                        {{ __('admin::app.settings.users.status') }}
                    </label>

                    <label class="switch">
                        <input
                            type="checkbox"
                            id="status"
                            name="status"
                            class="control"
                        />
                        <span class="slider round"></span>
                    </label>

                    <span class="control-error" v-if="errors.has('name')">
                        @{{ errors.first('name') }}
                    </span>
                </div>

                <div :class="`control-group ${errors.has('role') ? 'has-error' : ''}`">
                    <label>
                        {{ __('admin::app.settings.users.role') }}
                    </label>

                    <select name="role_id" class="control">
                        @foreach ($roles as $role)
                            <option value="{{ $role->id }}">{{ $role->name }}</option>
                        @endforeach
                    </select>

                    <span class="control-error" v-if="errors.has('name')">
                        @{{ errors.first('name') }}
                    </span>
                </div>

                <div :class="`control-group ${errors.has('password') ? 'has-error' : ''}`">
                    <label>
                        {{ __('admin::app.settings.users.password') }}
                    </label>

                    <input
                        type="password"
                        name="password"
                        class="control"
                        data-vv-as="{{ __('admin::app.settings.users.password') }}"
                        placeholder="{{ __('admin::app.settings.users.password') }}"
                    />

                    <span class="control-error" v-if="errors.has('name')">
                        @{{ errors.first('name') }}
                    </span>
                </div>

                <div :class="`control-group ${errors.has('confirm_password') ? 'has-error' : ''}`">
                    <label>
                        {{ __('admin::app.settings.users.confirm_password') }}
                    </label>

                    <input
                        type="password"
                        class="control"
                        name="confirm_password"
                        data-vv-as="{{ __('admin::app.settings.users.confirm_password') }}"
                        placeholder="{{ __('admin::app.settings.users.confirm_password') }}"
                    />

                    <span class="control-error" v-if="errors.has('name')">
                        @{{ errors.first('name') }}
                    </span>
                </div>

                <button type="submit" class="badge badge-xl badge-primary">
                    {{ __('admin::app.layouts.submit') }}
                </button>
            </form>
        </div>
    </div>
@stop