@extends('admin::layouts.master')

@section('page_title')
    {{ $email->subject }}
@stop

@section('content-wrapper')
    <div class="content full-page">
        <div class="page-header">
            <div class="page-title">
                <h1>{{ $email->subject }}</h1>
            </div>
        </div>

        <div class="page-content" style="margin-top: 30px;">

            <email-list-component></email-list-component>

        </div>
    </div>
@stop

@push('scripts')

    <script src="{{ asset('vendor/webkul/admin/assets/js/tinyMCE/tinymce.min.js') }}"></script>

    <script type="text/x-template" id="email-list-component-template">
        <div class="email-list">
            <email-item-component
                :email="email"
                :key="0"
                :index="0">
            </email-item-component>


            <div class="email-reply-list">
                <email-item-component
                    v-for='(email, index) in email.emails'
                    :email="email"
                    :key="0"
                    :index="0">
                </email-item-component>
            </div>

            <div class="email-action">
                <span class="reply-button">
                    <i class="icon reply-icon"></i>
                    {{ __('admin::app.mail.reply') }}
                </span>

                <span class="forward-button">
                    <i class="icon forward-icon"></i>
                    {{ __('admin::app.mail.forward') }}
                </span>
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
                                    <li @mouseover="hovering = 'reply-white-icon'" @mouseout="hovering = ''">
                                        <i class="icon reply-icon" :class="{'reply-white-icon': hovering == 'reply-white-icon'}"></i>
                                        {{ __('admin::app.mail.reply') }}
                                    </li>
                                    <li @mouseover="hovering = 'reply-all-white-icon'" @mouseout="hovering = ''">
                                        <i class="icon reply-all-icon" :class="{'reply-all-white-icon': hovering == 'reply-all-white-icon'}"></i>
                                        {{ __('admin::app.mail.reply-all') }}
                                    </li>
                                    <li @mouseover="hovering = 'forward-white-icon'" @mouseout="hovering = ''">
                                        <i class="icon forward-icon" :class="{'forward-white-icon': hovering == 'forward-white-icon'}"></i>
                                        {{ __('admin::app.mail.forward') }}
                                    </li>
                                    <li @mouseover="hovering = 'trash-white-icon'" @mouseout="hovering = ''">
                                        <i class="icon trash-icon" :class="{'trash-white-icon': hovering == 'trash-white-icon'}"></i>
                                        {{ __('admin::app.mail.delete') }}
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
                
                <div class="row" v-if="email.cc.length">
                    <span class="label">
                        {{ __('admin::app.mail.cc-') }}
                    </span>

                    <span class="value">
                        @{{ String(email.cc) }}
                    </span>
                </div>
                
                <div class="row" v-if="email.cc.length">
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

    <script>
        Vue.component('email-list-component', {

            template: '#email-list-component-template',

            props: ['data'],

            inject: ['$validator'],

            data: function () {
                return {
                    email: @json($email),
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
        });
    </script>
@endpush