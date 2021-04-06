@extends('admin::layouts.master')

@section('page_title')
    {{ __('admin::app.settings.attributes.add-title') }}
@stop

@section('content-wrapper')
    <div class="content full-page adjacent-center">
        <div class="page-header">
            <div class="page-title">
                <h1>{{ __('admin::app.settings.attributes.add-title') }}</h1>
            </div>
        </div>

        <form method="POST" action="{{ route('admin.settings.attributes.store') }}" @submit.prevent="onSubmit" enctype="multipart/form-data">

            <div class="page-content">
                <div class="form-container">

                    <div class="panel">
                        <div class="panel-header">
                            <button type="submit" class="btn btn-md btn-primary">
                                {{ __('admin::app.settings.attributes.save-btn-title') }}
                            </button>

                            <a href="">{{ __('admin::app.settings.attributes.back') }}</a>
                        </div>
        
                        <div class="panel-body">
                            @csrf()

                            <div class="control-group" :class="[errors.has('code') ? 'has-error' : '']">
                                <label for="code" class="required">{{ __('admin::app.settings.attributes.code') }}</label>
                                <input type="text" v-validate="'required'" class="control" id="code" name="code" value="{{ old('code') }}"  data-vv-as="&quot;{{ __('admin::app.settings.attributes.code') }}&quot;" v-code/>
                                <span class="control-error" v-if="errors.has('code')">@{{ errors.first('code') }}</span>
                            </div>

                            <div class="control-group" :class="[errors.has('name') ? 'has-error' : '']">
                                <label for="name" class="required">{{ __('admin::app.settings.attributes.name') }}</label>
                                <input type="text" v-validate="'required'" class="control" id="name" name="name" value="{{ old('name') }}" data-vv-as="&quot;{{ __('admin::app.settings.attributes.name') }}&quot;"/>
                                <span class="control-error" v-if="errors.has('name')">@{{ errors.first('name') }}</span>
                            </div>

                            <div class="control-group">
                                <label for="type" class="required">{{ __('admin::app.settings.attributes.type') }}</label>
                                <select class="control" id="type" name="type">
                                    <option value="text">{{ __('admin::app.settings.attributes.text') }}</option>
                                    <option value="textarea">{{ __('admin::app.settings.attributes.textarea') }}</option>
                                    <option value="price">{{ __('admin::app.settings.attributes.price') }}</option>
                                    <option value="boolean">{{ __('admin::app.settings.attributes.boolean') }}</option>
                                    <option value="select">{{ __('admin::app.settings.attributes.select') }}</option>
                                    <option value="multiselect">{{ __('admin::app.settings.attributes.multiselect') }}</option>
                                    <option value="datetime">{{ __('admin::app.settings.attributes.datetime') }}</option>
                                    <option value="date">{{ __('admin::app.settings.attributes.date') }}</option>
                                    <option value="image">{{ __('admin::app.settings.attributes.image') }}</option>
                                    <option value="file">{{ __('admin::app.settings.attributes.file') }}</option>
                                    <option value="checkbox">{{ __('admin::app.settings.attributes.checkbox') }}</option>
                                </select>
                            </div>

                            <div class="hide" id="options">

                                <option-wrapper></option-wrapper>

                            </div>

                            <div class="control-group">
                                <label for="is_required">{{ __('admin::app.settings.attributes.is_required') }}</label>
                                <select class="control" id="is_required" name="is_required">
                                    <option value="0">{{ __('admin::app.settings.attributes.no') }}</option>
                                    <option value="1">{{ __('admin::app.settings.attributes.yes') }}</option>
                                </select>
                            </div>

                            <div class="control-group">
                                <label for="is_unique">{{ __('admin::app.settings.attributes.is_unique') }}</label>
                                <select class="control" id="is_unique" name="is_unique">
                                    <option value="0">{{ __('admin::app.settings.attributes.no') }}</option>
                                    <option value="1">{{ __('admin::app.settings.attributes.yes') }}</option>
                                </select>
                            </div>

                            <div class="control-group">
                                <label for="validation">{{ __('admin::app.settings.attributes.input_validation') }}</label>
                                <select class="control" id="validation" name="validation">
                                    <option value=""></option>
                                    <option value="numeric">{{ __('admin::app.settings.attributes.number') }}</option>
                                    <option value="email">{{ __('admin::app.settings.attributes.email') }}</option>
                                    <option value="decimal">{{ __('admin::app.settings.attributes.decimal') }}</option>
                                    <option value="url">{{ __('admin::app.settings.attributes.url') }}</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </form>
    </div>
@stop

@push('scripts')
    <script type="text/x-template" id="options-template">
        <div class="control-group dragable-container">
            <label>{{ __('admin::app.settings.attributes.options') }}</label>

            <draggable tag="ul" :list="optionRows" class="list-group dragable-list">
                <li
                    class="list-group-item"
                    v-for="(row, index) in optionRows"
                    :key="row.id"
                >
                    <div class="control-group" :class="[errors.has('options[' + row.id + '][name]') ? 'has-error' : '']">
                        <input type="text" v-validate="'required'" v-model="row['name']" :name="'options[' + row.id + '][name]'" class="control" data-vv-as="&quot;{{ __('admin::app.settings.attributes.name') }}&quot;"/>
                        <span class="control-error" v-if="errors.has('options[' + row.id + '][name]')">@{{ errors.first('options[' + row.id + '][name]') }}</span>

                        <i class="icon align-justify-icon"></i>
                    </div>

                    <i class="icon trash-icon" @click="removeRow(row)"></i>
                </li>
            </draggable>

            <button type="button" class="btn btn-md btn-primary mt-20" id="add-option-btn" @click="addOptionRow()">
                {{ __('admin::app.settings.attributes.add-option-btn-title') }}
            </button>
        </div>
    </script>

    <script>
        Vue.component('option-wrapper', {

            template: '#options-template',

            inject: ['$validator'],

            data: function() {
                return {
                    optionRowCount: 1,

                    optionRows: [],
                }
            },

            mounted: function() {
                $('#type').on('change', function (e) {
                    if (['select', 'multiselect', 'checkbox'].indexOf($(e.target).val()) === -1) {
                        $('#options').addClass('hide')
                    } else {
                        $('#options').removeClass('hide')
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
        })
    </script>
@endpush
