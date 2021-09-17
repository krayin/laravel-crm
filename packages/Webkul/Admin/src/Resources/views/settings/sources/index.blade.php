@extends('ui::datagrid.table')

@section('page_title')
    {{ __('admin::app.settings.sources.title') }}
@stop

@section('table-header')
    {!! view_render_event('admin.settings.sources.index.header.before') !!}

    {{ Breadcrumbs::render('settings.sources') }}

    {{ __('admin::app.settings.sources.title') }}

    {!! view_render_event('admin.settings.sources.index.header.after') !!}
@stop

@section('table-action')
    <button class="btn btn-md btn-primary" @click="openModal('addSourceModal')">{{ __('admin::app.settings.sources.create-title') }}</button>
@stop

@section('meta-content')
    <form action="{{ route('admin.settings.sources.store') }}" method="POST" @submit.prevent="onSubmit">
        <modal id="addSourceModal" :is-open="modalIds.addSourceModal">
            <h3 slot="header-title">{{ __('admin::app.settings.sources.create-title') }}</h3>
            
            <div slot="header-actions">
                {!! view_render_event('admin.settings.sources.create.form_buttons.before') !!}
                
                <button class="btn btn-sm btn-secondary-outline" @click="closeModal('addSourceModal')">{{ __('admin::app.settings.sources.cancel') }}</button>

                <button type="submit" class="btn btn-sm btn-primary">{{ __('admin::app.settings.sources.save-btn-title') }}</button>

                {!! view_render_event('admin.settings.sources.create.form_buttons.after') !!}
            </div>

            <div slot="body">
                {!! view_render_event('admin.settings.sources.create.form_controls.before') !!}

                @csrf()

                <div class="form-group" :class="[errors.has('name') ? 'has-error' : '']">
                    <label class="required">
                        {{ __('admin::app.layouts.name') }}
                    </label>

                    <input type="text" name="name" class="control" v-validate="'required'" data-vv-as="{{ __('admin::app.layouts.name') }}" placeholder="{{ __('admin::app.layouts.name') }}" />

                    <span class="control-error" v-if="errors.has('name')">
                        @{{ errors.first('name') }}
                    </span>
                </div>

                {!! view_render_event('admin.settings.sources.create.form_controls.after') !!}
            </div>
        </modal>
    </form>
@stop