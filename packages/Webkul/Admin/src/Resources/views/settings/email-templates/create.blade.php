@extends('admin::layouts.master')

@section('page_title')
    {{ __('admin::app.settings.email-templates.create-title') }}
@stop

@section('content-wrapper')
    <div class="content full-page adjacent-center">
        {!! view_render_event('admin.settings.email_templates.create.header.before') !!}

        <div class="page-header">
            
            {{ Breadcrumbs::render('settings.email_templates.create') }}

            <div class="page-title">
                <h1>{{ __('admin::app.settings.email-templates.create-title') }}</h1>
            </div>
        </div>

        {!! view_render_event('admin.settings.email_templates.create.header.after') !!}

        <form method="POST" action="{{ route('admin.settings.email_templates.store') }}" @submit.prevent="onSubmit">
            <div class="page-content">
                <div class="form-container">
                    <div class="panel">
                        <div class="panel-header">
                            {!! view_render_event('admin.settings.email_templates.create.form_buttons.before') !!}

                            <button type="submit" class="btn btn-md btn-primary">
                                {{ __('admin::app.settings.email-templates.save-btn-title') }}
                            </button>

                            <a href="{{ route('admin.settings.email_templates.index') }}">
                                {{ __('admin::app.layouts.back') }}
                            </a>

                            {!! view_render_event('admin.settings.email_templates.create.form_buttons.after') !!}
                        </div>

                        <div class="panel-body">
                            {!! view_render_event('admin.settings.email_templates.create.form_controls.before') !!}

                            @csrf()

                            <div class="form-group" :class="[errors.has('name') ? 'has-error' : '']">
                                <label class="required">
                                    {{ __('admin::app.settings.email-templates.name') }}
                                </label>

                                <input
                                    type="text"
                                    name="name"
                                    class="control"
                                    value="{{ old('name') }}"
                                    v-validate="'required'"
                                    data-vv-as="{{ __('admin::app.settings.email-templates.name') }}"
                                />

                                <span class="control-error" v-if="errors.has('name')">
                                    @{{ errors.first('name') }}
                                </span>
                            </div>

                            <div class="form-group">
                                <label class="required">
                                    {{ __('admin::app.settings.email-templates.subject') }}
                                </label>

                                <div>
                                    <div class="form-group input-group" :class="[errors.has('subject') ? 'has-error' : '']">
                                        <input
                                            type="text"
                                            name="subject"
                                            class="control"
                                            id="subject"
                                            value="{{ old('subject') }}"
                                            v-validate="'required'"
                                            data-vv-as="{{ __('admin::app.settings.email-templates.subject') }}"
                                        />

                                        <div class="input-group-append">
                                            <select class="control subject-placeholers" id="subject-placeholders">
                                                <option value="">{{ __('admin::app.settings.email-templates.placeholders') }}</option>

                                                @foreach ($placeholders as $entity)
                                                    <optgroup label="{{ $entity['text'] }}">

                                                        @foreach ($entity['menu'] as $placeholder)
                                                            <option value="{{ $placeholder['value'] }}">
                                                                {{ $placeholder['text'] }}
                                                            </option>
                                                        @endforeach
                                                        
                                                    </optgroup>
                                                @endforeach
                                            </select>
                                        </div>

                                        <span class="control-error" v-if="errors.has('subject')">
                                            @{{ errors.first('subject') }}
                                        </span>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group" :class="[errors.has('content') ? 'has-error' : '']">
                                <label class="required">
                                    {{ __('admin::app.settings.email-templates.content') }}
                                </label>

                                <textarea
                                    name="content"
                                    class="control"
                                    id="control"
                                    v-validate="'required'"
                                    data-vv-as="{{ __('admin::app.settings.email-templates.content') }}"
                                >{{ old('content') }}</textarea>

                                <span class="control-error" v-if="errors.has('content')">
                                    @{{ errors.first('content') }}
                                </span>
                            </div>

                            {!! view_render_event('admin.settings.email_templates.create.form_controls.after') !!}
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
@stop

@push('scripts')
    <script src="{{ asset('vendor/webkul/admin/assets/js/tinyMCE/tinymce.min.js') }}"></script>

    <script>
        $('document').ready(function() {
            var cursorPosition;

            $('#subject').on('focusout', function() {
                cursorPosition = $(this).prop("selectionStart");
            });

            $('#subject-placeholders').on('change', function(e) {
                var subjectControl = $('#subject');

                var placeholder = $(e.target).val();

                $(e.target).val('');

                if (cursorPosition >= 0) {
                    var newContent = subjectControl.val().substring(0, cursorPosition) + placeholder + subjectControl.val().substring(cursorPosition);

                    subjectControl.val(newContent);

                    cursorPosition = cursorPosition + placeholder.length;
                } else if (placeholder) {
                    subjectControl.val(subjectControl.val() + placeholder);
                }
            });

            tinymce.init({
                selector: 'textarea#control',

                height: 200,

                width: "100%",

                menubar: false,

                plugins: 'image imagetools media wordcount save fullscreen code table lists link hr',

                toolbar: 'placeholders | formatselect | bold italic strikethrough forecolor backcolor link hr | alignleft aligncenter alignright alignjustify | numlist bullist outdent indent  | removeformat | code | table',
                
                image_advtab: true,

                setup: function (editor) {
                    editor.addButton('placeholders', {
                        type: 'listbox',

                        text: 'Placeholders',
                        
                        onselect: function (e) {
                            editor.insertContent(this.value());

                            this.text('Placeholders');
                        },

                        values: @json($placeholders)
                    })
                }
            });
        });
    </script>
@endpush