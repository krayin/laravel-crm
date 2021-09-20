@extends('admin::layouts.master')

@section('page_title')
    {{ __('admin::app.settings.groups.edit-title') }}
@stop

@section('content-wrapper')
    <div class="content full-page adjacent-center">
        {!! view_render_event('admin.settings.groups.edit.header.before', ['group' => $group]) !!}

        <div class="page-header">
            
            {{ Breadcrumbs::render('settings.groups.edit', $group) }}

            <div class="page-title">
                <h1>{{ __('admin::app.settings.groups.edit-title') }}</h1>
            </div>
        </div>

        {!! view_render_event('admin.settings.groups.edit.header.after', ['group' => $group]) !!}

        <form method="POST" action="{{ route('admin.settings.groups.update', ['id' => $group->id]) }}" @submit.prevent="onSubmit">
            <div class="page-content">
                <div class="form-container">
                    <div class="panel">
                        <div class="panel-header">
                            {!! view_render_event('admin.settings.groups.edit.form_buttons.before', ['group' => $group]) !!}

                            <button type="submit" class="btn btn-md btn-primary">
                                {{ __('admin::app.settings.groups.save-btn-title') }}
                            </button>

                            <a href="{{ route('admin.settings.groups.index') }}">
                                {{ __('admin::app.layouts.back') }}
                            </a>

                            {!! view_render_event('admin.settings.groups.edit.form_buttons.after', ['group' => $group]) !!}
                        </div>

                        <div class="panel-body">
                            {!! view_render_event('admin.settings.groups.edit.form_controls.before', ['group' => $group]) !!}

                            @csrf()

                            <input name="_method" type="hidden" value="PUT">
                            
                            <div class="form-group" :class="[errors.has('name') ? 'has-error' : '']">
                                <label class="required">
                                    {{ __('admin::app.layouts.name') }}
                                </label>

                                <input
                                    type="text"
                                    name="name"
                                    value="{{ old('name') ?: $group->name }}"
                                    class="control"
                                    placeholder="{{ __('admin::app.layouts.name') }}"
                                    v-validate="'required'"
                                    data-vv-as="{{ __('admin::app.layouts.name') }}"
                                />

                                <span class="control-error" v-if="errors.has('name')">
                                    @{{ errors.first('name') }}
                                </span>
                            </div>

                            <div class="form-group" :class="[errors.has('description') ? 'has-error' : '']">
                                <label class="required">
                                    {{ __('admin::app.settings.groups.description') }}
                                </label>

                                <textarea
                                    class="control"
                                    name="description"
                                    placeholder="{{ __('admin::app.settings.groups.description') }}"
                                    v-validate="'required'"
                                    data-vv-as="{{ __('admin::app.settings.groups.description') }}"
                                >{{ old('description') ?: $group->description }}</textarea>

                                <span class="control-error" v-if="errors.has('description')">
                                    @{{ errors.first('description') }}
                                </span>
                            </div>

                            {!! view_render_event('admin.settings.groups.edit.form_controls.after', ['group' => $group]) !!}
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
@stop