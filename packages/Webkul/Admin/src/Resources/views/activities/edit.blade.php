@extends('admin::layouts.master')

@section('page_title')
    {{ __('admin::app.activities.edit-title') }}
@stop

@section('content-wrapper')
    <div class="content full-page adjacent-center">
        {!! view_render_event('admin.activities.edit.header.before', ['activity' => $activity]) !!}

        <div class="page-header">

            {{ Breadcrumbs::render('activities.edit', $activity) }}

            <div class="page-title">
                <h1>{{ __('admin::app.activities.edit-title') }}</h1>
            </div>
        </div>

        {!! view_render_event('admin.activities.edit.header.after', ['activity' => $activity]) !!}

        <form method="POST" action="{{ route('admin.activities.update', $activity->id) }}" @submit.prevent="onSubmit">

            <div class="page-content">
                <div class="form-container">

                    <div class="panel">
                        <div class="panel-header">
                            {!! view_render_event('admin.activities.edit.form_buttons.before', ['activity' => $activity]) !!}

                            <button type="submit" class="btn btn-md btn-primary">
                                {{ __('admin::app.activities.save-btn-title') }}
                            </button>

                            <a href="{{ route('admin.activities.index') }}">{{ __('admin::app.activities.back') }}</a>

                            {!! view_render_event('admin.activities.edit.form_buttons.after', ['activity' => $activity]) !!}
                        </div>
        
                        <div class="panel-body">
                            {!! view_render_event('admin.activities.edit.form_controls.before', ['activity' => $activity]) !!}

                            @csrf()

                            <input name="_method" type="hidden" value="PUT">
        
                            <div class="form-group" :class="[errors.has('title') ? 'has-error' : '']">
                                <label for="comment" class="required">{{ __('admin::app.activities.title-control') }}</label>

                                <input
                                    name="title"
                                    class="control"
                                    value="{{ old('title') ?: $activity->title }}"
                                    v-validate="'required'"
                                    data-vv-as="&quot;{{ __('admin::app.activities.title-control') }}&quot;"
                                />
        
                                <span class="control-error" v-if="errors.has('title')">@{{ errors.first('title') }}</span>
                            </div>
                
                            <div class="form-group" :class="[errors.has('type') ? 'has-error' : '']">
                                <label for="type" class="required">{{ __('admin::app.activities.type') }}</label>
        
                                <?php $selectedOption = old('type') ?: $activity->type ?>

                                <select
                                    name="type"
                                    class="control"
                                    v-validate="'required'"
                                    data-vv-as="&quot;{{ __('admin::app.activities.type') }}&quot;"
                                >
                                    <option value=""></option>

                                    <option value="call" {{ $selectedOption == 'call' ? 'selected' : '' }}>
                                        {{ __('admin::app.activities.call') }}
                                    </option>

                                    <option value="meeting" {{ $selectedOption == 'meeting' ? 'selected' : '' }}>
                                        {{ __('admin::app.activities.meeting') }}
                                    </option>

                                    <option value="lunch" {{ $selectedOption == 'lunch' ? 'selected' : '' }}>
                                        {{ __('admin::app.activities.lunch') }}
                                    </option>
                                </select>
        
                                <span class="control-error" v-if="errors.has('type')">@{{ errors.first('type') }}</span>
                            </div>
        
                            <div class="form-group date" :class="[errors.has('schedule_from') || errors.has('schedule_to') ? 'has-error' : '']">
                                <label for="schedule_from" class="required">{{ __('admin::app.activities.schedule') }}</label>
        
                                <div class="input-group">
                                    <datetime>
                                        <input
                                            type="text"
                                            name="schedule_from"
                                            class="control"
                                            value="{{ old('schedule_from') ?: $activity->schedule_from }}"
                                            placeholder="{{ __('admin::app.activities.from') }}" ref="schedule_from"
                                            v-validate="'required|date_format:yyyy-MM-dd HH:mm:ss|after:{{\Carbon\Carbon::now()->format('Y-m-d H:i:s')}}'"
                                            data-vv-as="&quot;{{ __('admin::app.activities.from') }}&quot;"
                                        >
        
                                        <span class="control-error" v-if="errors.has('schedule_from')">@{{ errors.first('schedule_from') }}</span>
                                    </datetime>
        
                                    <datetime>
                                        <input
                                            type="text"
                                            name="schedule_to"
                                            class="control"
                                            value="{{ old('schedule_to') ?: $activity->schedule_to }}"
                                            placeholder="{{ __('admin::app.activities.to') }}" ref="schedule_to"
                                            v-validate="'required|date_format:yyyy-MM-dd HH:mm:ss|after:schedule_from'"
                                            data-vv-as="&quot;{{ __('admin::app.activities.to') }}&quot;"
                                        >
        
                                        <span class="control-error" v-if="errors.has('schedule_to')">@{{ errors.first('schedule_to') }}</span>
                                    </datetime>
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="location">{{ __('admin::app.activities.location') }}</label>

                                <input name="location" class="control" value="{{ old('location') ?: $activity->location }}"/>
                            </div>

                            <div class="form-group video-conference">
                            </div>
        
                            <div class="form-group">
                                <label for="comment">{{ __('admin::app.activities.description') }}</label>
                                <textarea class="control" id="activity-comment" name="comment">{{ old('comment') ?: $activity->comment }}</textarea>
                            </div>
        
                            <div class="form-group">
                                <label for="participants">{{ __('admin::app.activities.participants') }}</label>
        
                                <multi-lookup-component :data='@json($activity->participants)'></multi-lookup-component>
                            </div>

                            <div class="form-group">
                                <label for="validation">{{ __('admin::app.activities.lead') }}</label>

                                @include('admin::common.custom-attributes.edit.lookup')

                                @php
                                    $lookUpEntityData = app('Webkul\Attribute\Repositories\AttributeRepository')
                                        ->getLookUpEntity(
                                            'leads',
                                            old('lead_id')
                                                ?: (
                                                    ($lead = $activity->leads()->first())
                                                    ? $lead->id
                                                    : null
                                                )
                                            );
                                @endphp

                                <lookup-component
                                    :attribute="{'code': 'lead_id', 'name': 'Lead', 'lookup_type': 'leads'}"
                                    :data='@json($lookUpEntityData)'
                                ></lookup-component>
                            </div>

                            {!! view_render_event('admin.activities.edit.form_controls.after', ['activity' => $activity]) !!}

                        </div>
                    </div>

                </div>

            </div>

        </form>

    </div>
@stop

@push('scripts')

    <script type="text/x-template" id="multi-lookup-component-template">
        <div class="lookup-control">
            <div class="form-group" style="margin-bottom: 0">
                <input type="text" class="control" v-model="search_term" v-on:keyup="search" autocomplete="off" placeholder="{{ __('admin::app.activities.typing-placeholder') }}">

                <div class="lookup-results grouped" v-if="search_term.length">
                    <label>{{ __('admin::app.leads.users') }}</label>

                    <ul>
                        <li v-for='(participant, index) in searched_participants.users' @click="addParticipant('users', participant)">
                            <span>@{{ participant.name }}</span>
                        </li>

                        <li v-if='! searched_participants.users.length && search_term.length && ! is_searching'>
                            <span>{{ __('admin::app.common.no-result-found') }}</span>
                        </li>
                    </ul>

                    <label>{{ __('admin::app.leads.persons') }}</label>

                    <ul>
                        <li v-for='(participant, index) in searched_participants.persons' @click="addParticipant('persons', participant)">
                            <span>@{{ participant.name }}</span>
                        </li>

                        <li v-if='! searched_participants.persons.length && search_term.length && ! is_searching'>
                            <span>{{ __('admin::app.common.no-result-found') }}</span>
                        </li>
                    </ul>
                </div>

                <i class="icon loader-active-icon" v-if="is_searching"></i>
            </div>

            <div class="lookup-selected-options">
                <span class="badge badge-sm badge-pill badge-secondary-outline users" v-for='(participant, index) in participants.users'>
                    <input type="hidden" name="participants[users][]" :value="participant.id"/>
                    @{{ participant.name }}
                    <i class="icon close-icon"  @click="removeParticipant('users', participant)"></i>
                </span>

                <span class="badge badge-sm badge-pill badge-warning-outline persons" v-for='(participant, index) in participants.persons'>
                    <input type="hidden" name="participants[persons][]" :value="participant.id"/>
                    @{{ participant.name }}
                    <i class="icon close-icon"  @click="removeParticipant('persons', participant)"></i>
                </span>
            </div>
        </div>
    </script>

    <script>
        Vue.component('multi-lookup-component', {

            template: '#multi-lookup-component-template',
    
            props: ['data'],

            inject: ['$validator'],

            data: function () {
                return {
                    search_term: '',

                    is_searching: false,

                    searched_participants: {
                        'users': [],

                        'persons': []
                    },

                    participants: {
                        'users': [],

                        'persons': []
                    },
                }
            },

            mounted: function() {
                var self = this;

                this.data.forEach(function(participant) {
                    if (participant.user) {
                        self.participants.users.push({'id': participant.user.id, 'name': participant.user.name});
                    } else {
                        self.participants.persons.push({'id': participant.person.id, 'name': participant.person.name});
                    }
                });
            },

            methods: {
                search: debounce(function () {
                    this.is_searching = true;

                    if (this.search_term.length < 2) {
                        this.searched_participants = {
                            'users': [],

                            'persons': []
                        };

                        this.is_searching = false;

                        return;
                    }

                    this.$http.get("{{ route('admin.activities.search_participants') }}", {params: {query: this.search_term}})
                        .then (response => {
                            var self = this;
                            
                            ['users', 'persons'].forEach(function(userType) {
                                if (self.participants[userType].length) {
                                    self.participants[userType].forEach(function(addedUser) {
                                        
                                        response.data[userType].forEach(function(user, index) {
                                            if (user.id == addedUser.id) {
                                                response.data[userType].splice(index, 1);
                                            }
                                        });

                                    })
                                }
                            })

                            this.searched_participants = response.data;

                            this.is_searching = false;
                        })
                        .catch (error => {
                            this.is_searching = false;
                        })
                }, 500),

                addParticipant: function(type, participant) {
                    this.search_term = '';

                    this.searched_participants = {
                        'users': [],

                        'persons': []
                    };

                    this.participants[type].push(participant);
                },

                removeParticipant: function(type, participant) {
                    const index = this.participants[type].indexOf(participant);

                    Vue.delete(this.participants[type], index);
                }
            }
        });
    </script>

@endpush