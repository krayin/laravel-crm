@extends('admin::layouts.master')

@section('page_title')
    {{ __('admin::app.mail.compose') }}
@stop

@section('content-wrapper')
    <div class="content full-page adjacent-center">

        {!! view_render_event('admin.mail.compose.header.before', ['email' => $email ?? null]) !!}

        <div class="page-header">
            
            {{ Breadcrumbs::render('mail.route', request('route')) }}

            <div class="page-title">
                <h1>{{ __('admin::app.mail.compose') }}</h1>
            </div>
        </div>

        {!! view_render_event('admin.mail.compose.header.after', ['email' => $email ?? null]) !!}

        <div class="page-content">

            <email-form></email-form>

        </div>

    </div>
@stop

@push('scripts')
    <script src="{{ asset('vendor/webkul/admin/assets/js/tinyMCE/tinymce.min.js') }}"></script>

    <script type="text/x-template" id="email-form-template">
        <form
            action="{{ isset($email) ? route('admin.mail.update', $email->id) : route('admin.mail.store') }}"
            method="POST"
            enctype="multipart/form-data"
            @submit.prevent="onSubmit"
        >

            <div class="form-container">

                <div class="panel">
                    <div class="panel-header">
                        {!! view_render_event('admin.mail.compose.form_buttons.before', ['email' => $email ?? null]) !!}

                        <button type="submit" class="btn btn-md btn-primary" @click="is_draft = 0">
                            <i class="icon email-send-icon"></i>

                            {{ __('admin::app.mail.send') }}
                        </button>

                        <input type="submit" value="{{ __('admin::app.mail.save-to-draft') }}" @click="is_draft = 1"/>

                        <a href="{{ route('admin.mail.index', ['route' => 'inbox']) }}">{{ __('admin::app.mail.back') }}</a>

                        {!! view_render_event('admin.mail.compose.form_buttons.after', ['email' => $email ?? null]) !!}
                    </div>
    
                    <div class="panel-body">
                        {!! view_render_event('admin.mail.compose.form_controls.before', ['email' => $email ?? null]) !!}

                        @csrf()

                        @if (isset($email))
                            <input name="_method" type="hidden" value="PUT">
                        @endif

                        <input type="hidden" name="is_draft" v-model="is_draft"/>

                        <input type="hidden" name="id" value="{{ isset($email) ? $email->id : '' }}"/>

                        @include ('admin::common.custom-attributes.edit.email-tags')

                        <div class="form-group email-control-group" :class="[errors.has('reply_to[]') ? 'has-error' : '']">
                            <label for="to" class="required">{{ __('admin::app.leads.to') }}</label>
    
                            <email-tags-component
                                control-name="reply_to[]"
                                control-label="{{ __('admin::app.leads.to') }}"
                                :validations="'required'"
                                :data='@json(isset($email) ? $email->reply_to : [])'
                            ></email-tags-component>
    
                            <span class="control-error" v-if="errors.has('reply_to[]')">
                                @{{ errors.first('reply_to[]') }}
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
    
                        <div class="form-group email-control-group" :class="[errors.has('cc[]') ? 'has-error' : '']" v-if="show_cc">
                            <label for="cc">{{ __('admin::app.leads.cc') }}</label>
    
                            <email-tags-component
                                control-name="cc[]"
                                control-label="{{ __('admin::app.leads.cc') }}"
                                :data='@json(isset($email) ? $email->cc : [])'
                            ></email-tags-component>
    
                            <span class="control-error" v-if="errors.has('cc[]')">
                                @{{ errors.first('cc[]') }}
                            </span>
                        </div>
    
                        <div class="form-group email-control-group" :class="[errors.has('bcc[]') ? 'has-error' : '']" v-if="show_bcc">
                            <label for="bcc">{{ __('admin::app.leads.bcc') }}</label>
    
                            <email-tags-component
                                control-name="bcc[]"
                                control-label="{{ __('admin::app.leads.bcc') }}"
                                :data='@json(isset($email) ? $email->bcc : [])'
                            ></email-tags-component>
    
                            <span class="control-error" v-if="errors.has('bcc[]')">
                                @{{ errors.first('bcc[]') }}
                            </span>
                        </div>
                        
                        <div class="form-group" :class="[errors.has('subject') ? 'has-error' : '']">
                            <label for="subject" class="required">{{ __('admin::app.leads.subject') }}</label>

                            <input
                                type="text"
                                name="subject"
                                class="control"
                                id="subject"
                                value="{{ isset($email) ? $email->subject : '' }}"
                                v-validate="'required'"
                                data-vv-as="&quot;{{ __('admin::app.leads.subject') }}&quot;"
                            />

                            <span class="control-error" v-if="errors.has('subject')">
                                @{{ errors.first('subject') }}
                            </span>
                        </div>
                        
                        <div class="form-group" :class="[errors.has('reply') ? 'has-error' : '']">
                            <label for="reply" class="required" style="margin-bottom: 10px">{{ __('admin::app.leads.reply') }}</label>

                            <textarea
                                name="reply"
                                class="control"
                                id="reply"
                                v-validate="'required'"
                                data-vv-as="&quot;{{ __('admin::app.leads.reply') }}&quot;"
                            >{{ isset($email) ? $email->reply : '' }}</textarea>

                            <span class="control-error" v-if="errors.has('reply')">
                                @{{ errors.first('reply') }}
                            </span>
                        </div>
    
                        <div class="form-group">
                            <attachment-wrapper :data='@json(isset($email) ? $email->attachments : [])'></attachment-wrapper>
                        </div>

                        {!! view_render_event('admin.mail.compose.form_controls.after', ['email' => $email ?? null]) !!}
                    </div>
                </div>

            </div>

        </form>
    </script>

    <script>
        Vue.component('email-form', {

            template: '#email-form-template',

            inject: ['$validator'],

            data: function () {
                return {
                    is_draft: 0,

                    show_cc: false,

                    show_bcc: false,
                }
            },

            mounted: function() {
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
                            self.$validator.validate('reply', this.getContent());
                        });
                    }
                });
            },

            methods: {
                onSubmit: function(e) {
                    this.$root.onSubmit(e);
                }
            }
        });
    </script>
@endpush