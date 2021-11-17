@extends('admin::layouts.master')

@section('page_title')
    {{ __('admin::app.settings.pipelines.edit-title') }}
@stop

@section('content-wrapper')
    <div class="content full-page adjacent-center">
        {!! view_render_event('admin.settings.pipelines.edit.header.before', ['pipeline' => $pipeline]) !!}

        <div class="page-header">
            {{ Breadcrumbs::render('settings.pipelines.edit', $pipeline) }}

            <div class="page-title">
                <h1>{{ __('admin::app.settings.pipelines.edit-title') }}</h1>
            </div>
        </div>

        {!! view_render_event('admin.settings.pipelines.edit.header.after', ['pipeline' => $pipeline]) !!}

        <form method="POST" action="{{ route('admin.settings.pipelines.update', $pipeline->id) }}" @submit.prevent="onSubmit">
            <div class="page-content">
                <div class="form-container">
                    <div class="panel">
                        <div class="panel-header">
                            {!! view_render_event('admin.settings.pipelines.edit.form_buttons.before', ['pipeline' => $pipeline]) !!}

                            <button type="submit" class="btn btn-md btn-primary">
                                {{ __('admin::app.settings.pipelines.save-btn-title') }}
                            </button>

                            <a href="{{ route('admin.settings.pipelines.index') }}">
                                {{ __('admin::app.layouts.back') }}
                            </a>

                            {!! view_render_event('admin.settings.pipelines.edit.form_buttons.after', ['pipeline' => $pipeline]) !!}
                        </div>

                        <div class="panel-body">
                            {!! view_render_event('admin.settings.pipelines.edit.form_controls.before', ['pipeline' => $pipeline]) !!}

                            @csrf()

                            <input name="_method" type="hidden" value="PUT">

                            <div class="form-group" :class="[errors.has('name') ? 'has-error' : '']">
                                <label class="required">
                                    {{ __('admin::app.settings.pipelines.name') }}
                                </label>

                                <input
                                    type="text"
                                    name="name"
                                    class="control"
                                    value="{{ old('name') ?: $pipeline->name }}"
                                    v-validate="'required'"
                                    data-vv-as="{{ __('admin::app.settings.pipelines.name') }}"
                                />

                                <span class="control-error" v-if="errors.has('name')">
                                    @{{ errors.first('name') }}
                                </span>
                            </div>

                            <div class="form-group" :class="[errors.has('rotten_days') ? 'has-error' : '']">
                                <label class="required">
                                    {{ __('admin::app.settings.pipelines.rotting-days') }}
                                </label>

                                <input
                                    type="text"
                                    name="rotten_days"
                                    class="control"
                                    value="{{ old('rotten_days') ?: $pipeline->rotten_days }}"
                                    v-validate="'required|numeric|min_value:1'"
                                    data-vv-as="{{ __('admin::app.settings.pipelines.rotting-days') }}"
                                />

                                <span class="control-error" v-if="errors.has('rotten_days')">
                                    @{{ errors.first('rotten_days') }}
                                </span>
                            </div>

                            @if ($pipeline->is_default)
                                <input type="hidden" name="is_default" value="1"/>
                            @else
                                <div class="form-group">
                                    <label>
                                        {{ __('admin::app.settings.pipelines.is-default') }}
                                    </label>

                                    <label class="switch">
                                        <input
                                            type="checkbox"
                                            name="is_default"
                                            class="control"
                                            id="is_default"
                                            {{ $pipeline->is_default ? 'checked disabled' : '' }}
                                        />
                                        <span class="slider round"></span>
                                    </label>
                                </div>
                            @endif

                            <stages-component></stages-component>

                            {!! view_render_event('admin.settings.pipelines.edit.form_controls.after', ['pipeline' => $pipeline]) !!}
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
@stop

@include('admin::settings.pipelines.common.pipeline-components-helper')

@push('scripts')
    <script type="text/x-template" id="stages-component-template">
        <div class="table dragable-container">
            <table>
                <thead class="thead-dark">
                    <tr>
                        <th></th>
                        <th>{{ __('admin::app.settings.pipelines.name') }}</th>
                        <th>{{ __('admin::app.settings.pipelines.probability') }}</th>
                        <th></th>
                    </tr>
                </thead>

                <draggable tag="tbody" :list="stages" draggable=".draggable">
                    <tr :key="stage.id" v-for="(stage, index) in stages" v-bind:class="{ draggable: isDragable(stage) }">
                        <td class="dragable-icon">
                            <i class="icon align-justify-icon" v-if="isDragable(stage)"></i>
                        </td>

                        <td>
                            <div class="form-group" :class="[errors.has('stages[' + stage.id + '][name]') ? 'has-error' : '']">
                                <input
                                    type="hidden"
                                    :value="slugify(stage.name)"
                                    :name="'stages[' + stage.id + '][code]'"
                                />

                                <input
                                    type="text"
                                    :name="'stages[' + stage.id + '][name]'"
                                    class="control"
                                    v-model="stage['name']"
                                    v-validate="'required|unique_name'"
                                    data-vv-as="&quot;{{ __('admin::app.settings.pipelines.name') }}&quot;"
                                    :readonly="! isDragable(stage)"
                                />

                                <input
                                    type="hidden"
                                    :value="index + 1"
                                    :name="'stages[' + stage.id + '][sort_order]'"
                                />

                                <span class="control-error" v-if="errors.has('stages[' + stage.id + '][name]')">
                                    @{{ errors.first('stages[' + stage.id + '][name]') }}
                                </span>
                            </div>
                        </td>

                        <td>
                            <div class="form-group" :class="[errors.has('stages[' + stage.id + '][probability]') ? 'has-error' : '']">
                                <input
                                    type="text"
                                    :name="'stages[' + stage.id + '][probability]'"
                                    class="control"
                                    v-model="stage['probability']"
                                    v-validate="'required|numeric|min_value:0|max_value:100'"
                                    data-vv-as="&quot;{{ __('admin::app.settings.pipelines.probability') }}&quot;"
                                    :readonly="stage.code != 'new' && ! isDragable(stage)"
                                />

                                <span class="control-error" v-if="errors.has('stages[' + stage.id + '][probability]')">
                                    @{{ errors.first('stages[' + stage.id + '][probability]') }}
                                </span>
                            </div>
                        </td>

                        <td class="delete-icon">
                            <i class="icon trash-icon" @click="removeStage(stage)" v-if="isDragable(stage)"></i>
                        </td>
                    </tr>
                </draggable>
            </table>

            <button type="button" class="btn btn-md btn-primary mt-20" @click="addStage">
                {{ __('admin::app.settings.pipelines.add-stage-btn-title') }}
            </button>
        </div>
    </script>

    <script>
        Vue.component('stages-component', {
            template: '#stages-component-template',

            inject: ['$validator'],

            data: function() {
                return {
                    stages: @json($pipeline->stages),

                    stageCount: 1,
                }
            },

            created: function () {
                this.extendValidator();
            },

            methods: {
                ...stagesComponentMethods
            },
        });
    </script>
@endpush
