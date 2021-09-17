@extends('ui::datagrid.table')

@section('page_title')
    {{ __('admin::app.settings.types.title') }}
@stop

@section('table-header')
    {!! view_render_event('admin.settings.types.index.header.before') !!}

    {{ Breadcrumbs::render('settings.types') }}

    {{ __('admin::app.settings.types.title') }}

    {!! view_render_event('admin.settings.types.index.header.after') !!}
@stop

@section('table-action')
    <button class="btn btn-md btn-primary" @click="openModal('addTypeModal')">{{ __('admin::app.settings.types.create-title') }}</button>
@stop

@section('meta-content')
    <form action="{{ route('admin.settings.types.store') }}" method="POST" @submit.prevent="onSubmit">
        <modal id="addTypeModal" :is-open="modalIds.addTypeModal">
            <h3 slot="header-title">{{ __('admin::app.settings.types.create-title') }}</h3>
            
            <div slot="header-actions">
                {!! view_render_event('admin.settings.types.create.form_buttons.before') !!}

                <button class="btn btn-sm btn-secondary-outline" @click="closeModal('addTypeModal')">{{ __('admin::app.settings.types.cancel') }}</button>

                <button type="submit" class="btn btn-sm btn-primary">{{ __('admin::app.settings.types.save-btn-title') }}</button>

                {!! view_render_event('admin.settings.types.create.form_buttons.after') !!}
            </div>

            <div slot="body">
                {!! view_render_event('admin.settings.types.create.form_controls.before') !!}

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

                {!! view_render_event('admin.settings.types.create.form_controls.after') !!}
            </div>
        </modal>
    </form>
@stop