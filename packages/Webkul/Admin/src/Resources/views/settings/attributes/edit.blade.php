@extends('admin::layouts.master')

@section('page_title')
    {{ __('admin::app.settings.attributes.edit-title') }}
@stop

@section('content-wrapper')
    <div class="content full-page adjacent-center">
        <div class="page-header">
            
            {{ Breadcrumbs::render('settings.attributes.edit', $attribute) }}

            <div class="page-title">
                <h1>{{ __('admin::app.settings.attributes.edit-title') }}</h1>
            </div>
        </div>

        <form method="POST" action="{{ route('admin.settings.attributes.update', $attribute->id) }}" @submit.prevent="onSubmit">

            <div class="page-content">
                <div class="form-container">

                    <div class="panel">
                        <div class="panel-header">
                            <button type="submit" class="btn btn-md btn-primary">
                                {{ __('admin::app.settings.attributes.save-btn-title') }}
                            </button>

                            <a href="{{ route('admin.settings.attributes.index') }}">{{ __('admin::app.settings.attributes.back') }}</a>
                        </div>
        
                        <div class="panel-body">
                            @csrf()

                            <input name="_method" type="hidden" value="PUT">

                            <div class="form-group" :class="[errors.has('code') ? 'has-error' : '']">
                                <label for="code" class="required">{{ __('admin::app.settings.attributes.code') }}</label>
                                <input type="text" v-validate="'required'" class="control" id="code" name="code" value="{{ old('code') ?: $attribute->code }}" disabled="disabled" data-vv-as="&quot;{{ __('admin::app.settings.attributes.code') }}&quot;" v-code/>
                                <input type="hidden" name="code" value="{{ $attribute->code }}"/>
                                <span class="control-error" v-if="errors.has('code')">@{{ errors.first('code') }}</span>
                            </div>

                            <div class="form-group" :class="[errors.has('name') ? 'has-error' : '']">
                                <label for="name" class="required">{{ __('admin::app.settings.attributes.name') }}</label>
                                <input type="text" v-validate="'required'" class="control" id="name" name="name" value="{{ old('name') ?: $attribute->name }}" data-vv-as="&quot;{{ __('admin::app.settings.attributes.name') }}&quot;"/>
                                <span class="control-error" v-if="errors.has('name')">@{{ errors.first('name') }}</span>
                            </div>

                            <div class="form-group">
                                <entity-type name="entity_type" value="{{ $attribute->entity_type }}" disabled="true" required="true"></entity-type>
                            </div>

                            <div class="form-group">
                                <?php $selectedOption = old('type') ?: $attribute->type ?>
                                <label for="type">{{ __('admin::app.settings.attributes.type') }}</label>
                                <select class="control" id="type" disabled="disabled">
                                    <option value="text" {{ $selectedOption == 'text' ? 'selected' : '' }}>
                                        {{ __('admin::app.settings.attributes.text') }}
                                    </option>
                                    <option value="textarea" {{ $selectedOption == 'textarea' ? 'selected' : '' }}>
                                        {{ __('admin::app.settings.attributes.textarea') }}
                                    </option>
                                    <option value="price" {{ $selectedOption == 'price' ? 'selected' : '' }}>
                                        {{ __('admin::app.settings.attributes.price') }}
                                    </option>
                                    <option value="boolean" {{ $selectedOption == 'boolean' ? 'selected' : '' }}>
                                        {{ __('admin::app.settings.attributes.boolean') }}
                                    </option>
                                    <option value="select" {{ $selectedOption == 'select' ? 'selected' : '' }}>
                                        {{ __('admin::app.settings.attributes.select') }}
                                    </option>
                                    <option value="multiselect" {{ $selectedOption == 'multiselect' ? 'selected' : '' }}>
                                        {{ __('admin::app.settings.attributes.multiselect') }}
                                    </option>
                                    <option value="checkbox" {{ $selectedOption == 'checkbox' ? 'selected' : '' }}>
                                        {{ __('admin::app.settings.attributes.checkbox') }}
                                    </option>
                                    <option value="email" {{ $selectedOption == 'email' ? 'selected' : '' }}>
                                        {{ __('admin::app.settings.attributes.email') }}
                                    </option>
                                    <option value="address" {{ $selectedOption == 'address' ? 'selected' : '' }}>
                                        {{ __('admin::app.settings.attributes.address') }}
                                    </option>
                                    <option value="phone" {{ $selectedOption == 'phone' ? 'selected' : '' }}>
                                        {{ __('admin::app.settings.attributes.phone') }}
                                    </option>
                                    <option value="datetime" {{ $selectedOption == 'datetime' ? 'selected' : '' }}>
                                        {{ __('admin::app.settings.attributes.datetime') }}
                                    </option>
                                    <option value="date" {{ $selectedOption == 'date' ? 'selected' : '' }}>
                                        {{ __('admin::app.settings.attributes.date') }}
                                    </option>
                                    <option value="image" {{ $selectedOption == 'image' ? 'selected' : '' }}>
                                        {{ __('admin::app.settings.attributes.image') }}
                                    </option>
                                    <option value="file" {{ $selectedOption == 'file' ? 'selected' : '' }}>
                                        {{ __('admin::app.settings.attributes.file') }}
                                    </option>
                                    <option value="lookup" {{ $selectedOption == 'lookup' ? 'selected' : '' }}>
                                        {{ __('admin::app.settings.attributes.lookup') }}
                                    </option>
                                </select>
                                <input type="hidden" name="type" value="{{ $attribute->type }}"/>
                            </div>

                            <div id="options" class="{{ in_array($attribute->type, ['select', 'multiselect', 'checkbox', 'lookup']) ?: 'hide' }}">

                                <option-wrapper></option-wrapper>

                            </div>

                            <div class="form-group">
                                <label for="is_required">{{ __('admin::app.settings.attributes.is_required') }}</label>
                                <select class="control" id="is_required" name="is_required">
                                    <option value="0" {{ $attribute->is_required ? '' : 'selected' }}>{{ __('admin::app.settings.attributes.no') }}</option>
                                    <option value="1" {{ $attribute->is_required ? 'selected' : '' }}>{{ __('admin::app.settings.attributes.yes') }}</option>
                                </select>
                            </div>

                            <div class="form-group">
                                <label for="is_unique">{{ __('admin::app.settings.attributes.is_unique') }}</label>
                                <select class="control" id="is_unique" name="is_unique" disabled>
                                    <option value="0" {{ $attribute->is_unique ? '' : 'selected' }}>
                                        {{ __('admin::app.settings.attributes.no') }}
                                    </option>
                                    <option value="1" {{ $attribute->is_unique ? 'selected' : '' }}>
                                        {{ __('admin::app.settings.attributes.yes') }}
                                    </option>
                                </select>
                                <input type="hidden" name="is_unique" value="{{ $attribute->is_unique }}"/>
                            </div>

                            <div class="form-group">
                                <?php $selectedValidation = old('validation') ?: $attribute->validation ?>
                                <label for="validation">{{ __('admin::app.settings.attributes.input_validation') }}</label>
                                <select class="control" id="validation" name="validation">
                                    <option value=""></option>
                                    <option value="numeric" {{ $selectedValidation == 'numeric' ? 'selected' : '' }}>
                                        {{ __('admin::app.settings.attributes.number') }}
                                    </option>
                                    <option value="decimal" {{ $selectedValidation == 'decimal' ? 'selected' : '' }}>
                                        {{ __('admin::app.settings.attributes.decimal') }}
                                    </option>
                                    <option value="email" {{ $selectedValidation == 'email' ? 'selected' : '' }}>
                                        {{ __('admin::app.settings.attributes.email') }}
                                    </option>
                                    <option value="url" {{ $selectedValidation == 'url' ? 'selected' : '' }}>
                                        {{ __('admin::app.settings.attributes.url') }}
                                    </option>
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
        <div class="form-group dragable-container">
            <template v-if="typeValue != 'lookup'">
                <div class="form-group">
                    <label>{{ __('admin::app.settings.attributes.options') }}</label>

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
                            class="list-group-item"
                            v-for="(row, index) in optionRows"
                            :key="row.id"
                        >
                            <div class="form-group" :class="[errors.has('options[' + row.id + '][name]') ? 'has-error' : '']">
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
                </template>

                <entity-type v-else-if="optionType == 'lookup'" name="lookup_type" value="{{ $attribute->lookup_type }}" disabled="false"></entity-type>
            </template>

            <entity-type v-else name="lookup_type" disabled="false" value="{{ $attribute->lookup_type }}"></entity-type>
        </div>
    </script>

    <script type="text/x-template" id="entity-type-template">
        <div>
            <label :for="name" :class="required == 'true' ? 'required' : ''">
                {{ __('admin::app.settings.attributes.entity_type') }}
            </label>

            <select class="control" :id="name" :name="name" :value="value" :disabled="disabled == 'true'">
                <option
                    :key="index"
                    :value="index"
                    v-text="entityType.name"
                    v-for="(entityType, index) in lookupEntityTypes"
                ></option>
            </select>
        </div>
    </script>

    <script>
        Vue.component('option-wrapper', {

            template: '#options-template',

            inject: ['$validator'],

            data: function() {
                return {
                    optionRowCount: 1,
                    typeValue: "{{ $selectedOption }}",
                    optionType: "{{ $attribute->lookup_type ? 'lookup' : 'options' }}",
                    optionRows: @json($attribute->options()->orderBy('sort_order')->get()),
                }
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

        Vue.component('entity-type', {
            template: '#entity-type-template',

            props: ['name', 'value', 'disabled'],

            data: function() {
                return {
                    lookupEntityTypes: @json(config('attribute_entity_types')),
                }
            },
        });
    </script>
@endpush
