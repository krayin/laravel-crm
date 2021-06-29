@extends('ui::datagrid.table')

@section('page_title')
    {{ __('admin::app.settings.sources.title') }}
@stop

@section('table-header')
    {{ Breadcrumbs::render('settings.sources') }}

    {{ __('admin::app.settings.sources.title') }}
@stop

@section('table-action')
    <button class="btn btn-md btn-primary" @click="openModal('addSourceModal')">{{ __('admin::app.settings.sources.add-title') }}</button>
@stop

@section('meta-content')
    <form action="{{ route('admin.settings.sources.store') }}" method="POST" @submit.prevent="onSubmit">
        <modal id="addSourceModal" :is-open="modalIds.addSourceModal">
            <h3 slot="header-title">{{ __('admin::app.settings.sources.add-title') }}</h3>
            
            <div slot="header-actions">
                <button class="btn btn-sm btn-secondary-outline" @click="closeModal('addSourceModal')">{{ __('admin::app.settings.sources.cancel') }}</button>

                <button type="submit" class="btn btn-sm btn-primary">{{ __('admin::app.settings.sources.save-btn-title') }}</button>
            </div>

            <div slot="body">
                @csrf()

                <div class="form-group" :class="[errors.has('name') ? 'has-error' : '']">
                    <label>
                        {{ __('admin::app.layouts.name') }}
                    </label>

                    <input type="text" name="name" class="control" v-validate="'required'" data-vv-as="{{ __('admin::app.layouts.name') }}" placeholder="{{ __('admin::app.layouts.name') }}" />

                    <span class="control-error" v-if="errors.has('name')">
                        @{{ errors.first('name') }}
                    </span>
                </div>
            </div>
        </modal>
    </form>
@stop