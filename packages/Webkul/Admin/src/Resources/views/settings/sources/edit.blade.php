@extends('admin::layouts.master')

@section('page_title')
    {{ __('admin::app.settings.sources.edit-title') }}
@stop

@section('content-wrapper')
    <div class="content full-page adjacent-center">
        {!! view_render_event('admin.settings.sources.edit.header.before', ['source' => $source]) !!}

        <div class="page-header">
            
            {{ Breadcrumbs::render('settings.sources.edit', $source) }}

            <div class="page-title">
                <h1>{{ __('admin::app.settings.sources.edit-title') }}</h1>
            </div>
        </div>

        {!! view_render_event('admin.settings.sources.edit.header.after', ['source' => $source]) !!}

        <form method="POST" action="{{ route('admin.settings.sources.update', ['id' => $source->id]) }}" @submit.prevent="onSubmit">
            <div class="page-content">
                <div class="form-container">
                    <div class="panel">
                        <div class="panel-header">
                            {!! view_render_event('admin.settings.sources.edit.form_buttons.before', ['source' => $source]) !!}

                            <button type="submit" class="btn btn-md btn-primary">
                                {{ __('admin::app.settings.sources.save-btn-title') }}
                            </button>

                            <a href="{{ route('admin.settings.sources.index') }}">
                                {{ __('admin::app.layouts.back') }}
                            </a>

                            {!! view_render_event('admin.settings.sources.edit.form_buttons.after', ['source' => $source]) !!}
                        </div>

                        <div class="panel-body">
                            {!! view_render_event('admin.settings.sources.edit.form_controls.before', ['source' => $source]) !!}

                            @csrf()

                            <input name="_method" type="hidden" value="PUT">
                            
                            <div class="form-group" :class="[errors.has('name') ? 'has-error' : '']">
                                <label class="required">
                                    {{ __('admin::app.layouts.name') }}
                                </label>

                                <input
                                    type="text"
                                    name="name"
                                    class="control"
                                    value="{{ $source->name }}"
                                    placeholder="{{ __('admin::app.layouts.name') }}"
                                    v-validate="'required'"
                                    data-vv-as="{{ __('admin::app.layouts.name') }}"
                                />

                                <span class="control-error" v-if="errors.has('name')">
                                    @{{ errors.first('name') }}
                                </span>
                            </div>

                            {!! view_render_event('admin.settings.sources.edit.form_controls.after', ['source' => $source]) !!}
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
@stop