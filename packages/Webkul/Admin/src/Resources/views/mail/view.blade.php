@extends('admin::layouts.master')

@section('page_title')
    {{ $email->subject }}
@stop

@section('css')
    <style>
        .lead-form .modal-container .modal-header {
            border: 0;
        }

        .lead-form .modal-container .modal-body {
            padding: 0;
        }
    </style>
@stop

@section('content-wrapper')

    @php
        if (! $email->lead) {
            $email->lead = app('\Webkul\Lead\Repositories\LeadRepository')->getModel()->fill(['title' => $email->subject]);
        }

        if (! $email->person) {
            $email->person = app('\Webkul\Contact\Repositories\PersonRepository')->getModel()->fill(['emails' => [['value' => $email->from, 'label' => 'work']], 'name' => $email->name]);
        }
    @endphp

    <div class="content full-page">

        {!! view_render_event('admin.mail.view.header.before', ['email' => $email]) !!}

        <div class="page-header">
            
            {{ Breadcrumbs::render('mail.route.view', request('route'), $email) }}

            <div class="page-title">
                <h1>{{ $email->subject }}</h1>
            </div>

            <div class="page-action">

                {!! view_render_event('admin.mail.view.actions.before', ['email' => $email]) !!}

                <email-action-component></email-action-component>

                {!! view_render_event('admin.mail.view.actions.after', ['email' => $email]) !!}
            </div>
        </div>

        {!! view_render_event('admin.mail.view.header.after', ['email' => $email]) !!}


        <div class="page-content" style="margin-top: 30px; padding-bottom: 30px;">

            {!! view_render_event('admin.mail.view.list.before', ['email' => $email]) !!}

            <email-list-component></email-list-component>

            {!! view_render_event('admin.mail.view.list.after', ['email' => $email]) !!}

        </div>
    </div>

    <form
        action="{{ route('admin.contacts.persons.store') }}"
        method="post"
        data-vv-scope="person-form"
        @submit.prevent="onSubmit($event, 'person-form')"
        enctype="multipart/form-data"
    >

        <modal id="addPersonModal" :is-open="modalIds.addPersonModal">
            <h3 slot="header-title">{{ __('admin::app.contacts.persons.create-title') }}</h3>
            
            <div slot="header-actions">
                {!! view_render_event('admin.mail.view.actions.persons.create.form_buttons.before', ['email' => $email]) !!}

                <button class="btn btn-sm btn-secondary-outline" @click="closeModal('addPersonModal')">
                    {{ __('admin::app.contacts.persons.cancel') }}
                </button>

                <button class="btn btn-sm btn-primary">
                    {{ __('admin::app.contacts.persons.save-btn-title') }}
                </button>

                {!! view_render_event('admin.mail.view.actions.persons.create.form_buttons.after', ['email' => $email]) !!}
            </div>

            <div slot="body">
                {!! view_render_event('admin.mail.view.actions.persons.create.form_controls.before', ['email' => $email]) !!}

                @csrf()
                
                <input type="hidden" name="email_id" value="{{ $email->id }}" />
                
                <input type="hidden" name="quick_add" value="1"/>

                @include('admin::common.custom-attributes.edit', [
                    'customAttributes' => app('Webkul\Attribute\Repositories\AttributeRepository')->findWhere([
                        'entity_type' => 'persons',
                        'quick_add'   => 1
                    ]),
                    'entity'           => $email->person,
                    'formScope'        => 'person-form.',
                ])

                {!! view_render_event('admin.mail.view.actions.persons.create.form_controls.after', ['email' => $email]) !!}
            </div>
        </modal>

    </form>

    <form
        action="{{ route('admin.leads.store') }}"
        method="post"
        class="lead-form"
        data-vv-scope="lead-form"
        @submit.prevent="onSubmit($event, 'lead-form')"
    >

        <modal id="addLeadModal" :is-open="modalIds.addLeadModal">
            <h3 slot="header-title">{{ __('admin::app.leads.create-title') }}</h3>
            
            <div slot="header-actions">
                {!! view_render_event('admin.mail.view.actions.leads.create.form_buttons.before', ['email' => $email]) !!}

                <button class="btn btn-sm btn-secondary-outline" @click="closeModal('addLeadModal')">
                    {{ __('admin::app.leads.cancel') }}
                </button>

                <button class="btn btn-sm btn-primary">
                    {{ __('admin::app.leads.save-btn-title') }}
                </button>

                {!! view_render_event('admin.mail.view.actions.leads.create.form_buttons.after', ['email' => $email]) !!}
            </div>

            <div slot="body" style="padding: 0">
                {!! view_render_event('admin.mail.view.actions.leads.create.form_controls.before', ['email' => $email]) !!}

                @csrf()
                
                <input type="hidden" name="email_id" value="{{ $email->id }}" />

                <input type="hidden" name="quick_add" value="1" />

                <input type="hidden" id="lead_pipeline_stage_id" name="lead_pipeline_stage_id" value="1" />

                <tabs>
                    {!! view_render_event('admin.mail.view.actions.leads.create.form_controls.details.before', ['email' => $email]) !!}

                    <tab name="{{ __('admin::app.leads.details') }}" :selected="true">
                        @include('admin::common.custom-attributes.edit', [
                            'customAttributes' => app('Webkul\Attribute\Repositories\AttributeRepository')->findWhere([
                                'entity_type' => 'leads',
                                'quick_add'   => 1
                            ]),
                            'formScope'        => 'lead-form.',
                            'entity'           => $email->lead,
                        ])
                    </tab>

                    {!! view_render_event('admin.mail.view.actions.leads.create.form_controls.details.after', ['email' => $email]) !!}


                    {!! view_render_event('admin.mail.view.actions.leads.create.form_controls.contact_person.before', ['email' => $email]) !!}

                    <tab name="{{ __('admin::app.leads.contact-person') }}">
                        @include('admin::leads.common.contact', ['formScope' => 'lead-form.'])

                        <contact-component :data='@json(old('person'))'></contact-component>
                    </tab>

                    {!! view_render_event('admin.mail.view.actions.leads.create.form_controls.contact_person.after', ['email' => $email]) !!}


                    {!! view_render_event('admin.mail.view.actions.leads.create.form_controls.products.before', ['email' => $email]) !!}

                    <tab name="{{ __('admin::app.leads.products') }}">
                        @include('admin::leads.common.products', ['formScope' => 'lead-form.'])

                        <product-list :data='@json(old('products'))'></product-list>
                    </tab>

                    {!! view_render_event('admin.mail.view.actions.leads.create.form_controls.products.after', ['email' => $email]) !!}
                </tabs>

                {!! view_render_event('admin.mail.view.actions.leads.create.form_controls.after', ['email' => $email]) !!}
            </div>
        </modal>

    </form>
@stop

@push('scripts')

    <script src="{{ asset('vendor/webkul/admin/assets/js/tinyMCE/tinymce.min.js') }}"></script>

    <script type="text/x-template" id="email-action-component-template">
        <div class="email-action-container">
            <button class="btn btn-sm btn-secondary-outline" @click="show_filter = ! show_filter">
                <i class="icon link-icon"></i>

                <span>{{ __('admin::app.mail.link-mail') }}</span>
            </button>

            <div class="sidebar-filter" :class="{show: show_filter}">
                <header>
                    <h1>
                        <span>{{ __('admin::app.mail.link-mail') }}</span>

                        <div class="right">
                            <i class="icon close-icon" @click="show_filter = ! show_filter"></i>
                        </div>
                    </h1>
                </header>

                <div class="email-action-content">
                    {!! view_render_event('admin.mail.view.actions.link_person.before', ['email' => $email]) !!}

                    <div class="panel">
                        <div class="link-lead" v-if="! email.person_id">
                            <h3>{{ __('admin::app.mail.link-mail') }}</h3>

                            <div class="btn-group">
                                <button
                                    class="btn btn-sm btn-primary-outline"
                                    @click="enabled_search.person = true"
                                    v-if="! enabled_search.person"
                                >
                                    {{ __('admin::app.mail.add-to-existing-contact') }}
                                </button>

                                <div class="form-group" v-else>
                                    <input
                                        class="control"
                                        v-model="search_term.person"
                                        placeholder="{{ __('admin::app.mail.search-contact') }}"
                                        v-on:keyup="search('person')"
                                    />

                                    <div class="lookup-results" v-if="search_term.person.length">
                                        <ul>
                                            <li v-for='(result, index) in search_results.person' @click="link('person', result)">
                                                <span>@{{ result.name }}</span>
                                            </li>
                
                                            <li v-if='! search_results.person.length && search_term.person.length && ! is_searching.person'>
                                                <span>{{ __('admin::app.common.no-result-found') }}</span>
                                            </li>
                                        </ul>
                                    </div>

                                    <i class="icon close-icon"  v-if="! is_searching.person" @click="enabled_search.person = false; reset('person')"></i>

                                    <i class="icon loader-active-icon" v-if="is_searching.person"></i>
                                </div>

                                <button class="btn btn-sm btn-primary" @click="$root.openModal('addPersonModal')">
                                    {{ __('admin::app.mail.create-new-contact') }}
                                </button>
                            </div>
                        </div>

                        <div v-else>
                            <div class="panel-header">
                                {{ __('admin::app.mail.linked-contact') }}

                                <span class="links">
                                    <a :href="'{{ route('admin.contacts.persons.edit') }}/' + email.person_id" target="_blank">
                                        <i class="icon external-link-icon"></i>
                                    </a>

                                    <i class="icon close-icon" @click="unlink('person')"></i>
                                </span>
                            </div>

                            <div class="contact-details">
                                <div class="name">@{{ email.person.name }}</div>

                                <div class="email">
                                    <i class="icon emails-icon"></i>
                                    @{{ email.person.name }}
                                </div>
                            </div>
                        </div>
                    </div>

                    {!! view_render_event('admin.mail.view.actions.link_person.after', ['email' => $email]) !!}


                    {!! view_render_event('admin.mail.view.actions.link_lead.before', ['email' => $email]) !!}

                    <div class="panel">
                        <div class="link-lead" v-if="! email.lead_id">
                            <h3>{{ __('admin::app.mail.link-lead') }}</h3>

                            <div class="btn-group">
                                <button
                                    class="btn btn-sm btn-primary-outline"
                                    @click="enabled_search.lead = true"
                                    v-if="! enabled_search.lead"
                                >
                                    {{ __('admin::app.mail.link-to-existing-lead') }}
                                </button>

                                <div class="form-group" v-else>
                                    <input
                                        class="control"
                                        v-model="search_term.lead"
                                        placeholder="{{ __('admin::app.mail.search-lead') }}"
                                        v-on:keyup="search('lead')"
                                    />

                                    <div class="lookup-results" v-if="search_term.lead.length">
                                        <ul>
                                            <li v-for='(result, index) in search_results.lead' @click="link('lead', result)">
                                                <span>@{{ result.title }}</span>
                                            </li>
                
                                            <li v-if='! search_results.lead.length && search_term.lead.length && ! is_searching.lead'>
                                                <span>{{ __('admin::app.common.no-result-found') }}</span>
                                            </li>
                                        </ul>
                                    </div>

                                    <i
                                        class="icon close-icon"
                                        v-if="! is_searching.lead"
                                        @click="enabled_search.lead = false; reset('lead')"
                                    ></i>

                                    <i class="icon loader-active-icon" v-if="is_searching.lead"></i>
                                </div>

                                <button class="btn btn-sm btn-primary" @click="$root.openModal('addLeadModal')">
                                    {{ __('admin::app.mail.add-new-lead') }}
                                </button>
                            </div>
                        </div>

                        <div v-else>
                            <div class="panel-header">
                                {{ __('admin::app.mail.linked-lead') }}

                                <span class="links">
                                    <a :href="'{{ route('admin.leads.view') }}/' + email.lead_id" target="_blank">
                                        <i class="icon external-link-icon"></i>
                                    </a>

                                    <i class="icon close-icon" @click="unlink('lead')"></i>
                                </span>
                            </div>

                            <div class="panel-body">
                                <div class="custom-attribute-view" v-html="html">
                                </div>
                            </div>
                        </div>
                    </div>

                    {!! view_render_event('admin.mail.view.actions.link_lead.after', ['email' => $email]) !!}
                </div>
            </div>
        </div>
    </script>

    <script type="text/x-template" id="email-list-component-template">
        <div class="email-list">
            <email-item-component
                :email="email"
                :key="0"
                :index="0"
                @onEmailAction="emailAction($event)"
            ></email-item-component>


            <div class="email-reply-list">
                <email-item-component
                    v-for='(email, index) in email.emails'
                    :email="email"
                    :key="index + 1"
                    :index="index + 1"
                    @onEmailAction="emailAction($event)"
                ></email-item-component>
            </div>

            <div class="email-action" v-if="! action">
                <span class="reply-button" @click="emailAction({'type': 'reply'})">
                    <i class="icon reply-icon"></i>

                    {{ __('admin::app.mail.reply') }}
                </span>

                <span class="forward-button" @click="emailAction({'type': 'forward'})">
                    <i class="icon forward-icon"></i>

                    {{ __('admin::app.mail.forward') }}
                </span>
            </div>

            <div class="email-form-container" id="email-form-container" v-else>
                <email-form
                    :action="action"
                    @onDiscard="discard($event)"
                ></email-form>
            </div>
        </div>
    </script>

    <script type="text/x-template" id="email-item-component-template">
        <div class="email-item">
            <div class="email-header">
                <div class="row">
                    <span class="label">
                        {{ __('admin::app.mail.from-') }}
                    </span>

                    <span class="value">
                        @{{ email.name + ' ' + email.from }}
                    </span>

                    <span class="time">
                        <timeago :datetime="email.created_at" :auto-update="60"></timeago>

                        <span class="icon ellipsis-icon dropdown-toggle"></span>

                        <div class="dropdown-list">
                            <div class="dropdown-container">
                                <ul>
                                    <li
                                        @mouseover="hovering = 'reply-white-icon'"
                                        @mouseout="hovering = ''"
                                        @click="emailAction('reply')"
                                    >
                                        <i class="icon reply-icon" :class="{'reply-white-icon': hovering == 'reply-white-icon'}"></i>

                                        {{ __('admin::app.mail.reply') }}
                                    </li>

                                    <li
                                        @mouseover="hovering = 'reply-all-white-icon'"
                                        @mouseout="hovering = ''"
                                        @click="emailAction('reply-all')"
                                    >
                                        <i class="icon reply-all-icon" :class="{'reply-all-white-icon': hovering == 'reply-all-white-icon'}"></i>

                                        {{ __('admin::app.mail.reply-all') }}
                                    </li>

                                    <li
                                        @mouseover="hovering = 'forward-white-icon'"
                                        @mouseout="hovering = ''"
                                        @click="emailAction('forward')"
                                    >
                                        <i class="icon forward-icon" :class="{'forward-white-icon': hovering == 'forward-white-icon'}"></i>

                                        {{ __('admin::app.mail.forward') }}
                                    </li>

                                    <li
                                        @mouseover="hovering = 'trash-white-icon'"
                                        @mouseout="hovering = ''"
                                        @click="emailAction('delete')"
                                    >
                                        <form :action="'{{ route('admin.mail.delete') }}/' + email.id" method="post" :ref="'form-' + email.id">
                                            @csrf()

                                            <input type="hidden" name="_method" value="DELETE"/>

                                            <i class="icon trash-icon" :class="{'trash-white-icon': hovering == 'trash-white-icon'}"></i>

                                            {{ __('admin::app.mail.delete') }}
                                        </form>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </span>
                </div>

                <div class="row">
                    <span class="label">
                        {{ __('admin::app.mail.to-') }}
                    </span>

                    <span class="value">
                        @{{ String(email.reply_to) }}
                    </span>
                </div>
                
                <div class="row" v-if="email.cc && email.cc.length">
                    <span class="label">
                        {{ __('admin::app.mail.cc-') }}
                    </span>

                    <span class="value">
                        @{{ String(email.cc) }}
                    </span>
                </div>
                
                <div class="row" v-if="email.bcc && email.bcc.length">
                    <span class="label">
                        {{ __('admin::app.mail.bcc-') }}
                    </span>

                    <span class="value">
                        @{{ String(email.bcc) }}
                    </span>
                </div>
            </div>

            <div class="email-content">
                <div v-html="email.reply"></div>

                <div class="attachment-list">
                    <div class="attachment-item" v-for='(attachment, index) in email.attachments'>
                        <a :href="'{{ route('admin.mail.attachment_download') }}/' + attachment.id">
                            <i class="icon attachment-icon"></i>

                            @{{ attachment.name }}
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </script>

    <script type="text/x-template" id="email-form-template">
        <form
            action="{{ route('admin.mail.store') }}"
            method="POST"
            enctype="multipart/form-data"
            data-vv-scope="email-form"
            @submit.prevent="$root.onSubmit($event, 'email-form')"
        >

            <div class="form-container">

                <div class="panel">
    
                    <div class="panel-body">
                        {!! view_render_event('admin.mail.view.email.create.form_controls.before', ['email' => $email]) !!}

                        @csrf()

                        <!--<input type="hidden" name="parent_id" :value="action.email.id"/>-->
                        <input type="hidden" name="parent_id" value="{{ request('id') }}"/>

                        @include ('admin::common.custom-attributes.edit.email-tags')

                        <div class="form-group email-control-group" :class="[errors.has('email-form.reply_to[]') ? 'has-error' : '']">
                            <label for="to" class="required">{{ __('admin::app.leads.to') }}</label>
    
                            <email-tags-component
                                control-name="reply_to[]"
                                control-label="{{ __('admin::app.leads.to') }}"
                                :validations="'required'"
                                :data="reply_to"
                            ></email-tags-component>
    
                            <span class="control-error" v-if="errors.has('email-form.reply_to[]')">
                                @{{ errors.first('email-form.reply_to[]') }}
                            </span>

                            <div class="email-address-options">
                                <label @click="show_cc = ! show_cc">
                                    {{ __('admin::app.leads.cc') }}
                                </label>

                                <label @click="show_bcc = ! show_bcc">
                                    {{ __('admin::app.leads.bcc') }}
                                </label>
                            </div>
                        </div>
    
                        <div
                            class="form-group email-control-group"
                            :class="[errors.has('email-form.cc[]') ? 'has-error' : '']"
                            v-if="show_cc"
                        >
                            <label for="cc">{{ __('admin::app.leads.cc') }}</label>
    
                            <email-tags-component
                                control-name="cc[]"
                                control-label="{{ __('admin::app.leads.cc') }}"
                                :data='cc'
                            ></email-tags-component>
    
                            <span class="control-error" v-if="errors.has('email-form.cc[]')">
                                @{{ errors.first('email-form.cc[]') }}
                            </span>
                        </div>
    
                        <div
                            class="form-group email-control-group"
                            :class="[errors.has('email-form.bcc[]') ? 'has-error' : '']"
                            v-if="show_bcc"
                        >
                            <label for="bcc">{{ __('admin::app.leads.bcc') }}</label>
    
                            <email-tags-component
                                control-name="bcc[]"
                                control-label="{{ __('admin::app.leads.bcc') }}"
                                :data='bcc'
                            ></email-tags-component>
    
                            <span class="control-error" v-if="errors.has('email-form.bcc[]')">
                                @{{ errors.first('email-form.bcc[]') }}
                            </span>
                        </div>
                        
                        <div class="form-group" :class="[errors.has('email-form.reply') ? 'has-error' : '']">
                            <label for="reply" class="required" style="margin-bottom: 10px">{{ __('admin::app.leads.reply') }}</label>

                            <textarea
                                name="reply"
                                class="control"
                                id="reply"
                                v-validate="'required'"
                                data-vv-as="&quot;{{ __('admin::app.leads.reply') }}&quot;"
                            >@{{ reply }}</textarea>

                            <span class="control-error" v-if="errors.has('email-form.reply')">
                                @{{ errors.first('email-form.reply') }}
                            </span>
                        </div>
    
                        <div class="form-group">
                            <attachment-wrapper></attachment-wrapper>
                        </div>

                        {!! view_render_event('admin.mail.view.email.create.form_controls.after', ['email' => $email]) !!}
                    </div>

                    <div class="panel-bottom">
                        {!! view_render_event('admin.mail.view.email.create.form_buttons.before', ['email' => $email]) !!}

                        <button type="submit" class="btn btn-md btn-primary">
                            <i class="icon email-send-icon"></i>
    
                            {{ __('admin::app.mail.send') }}
                        </button>
    
                        <label @click="discard">{{ __('admin::app.mail.discard') }}</label>

                        {!! view_render_event('admin.mail.view.email.create.form_buttons.after', ['email' => $email]) !!}
                    </div>
                </div>

            </div>

        </form>
    </script>

    <script>
        @php
            $html = $email->lead_id
                ? view('admin::common.custom-attributes.view', [
                    'customAttributes' => app('Webkul\Attribute\Repositories\AttributeRepository')->findWhere([
                            'entity_type' => 'leads',
                        ]),
                        'entity'       => $email->lead,
                    ])->render() 
                : '';
        @endphp

        Vue.component('email-action-component', {

            template: '#email-action-component-template',

            inject: ['$validator'],

            data: function () {
                return {
                    email: @json($email->getAttributes()),

                    show_filter: false,

                    html: `{!! $html !!}`,

                    is_searching: {
                        person: false,

                        lead: false
                    },

                    search_term: {
                        person: '',

                        lead: '',
                    },

                    search_routes: {
                        person: "{{ route('admin.contacts.persons.search') }}",

                        lead: "{{ route('admin.leads.search') }}",
                    },

                    search_results: {
                        person: [],

                        lead: [],
                    },

                    enabled_search: {
                        person: false,

                        lead: false,
                    }
                }
            },

            created: function() {
                @if ($email->person)
                    this.email.person = @json($email->person);
                @endif
            },

            mounted: function() {
                if (! Array.isArray(window.serverErrors)) {
                    var self = this;

                    if (("{{ old('lead_pipeline_stage_id') }}")) {
                        this.$root.openModal('addLeadModal');

                        setTimeout(() => {
                            self.$root.addServerErrors('lead-form');
                        });
                    } else {
                        this.$root.openModal('addPersonModal');

                        setTimeout(() => {
                            self.$root.addServerErrors('person-form');
                        });
                    }
                }
            },

            methods: {
                search: debounce(function (type) {
                    this.is_searching[type] = true;

                    if (this.search_term[type].length < 2) {
                        this.search_results[type] = [];

                        this.is_searching[type] = false;

                        return;
                    }

                    this.$http.get(this.search_routes[type], {params: {query: this.search_term[type]}})
                        .then (response => {
                            this.search_results[type] = response.data;

                            this.is_searching[type] = false;
                        })
                        .catch (error => {
                            this.is_searching[type] = false;
                        })
                }, 500),

                link: function(type, entity) {
                    var self = this;

                    var data = (type == 'person') ? {'person_id': entity.id} : {'lead_id': entity.id};

                    this.$http.put("{{ route('admin.mail.update', $email->id) }}", data)
                        .then (response => {
                            self.email[type] = entity;

                            if (type == 'lead') {
                                self.html = response.data.html;
                            }

                            self.email[type + '_id'] = entity.id;

                            self.reset(type);

                            window.flashMessages = [{'type': 'success', 'message': response.data.message}];

                            self.$root.addFlashMessages();
                        })
                        .catch (error => {})
                },

                unlink: function(type) {
                    var self = this;

                    var data = (type == 'person') ? {'person_id': null} : {'lead_id': null};

                    this.$http.put("{{ route('admin.mail.update', $email->id) }}", data)
                        .then (response => {
                            self.email[type] = self.email[type + '_id'] = null;

                            window.flashMessages = [{'type': 'success', 'message': response.data.message}];

                            self.$root.addFlashMessages();
                        })
                        .catch (error => {})
                },

                reset: function(type) {
                    this.search_term[type] = '';

                    this.search_results[type] = [];

                    this.is_searching[type] = false;
                }
            }
        });

        Vue.component('email-list-component', {

            template: '#email-list-component-template',

            props: ['data'],

            inject: ['$validator'],

            data: function () {
                return {
                    email: @json($email),

                    action: null,
                }
            },

            methods: {
                emailAction: function(event) {
                    this.action = event;

                    if (! this.action.email) {
                        this.action.email = this.lastEmail();
                    }

                    var self = this;

                    setTimeout(function() {
                        self.scrollBottom();
                    }, 0);
                },

                scrollBottom: function() {
                    var scrollBottom = $(window).scrollTop() + $(window).height();

                    $('html, body').scrollTop(scrollBottom);
                },

                lastEmail: function() {
                    if (this.email.emails === undefined || ! this.email.emails.length) {
                        return this.email;
                    }

                    return this.email.emails[this.email.emails.length - 1];
                },

                discard: function() {
                    this.action = null;
                }
            },
        });

        Vue.component('email-item-component', {

            template: '#email-item-component-template',

            props: ['index', 'email'],

            inject: ['$validator'],

            data: function () {
                return {
                    hovering: '',
                }
            },
  
            methods: {
                emailAction: function(type) {
                    if (type != 'delete') {
                        this.$emit('onEmailAction', {'type': type, 'email': this.email});
                    } else {
                        if (! confirm('{{ __('admin::app.common.delete-confirm') }}')) {
                            return;
                        }
                        
                        this.$refs['form-' + this.email.id].submit()
                    }
                },
            }
        });

        Vue.component('email-form', {

            template: '#email-form-template',

            props: ['action'],

            inject: ['$validator'],

            data: function () {
                return {
                    show_cc: false,

                    show_bcc: false,
                }
            },

            computed: {
                reply_to: function() {
                    if (this.action.type == 'forward') {
                        return [];
                    }

                    return [this.action.email.from];
                },

                cc: function() {
                    if (this.action.type != 'reply-all') {
                        return [];
                    }

                    return this.action.email.cc;
                },

                bcc: function() {
                    if (this.action.type != 'reply-all') {
                        return [];
                    }

                    return this.action.email.bcc;
                },

                reply: function() {
                    if (this.action.type == 'forward') {
                        return this.action.email.reply;
                    }

                    return '';
                }
            },

            mounted: function() {
                tinymce.remove('#reply');

                var self = this;

                tinymce.init({
                    selector: 'textarea#reply',

                    height: 200,

                    width: "100%",
                    
                    plugins: 'image imagetools media wordcount save fullscreen code table lists link hr',

                    toolbar1: 'formatselect | bold italic strikethrough forecolor backcolor link hr | alignleft aligncenter alignright alignjustify | numlist bullist outdent indent  | removeformat | code | table',

                    image_advtab: true,

                    setup: function(editor) {
                        editor.on('keyUp', function() {
                            self.$validator.validate('email-form.reply', this.getContent());
                        });
                    }
                });
            },

            methods: {
                discard: function() {
                    this.$emit('onDiscard');
                }
            }
        });
    </script>
@endpush