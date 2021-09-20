@extends('admin::layouts.master')

@section('page_title')
    {{ __('admin::app.settings.types.edit-title') }}
@stop

@section('content-wrapper')
    <div class="content full-page adjacent-center">
        {!! view_render_event('admin.settings.types.edit.header.before', ['type' => $type]) !!}

        <div class="page-header">
            
            {{ Breadcrumbs::render('settings.types.edit', $type) }}

            <div class="page-title">
                <h1>{{ __('admin::app.settings.types.edit-title') }}</h1>
            </div>
        </div>

        {!! view_render_event('admin.settings.types.edit.header.after', ['type' => $type]) !!}

        <form method="POST" action="{{ route('admin.settings.types.update', ['id' => $type->id]) }}" @submit.prevent="onSubmit">
            <div class="page-content">
                <div class="form-container">
                    <div class="panel">
                        <div class="panel-header">
                            {!! view_render_event('admin.settings.types.edit.form_buttons.before', ['type' => $type]) !!}

                            <button type="submit" class="btn btn-md btn-primary">
                                {{ __('admin::app.settings.types.save-btn-title') }}
                            </button>

                            <a href="{{ route('admin.settings.types.index') }}">
                                {{ __('admin::app.layouts.back') }}
                            </a>

                            {!! view_render_event('admin.settings.types.edit.form_buttons.after', ['type' => $type]) !!}
                        </div>

                        <div class="panel-body">
                            {!! view_render_event('admin.settings.types.edit.form_controls.before', ['type' => $type]) !!}

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
                                    value="{{ $type->name }}"
                                    placeholder="{{ __('admin::app.layouts.name') }}"
                                    v-validate="'required'"
                                    data-vv-as="{{ __('admin::app.layouts.name') }}"
                                />

                                <span class="control-error" v-if="errors.has('name')">
                                    @{{ errors.first('name') }}
                                </span>
                            </div>

                            {!! view_render_event('admin.settings.types.edit.form_controls.after', ['type' => $type]) !!}
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
@stop