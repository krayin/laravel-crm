@extends('admin::layouts.master')

@section('page_title')
    {{ $lead->title }}
@stop

@section('css')
    <style>
        .modal-container .modal-header {
            border: 0;
        }

        .modal-container .modal-body {
            padding: 0;
        }
    </style>
@stop

@section('content-wrapper')

    <div class="content full-page">
        <div class="page-header">
            <div class="page-title">
                <h1>{{ $lead->title }}</h1>

                <div class="page-action">
                    <button class="btn btn-primary btn-md" @click="openModal('updateLeadModal')">Edit</button>
                </div>
            </div>
        </div>

        <div class="page-content lead-view">
            
            <div class="lead-content-left">
                <div class="panel">
                    <div class="panel-header">
                        {{ __('admin::app.leads.details') }}
                    </div>
    
                    <div class="panel-body">
                        
                        <div class="custom-attribute-view">
                            @include('admin::common.custom-attributes.view', [
                                'customAttributes' => app('Webkul\Attribute\Repositories\AttributeRepository')->findWhere([
                                    'entity_type' => 'leads',
                                ]),
                                'entity'           => $lead,
                            ])
                        </div>

                    </div>
                </div>

                <div class="panel">
                    <div class="panel-header">
                        {{ __('admin::app.leads.contact-person') }}
                    </div>
    
                    <div class="panel-body custom-attribute-view">
                        
                        <div class="attribute-value-row">
                            <div class="label">Name</div>
                
                            <div class="value">
                                <a href="{{ route('admin.contacts.persons.edit', $lead->person->id) }}" target="_blank">
                                    {{ $lead->person->name }}
                                </a>
                            </div>
                        </div>
                        
                        <div class="attribute-value-row">
                            <div class="label">Email</div>
                
                            <div class="value">
                                @include ('admin::common.custom-attributes.view.email', ['value' => $lead->person->emails])
                            </div>
                        </div>
                        
                        <div class="attribute-value-row">
                            <div class="label">Contact Numbers</div>
                
                            <div class="value">
                                @include ('admin::common.custom-attributes.view.phone', ['value' => $lead->person->contact_numbers])
                            </div>
                        </div>
                        
                        <div class="attribute-value-row">
                            <div class="label">Organization</div>
                
                            <div class="value">
                                @if ($lead->person->organization)
                                    <a href="{{ route('admin.contacts.organizations.edit', $lead->person->organization->id) }}" target="_blank">
                                        {{ $lead->person->organization->name }}
                                    </a>
                                @else
                                    {{ __('admin::app.common.not-available') }}
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <div class="panel">
                    <div class="panel-header">
                        {{ __('admin::app.leads.products') }}
                    </div>
    
                    <div class="panel-body" style="position: relative">
                        @include('admin::leads.common.products')

                        <product-list :data='@json($lead->products)'></product-list>

                        <button type="submit" class="btn btn-md btn-primary" style="position: absolute;right: 25px;bottom: 11px;">
                            Save
                        </button>
                    </div>
                </div>
            </div>

            <div class="lead-content-right">

                <activity-action-component></activity-action-component>

                <activity-list-component></activity-list-component>
            </div>

        </div>
    </div>

    <form action="{{ route('admin.leads.update', $lead->id) }}" method="post" @submit.prevent="onSubmit">
        <modal id="updateLeadModal" :is-open="modalIds.updateLeadModal">
            <h3 slot="header-title">{{ __('admin::app.leads.edit-title') }}</h3>
            
            <div slot="header-actions">
                <button class="btn btn-sm btn-secondary-outline" @click="closeModal('updateLeadModal')">{{ __('admin::app.leads.cancel') }}</button>

                <button class="btn btn-sm btn-primary">{{ __('admin::app.leads.save-btn-title') }}</button>
            </div>

            <div slot="body">
                @csrf()
                
                <input name="_method" type="hidden" value="PUT">

                <tabs>
                    <tab name="{{ __('admin::app.leads.details') }}" :selected="true">
                        @include('admin::common.custom-attributes.edit', [
                            'customAttributes' => app('Webkul\Attribute\Repositories\AttributeRepository')->findWhere([
                                'entity_type' => 'leads',
                            ]),
                            'entity'           => $lead,
                        ])
                    </tab>

                    <tab name="{{ __('admin::app.leads.contact-person') }}">
                        @include('admin::leads.common.contact')

                        <contact-component :data='@json($lead->person)'></contact-component>
                    </tab>

                    <tab name="{{ __('admin::app.leads.products') }}">
                        @include('admin::leads.common.products')

                        <product-list :data='@json($lead->products)'></product-list>
                    </tab>
                </tabs>
            </div>
        </modal>
    </form>
@stop

@push('scripts')
    <script type="text/x-template" id="activity-action-component-template">
        <tabs>
            <tab name="{{ __('admin::app.leads.note') }}" :selected="true">
                <form action="{{ route('admin.leads.activities.store', $lead->id) }}" method="post" data-vv-scope="note-form" @submit.prevent="onSubmit($event, 'note-form')">

                    <input type="hidden" name="type" value="note">
                    @csrf()

                    <div class="form-group" :class="[errors.has('note-form.comment') ? 'has-error' : '']">
                        <label for="comment" class="required">{{ __('admin::app.leads.note') }}</label>
                        <textarea v-validate="'required'" class="control" id="comment" name="comment" data-vv-as="&quot;{{ __('admin::app.leads.note') }}&quot;">{{ old('comment') }}</textarea>
                        <span class="control-error" v-if="errors.has('note-form.comment')">@{{ errors.first('note-form.comment') }}</span>
                    </div>

                    <button type="submit" class="btn btn-md btn-primary">
                        {{ __('admin::app.leads.save') }}
                    </button>

                </form>
            </tab>

            <tab name="{{ __('admin::app.leads.activity') }}">
                <form action="{{ route('admin.leads.activities.store', $lead->id) }}" method="post" data-vv-scope="activity-form" @submit.prevent="onSubmit($event, 'activity-form')">

                    @csrf()

                    <div class="form-group" :class="[errors.has('activity-form.type') ? 'has-error' : '']">
                        <label for="type" class="required">{{ __('admin::app.leads.type') }}</label>

                        <select v-validate="'required'" class="control" name="type" data-vv-as="&quot;{{ __('admin::app.leads.type') }}&quot;">
                            <option value=""></option>
                            <option value="call">{{ __('admin::app.leads.call') }}</option>
                            <option value="meeting">{{ __('admin::app.leads.meeting') }}</option>
                            <option value="lunch">{{ __('admin::app.leads.lunch') }}</option>
                            <option value="email">{{ __('admin::app.leads.email') }}</option>
                        </select>

                        <span class="control-error" v-if="errors.has('activity-form.type')">@{{ errors.first('activity-form.type') }}</span>
                    </div>

                    <div class="form-group">
                        <label for="comment">{{ __('admin::app.leads.description') }}</label>
                        <textarea class="control" id="comment" name="comment">{{ old('comment') }}</textarea>
                    </div>

                    <div class="form-group" :class="[errors.has('activity-form.schedule_from') || errors.has('activity-form.schedule_to') ? 'has-error' : '']">
                        <label for="schedule_from" class="required">{{ __('admin::app.leads.schedule') }}</label>

                        <div class="input-group">
                            <datetime>
                                <input type="text" name="schedule_from" class="control" v-validate="'required'" data-vv-as="&quot;{{ __('admin::app.leads.from') }}&quot;" placeholder="{{ __('admin::app.leads.from') }}">

                                <span class="control-error" v-if="errors.has('activity-form.schedule_from')">@{{ errors.first('activity-form.schedule_from') }}</span>
                            </datetime>

                            <datetime>
                                <input type="text" name="schedule_to" class="control" v-validate="'required'" data-vv-as="&quot;{{ __('admin::app.leads.to') }}&quot;" placeholder="{{ __('admin::app.leads.to') }}">

                                <span class="control-error" v-if="errors.has('activity-form.schedule_to')">@{{ errors.first('activity-form.schedule_to') }}</span>
                            </datetime>
                        </div>

                    </div>

                    <button type="submit" class="btn btn-md btn-primary">
                        {{ __('admin::app.leads.save') }}
                    </button>

                </form>
            </tab>

            <tab name="{{ __('admin::app.leads.email') }}">
                <form action="" method="post" data-vv-scope="email-form" @submit.prevent="onSubmit($event, 'email-form')" enctype="multipart/form-data">

                    @csrf()

                    <div class="form-group" :class="[errors.has('email-form.to') ? 'has-error' : '']">
                        <label for="to" class="required">{{ __('admin::app.leads.to') }}</label>
                        <input type="text" v-validate="'required'" class="control" id="to" name="to" data-vv-as="&quot;{{ __('admin::app.leads.to') }}&quot;">
                        <span class="control-error" v-if="errors.has('email-form.to')">@{{ errors.first('email-form.to') }}</span>
                    </div>
                    
                    <div class="form-group" :class="[errors.has('email-form.subject') ? 'has-error' : '']">
                        <label for="subject" class="required">{{ __('admin::app.leads.subject') }}</label>
                        <input type="text" v-validate="'required'" class="control" id="subject" name="subject" data-vv-as="&quot;{{ __('admin::app.leads.subject') }}&quot;">
                        <span class="control-error" v-if="errors.has('email-form.subject')">@{{ errors.first('email-form.subject') }}</span>
                    </div>

                </form>
            </tab>

            <tab name="{{ __('admin::app.leads.files') }}">
                <form action="{{ route('admin.leads.file_upload', $lead->id) }}" method="post" data-vv-scope="file-form" @submit.prevent="onSubmit($event, 'file-form')" enctype="multipart/form-data">

                    <input type="hidden" name="type" value="file">
                    @csrf()

                    <div class="form-group">
                        <label for="name">{{ __('admin::app.leads.name') }}</label>
                        <input type="text" class="control" id="name" name="name">
                    </div>

                    <div class="form-group" :class="[errors.has('file-form.file') ? 'has-error' : '']">
                        <label for="file" class="required">{{ __('admin::app.leads.file') }}</label>
                        <input type="file" v-validate="'required'" class="control" id="file" name="file" data-vv-as="&quot;{{ __('admin::app.leads.file') }}&quot;">
                        <span class="control-error" v-if="errors.has('file-form.file')">@{{ errors.first('file-form.file') }}</span>
                    </div>

                    <button type="submit" class="btn btn-md btn-primary">
                        {{ __('admin::app.leads.upload') }}
                    </button>

                </form>
            </tab>
        </tabs>
    </script>

    <script type="text/x-template" id="activity-list-component-template">
        <tabs class="activity-list">
            <tab v-for="type in types" :name="typeLabels[type]" :key="type" :selected="type == 'all'">

                <div class="planned-activities">
                    <div class="section-tag">
                        <span class="">{{ __('admin::app.leads.planned') }}</span>
                        <hr/>
                    </div>

                    <div class="activity-item" v-for="activity in activities[type]">
                        <div class="title">
                            Meeting scheduled on 30 March 2021

                            <span class="icon ellipsis-icon dropdown-toggle"></span>

                            <div class="dropdown-list">
                                <div class="dropdown-container">
                                    <ul>
                                        <li>
                                            {{ __('admin::app.leads.mark-as-done') }}
                                        </li>
                                        <li>
                                            
                                            {{ __('admin::app.leads.edit') }}
                                        </li>
                                        <li>
                                            {{ __('admin::app.leads.remove') }}
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>

                        <div class="comment" v-if="activity.comment">
                            @{{ activity.comment }}
                        </div>

                        <div class="info">
                            25 March 2021
                            <span class="seperator">·</span>
                            <a>Jitendra Singh</a> 
                        </div>
                    </div>
                </div>

                <div class="done-activities">
                    <div class="section-tag">
                        <span class="">{{ __('admin::app.leads.done') }}</span>
                        <hr/>
                    </div>

                    <div class="activity-item">
                        <div class="title">
                            Note added

                            <span class="icon ellipsis-icon"></span>
                        </div>

                        <div class="comment">
                            Agent Sushil Kumar updated agent from UnAssigned to Archana Tiwari
                        </div>

                        <div class="info">
                            25 March 2021
                            <span class="seperator">·</span>
                            <a>Jitendra Singh</a> 
                        </div>
                    </div>
                </div>
            </tab>
        </tabs>
    </script>

    <script>
        Vue.component('activity-action-component', {

            template: '#activity-action-component-template',
    
            props: ['data'],

            inject: ['$validator'],

            methods: {
                onSubmit: function(e, formScope) {
                    this.$validator.validateAll(formScope).then(function (result) {
                        if (result) {
                            e.target.submit();
                        }
                    });
                }
            }
        });

        Vue.component('activity-list-component', {

            template: '#activity-list-component-template',
    
            inject: ['$validator'],

            data: function () {
                return {
                    data: @json($lead->activities),

                    types: ['all', 'note', 'call', 'meeting', 'lunch', 'email', 'file'],

                    typeLabels: {
                        'all': "{{ __('admin::app.leads.all') }}",

                        'note': "{{ __('admin::app.leads.notes') }}",

                        'call': "{{ __('admin::app.leads.calls') }}",
                        
                        'meeting': "{{ __('admin::app.leads.meetings') }}",

                        'lunch': "{{ __('admin::app.leads.lunches') }}",

                        'email': "{{ __('admin::app.leads.emails') }}",

                        'file': "{{ __('admin::app.leads.files') }}",
                    },

                    activities: {}
                }
            },

            created: function() {
                this.types.forEach(type => {
                    if (type == 'all') {
                        this.activities['all'] = this.data;
                    } else {
                        this.activities[type] = this.data.filter(activity => activity.type == type);
                    }
                });
            }
        });
    </script>
@endpush