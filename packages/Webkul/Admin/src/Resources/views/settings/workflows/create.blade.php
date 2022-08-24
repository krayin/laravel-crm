@extends('admin::layouts.master')

@section('page_title')
    {{ __('admin::app.settings.workflows.create-title') }}
@stop

@section('content-wrapper')
    <div class="content full-page adjacent-center">
        {!! view_render_event('admin.settings.workflows.create.header.before') !!}

        <div class="page-header">
            
            {{ Breadcrumbs::render('settings.workflows.create') }}

            <div class="page-title">
                <h1>{{ __('admin::app.settings.workflows.create-title') }}</h1>
            </div>
        </div>

        {!! view_render_event('admin.settings.workflows.create.header.after') !!}

        <form method="POST" action="{{ route('admin.settings.workflows.store') }}" @submit.prevent="onSubmit">
            <div class="page-content">
                <div class="form-container">
                    <div class="panel">
                        <div class="panel-header">
                            {!! view_render_event('admin.settings.workflows.create.form_buttons.before') !!}

                            <button type="submit" class="btn btn-md btn-primary">
                                {{ __('admin::app.settings.workflows.save-btn-title') }}
                            </button>

                            <a href="{{ route('admin.settings.workflows.index') }}">
                                {{ __('admin::app.layouts.back') }}
                            </a>

                            {!! view_render_event('admin.settings.workflows.create.form_buttons.after') !!}
                        </div>

                        <div class="panel-body workflow-container">
                            {!! view_render_event('admin.settings.workflows.create.form_controls.before') !!}

                            @csrf()

                            <workflow-component></workflow-component>

                            {!! view_render_event('admin.settings.workflows.create.form_controls.after') !!}
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>

    @include('admin::common.custom-attributes.edit.lookup')
@stop

@push('scripts')
    @parent

    <script type="text/x-template" id="workflow-component-template">

        <div>
            <div class="form-group" :class="[errors.has('name') ? 'has-error' : '']">
                <label class="required">{{ __('admin::app.settings.workflows.name') }}</label>

                <input
                    type="text"
                    name="name"
                    class="control"
                    value="{{ old('name') }}"
                    v-validate="'required'"
                    data-vv-as="{{ __('admin::app.settings.workflows.name') }}"
                />

                <span class="control-error" v-if="errors.has('name')">
                    @{{ errors.first('name') }}
                </span>
            </div>

            <div class="form-group">
                <label>{{ __('admin::app.settings.workflows.description') }}</label>

                <textarea name="description" class="control">{{ old('description') }}</textarea>
            </div>

            <div class="panel-separator"></div>

            <div class="workflow-panel">
                <div class="header">
                    <label>{{ __('admin::app.settings.workflows.event') }}</label>
                    <p>{{ __('admin::app.settings.workflows.event-info') }}</p>
                </div>

                <div class="form-group" :class="[errors.has('event') ? 'has-error' : '']">
                    <label class="required">{{ __('admin::app.settings.workflows.event') }}</label>

                    <input type="hidden" name="entity_type" :value="entityType"/>

                    <select name="event" class="control" v-validate="'required'" v-model="event">
                        <optgroup v-for='entity in events' :label="entity.name">
                            <option v-for='event in entity.events' :value="event.event">
                                @{{ event.name }}
                            </option>
                        </optgroup>
                    </select>

                    <span class="control-error" v-if="errors.has('event')">
                        @{{ errors.first('event') }}
                    </span>
                </div>
            </div>

            <div class="panel-separator"></div>

            <div class="workflow-panel">
                <div class="header">
                    <label>{{ __('admin::app.settings.workflows.conditions') }}</label>

                    <p>{{ __('admin::app.settings.workflows.condition-info') }}</p>
                </div>

                <div class="form-group">
                    <label for="condition_type">{{ __('admin::app.settings.workflows.condition-type') }}</label>

                    <select class="control" id="condition_type" name="condition_type" v-model="condition_type">
                        <option value="and">{{ __('admin::app.settings.workflows.all-conditions-true') }}</option>
                        <option value="or">{{ __('admin::app.settings.workflows.any-condition-true') }}</option>
                    </select>
                </div>

                <div class="table workflow-conditions" style="overflow-x: unset;">
                    <table>
                        <tbody>

                            <workflow-condition-item
                                v-for='(condition, index) in conditions'
                                :entityType="entityType"
                                :condition="condition"
                                :key="index"
                                :index="index"
                                @onRemoveCondition="removeCondition($event)">
                            </workflow-condition-item>

                        </tbody>
                    </table>
                </div>

                <button type="button" class="btn btn-md btn-primary" style="margin-top: 20px;" @click="addCondition">
                    {{ __('admin::app.settings.workflows.add-condition') }}
                </button>

            </div>

            <div class="panel-separator"></div>

            <div class="workflow-panel">
                <div class="header">
                    <label>{{ __('admin::app.settings.workflows.actions') }}</label>

                    <p>{{ __('admin::app.settings.workflows.action-info') }}</p>
                </div>
                
                <div class="table workflow-actions" style="overflow-x: unset;">
                    <table>
                        <tbody>

                            <workflow-action-item
                                v-for='(action, index) in actions'
                                :entityType="entityType"
                                :action="action"
                                :key="index"
                                :index="index"
                                @onRemoveAction="removeAction($event)">
                            </workflow-action-item>

                        </tbody>
                    </table>
                </div>

                <button type="button" class="btn btn-md btn-primary" style="margin-top: 20px;" @click="addAction">
                    {{ __('admin::app.settings.workflows.add-action') }}
                </button>
            </div>
        </div>

    </script>

    <script type="text/x-template" id="workflow-condition-item-template">
        <tr>
            <td class="attribute">
                <div class="form-group">
                    <select :name="['conditions[' + index + '][attribute]']" class="control" v-model="condition.attribute">
                        <option value="">{{ __('admin::app.settings.workflows.choose-condition-to-add') }}</option>

                        <option v-for='attribute in conditions[entityType]' :value="attribute.id">
                            @{{ attribute.name }}
                        </option>
                    </select>
                </div>
            </td>

            <td class="operator">
                <div class="form-group" v-if="matchedAttribute">
                    <select :name="['conditions[' + index + '][operator]']" class="control" v-model="condition.operator">
                        <option v-for='operator in condition_operators[matchedAttribute.type]' :value="operator.operator">
                            @{{ operator.name }}
                        </option>
                    </select>
                </div>
            </td>

            <td class="value">
                <div v-if="matchedAttribute">
                    <input type="hidden" :name="['conditions[' + index + '][attribute_type]']" v-model="matchedAttribute.type">

                    <div class="form-group" v-if="matchedAttribute.type == 'text' || matchedAttribute.type == 'price' || matchedAttribute.type == 'decimal' || matchedAttribute.type == 'integer' || matchedAttribute.type == 'email' || matchedAttribute.type == 'phone'">
                        <input class="control" :name="['conditions[' + index + '][value]']" v-model="condition.value"/>
                    </div>

                    <div class="form-group date" v-if="matchedAttribute.type == 'date'">
                        <date>
                            <input class="control" :name="['conditions[' + index + '][value]']" v-model="condition.value"/>
                        </date>
                    </div>

                    <div class="form-group date" v-if="matchedAttribute.type == 'datetime'">
                        <datetime>
                            <input class="control" :name="['conditions[' + index + '][value]']" v-model="condition.value"/>
                        </datetime>
                    </div>

                    <div class="form-group" v-if="matchedAttribute.type == 'boolean'">
                        <select :name="['conditions[' + index + '][value]']" class="control" v-model="condition.value">
                            <option value="1">{{ __('admin::app.settings.workflows.yes') }}</option>
                            <option value="0">{{ __('admin::app.settings.workflows.no') }}</option>
                        </select>
                    </div>

                    <div class="form-group" v-if="matchedAttribute.type == 'select' || matchedAttribute.type == 'radio' || matchedAttribute.type == 'lookup'">
                        <select :name="['conditions[' + index + '][value]']" class="control" v-model="condition.value" v-if="! matchedAttribute.lookup_type">
                            <option v-for='option in matchedAttribute.options' :value="option.id">
                                @{{ option.name }}
                            </option>
                        </select>

                        <lookup-component
                            :attribute="{'code': 'conditions[' + index + '][value]', 'name': 'Email', 'lookup_type': matchedAttribute.lookup_type}"
                            validations="required|email"
                            v-else
                        ></lookup-component>
                    </div>

                    <div class="form-group" v-if="matchedAttribute.type == 'multiselect' || matchedAttribute.type == 'checkbox'">
                        <select :name="['conditions[' + index + '][value][]']" class="control" v-model="condition.value" multiple>
                            <option v-for='option in matchedAttribute.options' :value="option.id">
                                @{{ option.name }}
                            </option>
                        </select>
                    </div>
                    
                </div>
            </td>

            <td class="actions">
                <i class="icon trash-icon" @click="removeCondition"></i>
            </td>
        </tr>
    </script>

    <script type="text/x-template" id="workflow-action-item-template">
        <tr>
            <td class="action">
                <div class="form-group">
                    <select :name="['actions[' + index + '][id]']" class="control" v-model="action.id">
                        <option value="">{{ __('admin::app.settings.workflows.choose-action-to-add') }}</option>
                        <option v-for='action in actions[entityType]' :value="action.id">
                            @{{ action.name }}
                        </option>
                    </select>
                </div>
            </td>

            <td class="attribute" v-if="matchedAction && matchedAction.attributes">
                <div class="form-group">
                    <select :name="['actions[' + index + '][attribute]']" class="control" v-model="action.attribute">
                        <option value="">{{ __('admin::app.settings.workflows.choose-attribute') }}</option>

                        <option v-for='attribute in matchedAction.attributes' :value="attribute.id">
                            @{{ attribute.name }}
                        </option>
                    </select>
                </div>

                <div class="form-group" v-if="matchedAttribute">
                    <input type="hidden" :name="['actions[' + index + '][attribute_type]']" v-model="matchedAttribute.type">

                    <div class="form-group" v-if="matchedAttribute.type == 'text' || matchedAttribute.type == 'price' || matchedAttribute.type == 'decimal' || matchedAttribute.type == 'integer'">
                        <input class="control" :name="['actions[' + index + '][value]']" v-model="action.value"/>
                    </div>

                    <div class="form-group" v-if="matchedAttribute.type == 'email' || matchedAttribute.type == 'phone'">
                        <input class="control" :name="['actions[' + index + '][value][0][value]']" v-model="action.value[0].value"/>
                        
                        <input type="hidden" class="control" :name="['actions[' + index + '][value][0][label]']" value="work"/>
                    </div>

                    <div class="form-group date" v-if="matchedAttribute.type == 'textarea'">
                        <textarea class="control" :name="['actions[' + index + '][value]']" v-model="action.value"></textarea>
                    </div>

                    <div class="form-group date" v-if="matchedAttribute.type == 'date'">
                        <date>
                            <input class="control" :name="['actions[' + index + '][value]']" v-model="action.value"/>
                        </date>
                    </div>

                    <div class="form-group date" v-if="matchedAttribute.type == 'datetime'">
                        <datetime>
                            <input class="control" :name="['actions[' + index + '][value]']" v-model="action.value"/>
                        </datetime>
                    </div>

                    <div class="form-group" v-if="matchedAttribute.type == 'boolean'">
                        <select :name="['actions[' + index + '][value]']" class="control" v-model="action.value">
                            <option value="1">{{ __('admin::app.settings.workflows.yes') }}</option>
                            <option value="0">{{ __('admin::app.settings.workflows.no') }}</option>
                        </select>
                    </div>

                    <div class="form-group" v-if="matchedAttribute.type == 'select' || matchedAttribute.type == 'radio' || matchedAttribute.type == 'lookup'">
                        <select :name="['actions[' + index + '][value]']" class="control" v-model="action.value">
                            <option v-for='option in matchedAttribute.options' :value="option.id">
                                @{{ option.name }}
                            </option>
                        </select>
                    </div>

                    <div class="form-group" v-if="matchedAttribute.type == 'multiselect' || matchedAttribute.type == 'checkbox'">
                        <select :name="['actions[' + index + '][value][]']" class="control" v-model="action.value" multiple>
                            <option v-for='option in matchedAttribute.options' :value="option.id">
                                @{{ option.name }}
                            </option>
                        </select>
                    </div>
                </div>
            </td>

            <td class="option" v-if="matchedAction && matchedAction.options">
                <div class="form-group">
                    <select :name="['actions[' + index + '][value]']" class="control" v-model="action.value">
                        <option value="">{{ __('admin::app.settings.workflows.choose-option') }}</option>
                        
                        <option v-for='option in matchedAction.options' :value="option.id">
                            @{{ option.name }}
                        </option>
                    </select>
                </div>
            </td>

            <td class="option" v-if="matchedAction && ! matchedAction.attributes && ! matchedAction.options">
                <div class="form-group">
                    <input type="text" :name="['actions[' + index + '][value]']" class="control" v-model="action.value">
                </div>
            </td>

            <td class="actions">
                <i class="icon trash-icon" @click="removeAction"></i>
            </td>
        </tr>
    </script>

    <script>
        Vue.component('workflow-component', {

            template: '#workflow-component-template',

            inject: ['$validator'],

            data: function() {
                return {
                    events: @json(app('\Webkul\Workflow\Helpers\Entity')->getEvents()),

                    event: '',

                    condition_type: 'and',

                    conditions: [],

                    actions: [],
                }
            },

            computed: {
                entityType: function () {
                    if (this.event == '') {
                        return '';
                    }

                    var entityType = '';

                    var self = this;

                    for (let id in this.events) {
                        this.events[id].events.forEach(function (eventTemp) {
                            if (eventTemp.event == self.event) {
                                entityType = id;
                            }
                        });
                    }

                    return entityType;
                }
            },

            watch: {
                entityType: function(newValue, oldValue) {
                    this.conditions = [];

                    this.actions = [];
                }
            },

            methods: {
                addCondition: function() {
                    this.conditions.push({
                        'attribute': '',
                        'operator': '==',
                        'value': '',
                    });
                },

                removeCondition: function(condition) {
                    let index = this.conditions.indexOf(condition)

                    this.conditions.splice(index, 1)
                },

                addAction: function() {
                    this.actions.push({
                        'id': '',
                        'attribute': '',
                        'value': '',
                    });
                },

                removeAction: function(action) {
                    let index = this.actions.indexOf(action)

                    this.actions.splice(index, 1)
                },

                onSubmit: function(e) {
                    this.$root.onSubmit(e)
                },
            }
        });

        Vue.component('workflow-condition-item', {

            template: '#workflow-condition-item-template',

            props: ['index', 'entityType', 'condition'],

            data: function() {
                return {
                    conditions: @json(app('\Webkul\Workflow\Helpers\Entity')->getConditions()),

                    condition_operators: {
                        'price': [{
                                'operator': '==',
                                'name': '{{ __('admin::app.settings.workflows.is-equal-to') }}'
                            }, {
                                'operator': '!=',
                                'name': '{{ __('admin::app.settings.workflows.is-not-equal-to') }}'
                            }, {
                                'operator': '>=',
                                'name': '{{ __('admin::app.settings.workflows.equals-or-greater-than') }}'
                            }, {
                                'operator': '<=',
                                'name': '{{ __('admin::app.settings.workflows.equals-or-less-than') }}'
                            }, {
                                'operator': '>',
                                'name': '{{ __('admin::app.settings.workflows.greater-than') }}'
                            }, {
                                'operator': '<',
                                'name': '{{ __('admin::app.settings.workflows.less-than') }}'
                            }],
                        'decimal': [{
                                'operator': '==',
                                'name': '{{ __('admin::app.settings.workflows.is-equal-to') }}'
                            }, {
                                'operator': '!=',
                                'name': '{{ __('admin::app.settings.workflows.is-not-equal-to') }}'
                            }, {
                                'operator': '>=',
                                'name': '{{ __('admin::app.settings.workflows.equals-or-greater-than') }}'
                            }, {
                                'operator': '<=',
                                'name': '{{ __('admin::app.settings.workflows.equals-or-less-than') }}'
                            }, {
                                'operator': '>',
                                'name': '{{ __('admin::app.settings.workflows.greater-than') }}'
                            }, {
                                'operator': '<',
                                'name': '{{ __('admin::app.settings.workflows.less-than') }}'
                            }],
                        'integer': [{
                                'operator': '==',
                                'name': '{{ __('admin::app.settings.workflows.is-equal-to') }}'
                            }, {
                                'operator': '!=',
                                'name': '{{ __('admin::app.settings.workflows.is-not-equal-to') }}'
                            }, {
                                'operator': '>=',
                                'name': '{{ __('admin::app.settings.workflows.equals-or-greater-than') }}'
                            }, {
                                'operator': '<=',
                                'name': '{{ __('admin::app.settings.workflows.equals-or-less-than') }}'
                            }, {
                                'operator': '>',
                                'name': '{{ __('admin::app.settings.workflows.greater-than') }}'
                            }, {
                                'operator': '<',
                                'name': '{{ __('admin::app.settings.workflows.less-than') }}'
                            }],
                        'text': [{
                                'operator': '==',
                                'name': '{{ __('admin::app.settings.workflows.is-equal-to') }}'
                            }, {
                                'operator': '!=',
                                'name': '{{ __('admin::app.settings.workflows.is-not-equal-to') }}'
                            }, {
                                'operator': '{}',
                                'name': '{{ __('admin::app.settings.workflows.contain') }}'
                            }, {
                                'operator': '!{}',
                                'name': '{{ __('admin::app.settings.workflows.does-not-contain') }}'
                            }],
                        'boolean': [{
                                'operator': '==',
                                'name': '{{ __('admin::app.settings.workflows.is-equal-to') }}'
                            }, {
                                'operator': '!=',
                                'name': '{{ __('admin::app.settings.workflows.is-not-equal-to') }}'
                            }],
                        'date': [{
                                'operator': '==',
                                'name': '{{ __('admin::app.settings.workflows.is-equal-to') }}'
                            }, {
                                'operator': '!=',
                                'name': '{{ __('admin::app.settings.workflows.is-not-equal-to') }}'
                            }, {
                                'operator': '>=',
                                'name': '{{ __('admin::app.settings.workflows.equals-or-greater-than') }}'
                            }, {
                                'operator': '<=',
                                'name': '{{ __('admin::app.settings.workflows.equals-or-less-than') }}'
                            }, {
                                'operator': '>',
                                'name': '{{ __('admin::app.settings.workflows.greater-than') }}'
                            }, {
                                'operator': '<',
                                'name': '{{ __('admin::app.settings.workflows.less-than') }}'
                            }],
                        'datetime': [{
                                'operator': '==',
                                'name': '{{ __('admin::app.settings.workflows.is-equal-to') }}'
                            }, {
                                'operator': '!=',
                                'name': '{{ __('admin::app.settings.workflows.is-not-equal-to') }}'
                            }, {
                                'operator': '>=',
                                'name': '{{ __('admin::app.settings.workflows.equals-or-greater-than') }}'
                            }, {
                                'operator': '<=',
                                'name': '{{ __('admin::app.settings.workflows.equals-or-less-than') }}'
                            }, {
                                'operator': '>',
                                'name': '{{ __('admin::app.settings.workflows.greater-than') }}'
                            }, {
                                'operator': '<',
                                'name': '{{ __('admin::app.settings.workflows.less-than') }}'
                            }],
                        'select': [{
                                'operator': '==',
                                'name': '{{ __('admin::app.settings.workflows.is-equal-to') }}'
                            }, {
                                'operator': '!=',
                                'name': '{{ __('admin::app.settings.workflows.is-not-equal-to') }}'
                            }],
                        'radio': [{
                                'operator': '==',
                                'name': '{{ __('admin::app.settings.workflows.is-equal-to') }}'
                            }, {
                                'operator': '!=',
                                'name': '{{ __('admin::app.settings.workflows.is-not-equal-to') }}'
                            }],
                        'multiselect': [{
                                'operator': '{}',
                                'name': '{{ __('admin::app.settings.workflows.contains') }}'
                            }, {
                                'operator': '!{}',
                                'name': '{{ __('admin::app.settings.workflows.does-not-contain') }}'
                            }],
                        'checkbox': [{
                                'operator': '{}',
                                'name': '{{ __('admin::app.settings.workflows.contains') }}'
                            }, {
                                'operator': '!{}',
                                'name': '{{ __('admin::app.settings.workflows.does-not-contain') }}'
                            }],
                        'email': [{
                                'operator': '{}',
                                'name': '{{ __('admin::app.settings.workflows.contains') }}'
                            }, {
                                'operator': '!{}',
                                'name': '{{ __('admin::app.settings.workflows.does-not-contain') }}'
                            }],
                        'phone': [{
                                'operator': '{}',
                                'name': '{{ __('admin::app.settings.workflows.contains') }}'
                            }, {
                                'operator': '!{}',
                                'name': '{{ __('admin::app.settings.workflows.does-not-contain') }}'
                            }],
                        'lookup': [{
                                'operator': '==',
                                'name': '{{ __('admin::app.settings.workflows.is-equal-to') }}'
                            }, {
                                'operator': '!=',
                                'name': '{{ __('admin::app.settings.workflows.is-not-equal-to') }}'
                            }],
                    }
                }
            },

            computed: {
                matchedAttribute: function () {
                    if (this.condition.attribute == '') {
                        return;
                    }

                    var self = this;

                    matchedAttribute = this.conditions[this.entityType].filter(function (attribute) {
                        return attribute.id == self.condition.attribute;
                    });

                    if (matchedAttribute[0]['type'] == 'multiselect' || matchedAttribute[0]['type'] == 'checkbox') {
                        this.condition.operator = '{}';

                        this.condition.value = [];
                    } else if (matchedAttribute[0]['type'] == 'email' || matchedAttribute[0]['type'] == 'phone') {
                        this.condition.operator = '{}';
                    }

                    return matchedAttribute[0];
                }
            },

            methods: {
                removeCondition: function() {
                    this.$emit('onRemoveCondition', this.condition)
                },
            }
        });

        Vue.component('workflow-action-item', {

            template: '#workflow-action-item-template',

            props: ['index', 'entityType', 'action'],

            data: function() {
                return {
                    actions: @json(app('\Webkul\Workflow\Helpers\Entity')->getActions()),
                }
            },

            computed: {
                matchedAction: function () {
                    if (this.entityType == '') {
                        return;
                    }

                    var self = this;

                    matchedAction = this.actions[this.entityType].filter(function (action) {
                        return action.id == self.action.id;
                    });

                    return matchedAction[0];
                },

                matchedAttribute: function () {
                    if (! this.matchedAction) {
                        return;
                    }

                    var self = this;

                    matchedAttribute = this.matchedAction.attributes.filter(function (attribute) {
                        return attribute.id == self.action.attribute;
                    });

                    if (! matchedAttribute.length) {
                        return;
                    }

                    if (matchedAttribute[0]['type'] == 'multiselect' || matchedAttribute[0]['type'] == 'checkbox') {
                        this.action.value = [];
                    } else if (matchedAttribute[0]['type'] == 'email' || matchedAttribute[0]['type'] == 'phone') {
                        this.action.value = [{
                                'label': 'work',
                                'value': ''
                            }];
                    }else if (matchedAttribute[0]['type'] == 'text') {
                        this.action.value = '';
                    }

                    return matchedAttribute[0];
                }
            },

            methods: {
                removeAction: function() {
                    this.$emit('onRemoveAction', this.action)
                },
            }
        });
    </script>

@endpush