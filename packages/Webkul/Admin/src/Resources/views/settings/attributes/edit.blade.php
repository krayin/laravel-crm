@extends('admin::layouts.master')

@section('page_title')
    {{ __('admin::app.settings.attributes.edit-title') }}
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
        {!! view_render_event('admin.settings.attributes.edit.header.before', ['attribute' => $attribute]) !!}

        <div class="page-header">

            {{ Breadcrumbs::render('settings.attributes.edit', $attribute) }}

            <div class="page-title">
                <h1>{{ __('admin::app.settings.attributes.edit-title') }}</h1>
            </div>
        </div>

        {!! view_render_event('admin.settings.attributes.edit.header.after', ['attribute' => $attribute]) !!}

        <form method="POST" action="{{ route('admin.settings.attributes.update', $attribute->id) }}" @submit.prevent="onSubmit">

            <div class="page-content">
                <div class="form-container">

                    <div class="panel">
                        <div class="panel-header">
                            {!! view_render_event('admin.settings.attributes.edit.form_buttons.before', ['attribute' => $attribute]) !!}

                            <button type="submit" class="btn btn-md btn-primary">
                                {{ __('admin::app.settings.attributes.save-btn-title') }}
                            </button>

                            <a href="{{ route('admin.settings.attributes.index') }}">{{ __('admin::app.settings.attributes.back') }}</a>

                            {!! view_render_event('admin.settings.attributes.edit.form_buttons.after', ['attribute' => $attribute]) !!}
                        </div>

                        <div class="panel-body">
                            {!! view_render_event('admin.settings.attributes.edit.form_controls.before', ['attribute' => $attribute]) !!}

                            @csrf()

                            <input name="_method" type="hidden" value="PUT">

                            <div class="form-group" :class="[errors.has('code') ? 'has-error' : '']">
                                <label for="code" class="required">{{ __('admin::app.settings.attributes.code') }}</label>

                                <input
                                    type="text"
                                    name="code"
                                    class="control"
                                    id="code"
                                    value="{{ old('code') ?: $attribute->code }}"
                                    v-validate="'required'"
                                    data-vv-as="&quot;{{ __('admin::app.settings.attributes.code') }}&quot;"
                                    disabled="disabled"
                                    v-code
                                />

                                <input type="hidden" name="code" value="{{ $attribute->code }}"/>

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
                                    value="{{ old('name') ?: $attribute->name }}"
                                    v-validate="'required'"
                                    data-vv-as="&quot;{{ __('admin::app.settings.attributes.name') }}&quot;"
                                />

                                <span class="control-error" v-if="errors.has('name')">
                                    @{{ errors.first('name') }}
                                </span>
                            </div>

                            <div class="form-group">
                                <label for="entity_type" class="required">{{ __('admin::app.settings.attributes.entity-type') }}</label>

                                <?php $selectedOption = old('type') ?: $attribute->entity_type ?>

                                <select class="control" id="entity_type" name="entity_type">

                                    @foreach (config('attribute_entity_types') as $key => $entityType)
                                        <option value="{{ $key }}" {{ $selectedOption == $key ? 'selected' : '' }}>
                                            {{ $entityType['name'] }}
                                        </option>
                                    @endforeach

                                </select>
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

                                <select class="control" id="is_required" name="is_required" {{ ! (bool) $attribute->is_user_defined ? 'disabled' : ''}}>
                                    <option value="0" {{ $attribute->is_required ? '' : 'selected' }}>
                                        {{ __('admin::app.settings.attributes.no') }}
                                    </option>

                                    <option value="1" {{ $attribute->is_required ? 'selected' : '' }}>
                                        {{ __('admin::app.settings.attributes.yes') }}
                                    </option>
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

                            @if ($attribute->type == 'text')
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
                            @endif

                            {!! view_render_event('admin.settings.attributes.edit.form_controls.after', ['attribute' => $attribute]) !!}
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
                            class="list-group-item"
                            v-for="(row, index) in optionRows"
                            :key="row.id"
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

                    <select class="control" id="lookup_type" name="lookup_type" :value="'{{ $attribute->lookup_type }}'">
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

                <select class="control" id="lookup_type" name="lookup_type" :value="'{{ $attribute->lookup_type }}'">
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

            inject: ['$validator'],

            data: function() {
                return {
                    optionRowCount: 1,

                    typeValue: "{{ $selectedOption }}",

                    optionType: "{{ $attribute->lookup_type ? 'lookup' : 'options' }}",

                    optionRows: @json($attribute->options()->orderBy('sort_order')->get()),

                    lookupEntityTypes: @json(config('attribute_lookups')),
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
    </script>
@endpush
