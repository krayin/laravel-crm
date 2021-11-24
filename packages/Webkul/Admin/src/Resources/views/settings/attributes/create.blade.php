@extends('admin::layouts.master')

@section('page_title')
    {{ __('admin::app.settings.attributes.create-title') }}
@stop

@push('css')
    <style>
        #options > div {
            padding: 10px;
            box-shadow: rgb(0 0 0 / 24%) 0px 3px 8px;
        }
    </style>
@endpush

@section('content-wrapper')
    <div class="content full-page adjacent-center">
        {!! view_render_event('admin.settings.attributes.create.header.before') !!}

        <div class="page-header">

            {{ Breadcrumbs::render('settings.attributes.create') }}

            <div class="page-title">
                <h1>{{ __('admin::app.settings.attributes.create-title') }}</h1>
            </div>
        </div>

        {!! view_render_event('admin.settings.attributes.create.header.after') !!}

        <form method="POST" action="{{ route('admin.settings.attributes.store') }}" @submit.prevent="onSubmit">

            <div class="page-content">
                <div class="form-container">

                    <div class="panel">
                        <div class="panel-header">
                            {!! view_render_event('admin.settings.attributes.create.form_buttons.before') !!}

                            <button type="submit" class="btn btn-md btn-primary">
                                {{ __('admin::app.settings.attributes.save-btn-title') }}
                            </button>

                            <a href="{{ route('admin.settings.attributes.index') }}">{{ __('admin::app.settings.attributes.back') }}</a>

                            {!! view_render_event('admin.settings.attributes.create.form_buttons.after') !!}
                        </div>

                        <div class="panel-body">
                            {!! view_render_event('admin.settings.attributes.create.form_controls.before') !!}

                            @csrf()

                            <div class="form-group" :class="[errors.has('code') ? 'has-error' : '']">
                                <label for="code" class="required">{{ __('admin::app.settings.attributes.code') }}</label>

                                <input
                                    type="text"
                                    name="code"
                                    class="control"
                                    id="code"
                                    value="{{ old('code') }}"
                                    v-validate="'required'"
                                    data-vv-as="&quot;{{ __('admin::app.settings.attributes.code') }}&quot;"
                                    v-code
                                />

                                <span class="control-error" v-if="errors.has('code')">
                                    @{{ errors.first('code') }}
                                </span>
                            </div>

                            <div class="form-group" :class="[errors.has('name') ? 'has-error' : '']">
                                <label for="name" class="required">{{ __('admin::app.settings.attributes.name') }}</label>

                                <input
                                    type="text"
                                    name="name"
                                    class="control"
                                    id="name"
                                    value="{{ old('name') }}"
                                    v-validate="'required'"
                                    data-vv-as="&quot;{{ __('admin::app.settings.attributes.name') }}&quot;"
                                />

                                <span class="control-error" v-if="errors.has('name')">
                                    @{{ errors.first('name') }}
                                </span>
                            </div>

                            <div class="form-group">
                                <label for="entity_type" class="required">{{ __('admin::app.settings.attributes.entity-type') }}</label>

                                <select class="control" id="entity_type" name="entity_type">
                                    @foreach (config('attribute_entity_types') as $key => $entityType)
                                        <option value="{{ $key }}">{{ $entityType['name'] }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="form-group">
                                <label for="type" class="required">{{ __('admin::app.settings.attributes.type') }}</label>

                                <select class="control" id="type" name="type">
                                    <option value="text">{{ __('admin::app.settings.attributes.text') }}</option>
                                    <option value="textarea">{{ __('admin::app.settings.attributes.textarea') }}</option>
                                    <option value="price">{{ __('admin::app.settings.attributes.price') }}</option>
                                    <option value="boolean">{{ __('admin::app.settings.attributes.boolean') }}</option>
                                    <option value="select">{{ __('admin::app.settings.attributes.select') }}</option>
                                    <option value="multiselect">{{ __('admin::app.settings.attributes.multiselect') }}</option>
                                    <option value="checkbox">{{ __('admin::app.settings.attributes.checkbox') }}</option>
                                    <option value="email">{{ __('admin::app.settings.attributes.email') }}</option>
                                    <option value="address">{{ __('admin::app.settings.attributes.address') }}</option>
                                    <option value="phone">{{ __('admin::app.settings.attributes.phone') }}</option>
                                    <option value="lookup">{{ __('admin::app.settings.attributes.lookup') }}</option>
                                    <option value="datetime">{{ __('admin::app.settings.attributes.datetime') }}</option>
                                    <option value="date">{{ __('admin::app.settings.attributes.date') }}</option>
                                    <option value="image">{{ __('admin::app.settings.attributes.image') }}</option>
                                    <option value="file">{{ __('admin::app.settings.attributes.file') }}</option>
                                </select>
                            </div>

                            <div class="hide" id="options">

                                <option-wrapper :attribute-type="$refs.attribute_type"></option-wrapper>

                            </div>

                            <div class="form-group">
                                <label for="is_required">{{ __('admin::app.settings.attributes.is_required') }}</label>

                                <select class="control" id="is_required" name="is_required">
                                    <option value="0">{{ __('admin::app.settings.attributes.no') }}</option>
                                    <option value="1">{{ __('admin::app.settings.attributes.yes') }}</option>
                                </select>
                            </div>

                            <div class="form-group">
                                <label for="is_unique">{{ __('admin::app.settings.attributes.is_unique') }}</label>

                                <select class="control" id="is_unique" name="is_unique">
                                    <option value="0">{{ __('admin::app.settings.attributes.no') }}</option>
                                    <option value="1">{{ __('admin::app.settings.attributes.yes') }}</option>
                                </select>
                            </div>

                            <div class="form-group">
                                <label for="validation">{{ __('admin::app.settings.attributes.input_validation') }}</label>

                                <select class="control" id="validation" name="validation">
                                    <option value=""></option>
                                    <option value="numeric">{{ __('admin::app.settings.attributes.number') }}</option>
                                    <option value="email">{{ __('admin::app.settings.attributes.email') }}</option>
                                    <option value="decimal">{{ __('admin::app.settings.attributes.decimal') }}</option>
                                    <option value="url">{{ __('admin::app.settings.attributes.url') }}</option>
                                </select>
                            </div>

                            {!! view_render_event('admin.settings.attributes.create.form_controls.after') !!}
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
@stop

@push('scripts')
    <script type="text/x-template" id="options-template">
        <div class="form-group dragable-container">
            <template v-if="typeValue != 'lookup'">
                <div class="form-group">
                    <label>{{ __('admin::app.settings.attributes.options-type') }}</label>

                    <select class="control" name="option_type" v-model="optionType">
                        <option value="lookup">
                            {{ __('admin::app.settings.attributes.lookup') }}
                        </option>

                        <option value="options">
                            {{ __('admin::app.settings.attributes.options') }}
                        </option>
                    </select>
                </div>

                <template v-if="optionType == 'options'">
                    <draggable tag="ul" :list="optionRows" class="list-group dragable-list">
                        <li
                            :key="row.id"
                            class="list-group-item"
                            v-for="(row, index) in optionRows"
                        >
                            <div class="form-group" :class="[errors.has('options[' + row.id + '][name]') ? 'has-error' : '']">
                                <input
                                    type="text"
                                    :name="'options[' + row.id + '][name]'"
                                    class="control"
                                    v-model="row['name']"
                                    v-validate="'required'"
                                    data-vv-as="&quot;{{ __('admin::app.settings.attributes.name') }}&quot;"
                                />

                                <span class="control-error" v-if="errors.has('options[' + row.id + '][name]')">
                                    @{{ errors.first('options[' + row.id + '][name]') }}
                                </span>

                                <i class="icon align-justify-icon"></i>
                            </div>

                            <i class="icon trash-icon" @click="removeRow(row)"></i>
                        </li>
                    </draggable>

                    <button type="button" class="btn btn-md btn-primary mt-20" id="add-option-btn" @click="addOptionRow">
                        {{ __('admin::app.settings.attributes.add-option-btn-title') }}
                    </button>
                </template>

                <div v-else>
                    <label for="lookup_type" class="required">
                        {{ __('admin::app.settings.attributes.lookup-type') }}
                    </label>

                    <select class="control" id="lookup_type" name="lookup_type">
                        <option
                            :key="index"
                            :value="index"
                            v-text="entityType.name"
                            v-for="(entityType, index) in lookupEntityTypes"
                        ></option>
                    </select>
                </div>

            </template>

            <div v-else>
                <label for="lookup_type" class="required">
                    {{ __('admin::app.settings.attributes.lookup-type') }}
                </label>

                <select class="control" id="lookup_type" name="lookup_type">
                    <option
                        :key="index"
                        :value="index"
                        v-text="entityType.name"
                        v-for="(entityType, index) in lookupEntityTypes"
                    ></option>
                </select>
            </div>
        </div>
    </script>

    <script>
        Vue.component('option-wrapper', {
            template: '#options-template',

            props: ['attributeType'],

            inject: ['$validator'],

            data: function() {
                return {
                    typeValue: '',

                    optionRows: [],

                    optionRowCount: 1,

                    optionType: 'lookup',

                    typesHasOptions: ['select', 'multiselect', 'checkbox', 'lookup'],

                    lookupEntityTypes: @json(config('attribute_lookups')),
                }
            },

            mounted: function() {
                $('#type').on('change', event => {
                    this.typeValue = $(event.target).val();

                    if (this.typesHasOptions.indexOf(this.typeValue) === -1) {
                        $('#options').addClass('hide');
                    } else {
                        $('#options').removeClass('hide');
                    }

                    if (this.typeValue == 'text') {
                        $('#validation').parent().show();
                    } else {
                        $('#validation').parent().hide();
                    }
                });
            },

            methods: {
                addOptionRow: function () {
                    this.optionRows.push({'id': 'option_' + this.optionRowCount++});
                },

                removeRow: function (row) {
                    const index = this.optionRows.indexOf(row);

                    Vue.delete(this.optionRows, index);
                },
            },
        });
    </script>
@endpush
