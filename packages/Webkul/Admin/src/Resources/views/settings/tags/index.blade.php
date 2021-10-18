@extends('admin::layouts.master')

@section('page_title')
    {{ __('admin::app.settings.tags.title') }}
@stop

@section('css')
    <style>
        .color-item {
            position: relative;
            display: inline-block;
            margin: 10px 5px 5px 0px;
        }

        .color-item input {
            position: absolute;
            top: 4px;
            left: 1px;
            opacity: 0;
            z-index: 100;
            cursor: pointer;
        }

        .color-item label {
            width: 25px;
            height: 25px;
            margin-right: 3px;
            border-radius: 50%;
            cursor: pointer;
            display: inline-block;
            box-shadow: 0px 4px 15.36px 0.75px rgb(0 0 0 / 10%), 0px 2px 6px 0px rgb(0 0 0 / 15%);
            transition: 0.2s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .color-item input:checked + label {
            border: solid 3px #FFFFFF;
        }
    </style>
@stop

@section('content-wrapper')
    <div class="content full-page">
        <table-component data-src="{{ route('admin.settings.tags.index') }}">
            <template v-slot:table-header>
                <h1>
                    {!! view_render_event('admin.settings.tags.index.header.before') !!}

                    {{ Breadcrumbs::render('settings.tags') }}

                    {{ __('admin::app.settings.tags.title') }}

                    {!! view_render_event('admin.settings.tags.index.header.after') !!}
                </h1>
            </template>

            @if (bouncer()->hasPermission('settings.other_settings.tags.create'))
                <template v-slot:table-action>
                    <button class="btn btn-md btn-primary" @click="openModal('addTagModal')">{{ __('admin::app.settings.tags.create-title') }}</button>
                </template>
            @endif
        <table-component>
    </div>

    <form action="{{ route('admin.settings.tags.store') }}" method="POST" @submit.prevent="onSubmit">
        <modal id="addTagModal" :is-open="modalIds.addTagModal">
            <h3 slot="header-title">{{ __('admin::app.settings.tags.create-title') }}</h3>

            <div slot="header-actions">
                {!! view_render_event('admin.settings.tags.create.form_buttons.before') !!}

                <button class="btn btn-sm btn-secondary-outline" @click="closeModal('addTagModal')">{{ __('admin::app.settings.tags.cancel') }}</button>

                <button type="submit" class="btn btn-sm btn-primary">{{ __('admin::app.settings.tags.save-btn-title') }}</button>

                {!! view_render_event('admin.settings.tags.create.form_buttons.after') !!}
            </div>

            <div slot="body">
                {!! view_render_event('admin.settings.tags.create.form_controls.before') !!}

                @csrf()

                <div class="form-group" :class="[errors.has('name') ? 'has-error' : '']">
                    <label class="required">
                        {{ __('admin::app.settings.tags.name') }}
                    </label>

                    <input
                        type="text"
                        name="name"
                        class="control"
                        placeholder="{{ __('admin::app.settings.tags.name') }}"
                        v-validate="'required'"
                        data-vv-as="{{ __('admin::app.settings.tags.name') }}"
                    />

                    <span class="control-error" v-if="errors.has('name')">
                        @{{ errors.first('name') }}
                    </span>
                </div>

                <div class="form-group">
                    <label>{{ __('admin::app.settings.tags.color') }}</label>
                    
                    <div class="color-list">
                        <span class="color-item">
                            <input type="radio" id="337CFF" name="color" value="#337CFF">
                            <label for="337CFF" style="background: #337CFF;"></label>
                        </span>

                        <span class="color-item">
                            <input type="radio" id="FEBF00" name="color" value="#FEBF00">
                            <label for="FEBF00" style="background: #FEBF00;"></label>
                        </span>

                        <span class="color-item">
                            <input type="radio" id="E5549F" name="color" value="#E5549F">
                            <label for="E5549F" style="background: #E5549F;"></label>
                        </span>

                        <span class="color-item">
                            <input type="radio" id="27B6BB" name="color" value="#27B6BB">
                            <label for="27B6BB" style="background: #27B6BB;"></label>
                        </span>

                        <span class="color-item">
                            <input type="radio" id="FB8A3F" name="color" value="#FB8A3F">
                            <label for="FB8A3F" style="background: #FB8A3F;"></label>
                        </span>

                        <span class="color-item">
                            <input type="radio" id="43AF52" name="color" value="#43AF52">
                            <label for="43AF52" style="background: #43AF52;"></label>
                        </span>
                    </div>
                </div>

                {!! view_render_event('admin.settings.tags.create.form_controls.after') !!}
            </div>
        </modal>
    </form>
@stop