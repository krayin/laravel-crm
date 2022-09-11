@extends('admin::layouts.master')

@section('page_title')
    {{ __('web_form::app.edit-title') }}
@stop

@push('css')
    <style>
        .pre {
            margin-top: 10px;
            padding: 10px;
            background-color: #ebeff3;
            border: 1px solid #cfd7df;
            width: 100%;
            display: block;
            word-break: break-all;
        }

        .copy-input{
            display:flex;
            align-items:center;
        }

        .copy-button{
            width: 23%;
            margin-top: 4px;
        }
    </style>
@endpush

@section('content-wrapper')

    <div class="content full-page adjacent-center">
        {!! view_render_event('admin.settings.web_forms.edit.header.before', ['webForm' => $webForm]) !!}

        <div class="page-header">

            {{ Breadcrumbs::render('settings.web_forms.edit', $webForm) }}

            <div class="page-title">
                <h1>{{ __('web_form::app.edit-title') }}</h1>
            </div>
        </div>

        {!! view_render_event('admin.settings.web_forms.edit.header.after', ['webForm' => $webForm]) !!}

        <form method="POST" action="{{ route('admin.settings.web_forms.update', $webForm->id) }}" @submit.prevent="onSubmit">
            <div class="page-content">
                <div class="form-container">
                    <div class="panel">
                        <div class="panel-header">
                            {!! view_render_event('admin.settings.web_forms.edit.form_buttons.before', ['webForm' => $webForm]) !!}

                            <button type="submit" class="btn btn-md btn-primary">
                                {{ __('web_form::app.save-btn-title') }}
                            </button>

                            <a @click="openModal('embedModal')" style="cursor: pointer">
                                {{ __('web_form::app.embed') }}
                            </a>

                            <a href="{{ route('admin.settings.web_forms.preview', $webForm->form_id) }}" target="_blank">
                                {{ __('web_form::app.preview') }}
                            </a>

                            <a href="{{ route('admin.settings.web_forms.index') }}">
                                {{ __('admin::app.layouts.back') }}
                            </a>

                            {!! view_render_event('admin.settings.web_forms.edit.form_buttons.after', ['webForm' => $webForm]) !!}
                        </div>

                        <div class="panel-body">
                            {!! view_render_event('admin.settings.web_forms.edit.form_controls.before', ['webForm' => $webForm]) !!}

                            @csrf()

                            <input name="_method" type="hidden" value="PUT">

                            <web-form-component></web-form-component>

                            {!! view_render_event('admin.settings.web_forms.create.form_controls.after', ['webForm' => $webForm]) !!}
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>

    <modal id="embedModal" :is-open="modalIds.embedModal">
        <h3 slot="header-title">{{ __('web_form::app.code-snippet') }}</h3>

        <div slot="header-actions">
            <button class="btn btn-sm btn-secondary-outline" @click="closeModal('embedModal')">{{ __('web_form::app.cancel') }}</button>
        </div>

        <div slot="body">

            <div class="form-group">
                <label>
                    {{ __('web_form::app.public-url') }}
                </label>

                <div class="pre copy-input">
                    <input
                        type="text"
                        class="control"
                        v-on:focus="$event.target.select()" 
                        id="publicUrl"
                        value="{{ route('admin.settings.web_forms.preview', $webForm->form_id) }}"
                    />

                    <div class="input-group-append copy-button" >
                        <button
                            class="btn btn-primary btn-md"
                            type="button"
                            id="publicUrlBtn"
                            title="{{ __('shop::app.customer.account.wishlist.copy-link') }}"
                            onclick="copyToClipboard('#publicUrl','#publicUrlBtn')"
                        >
                        {{ __('web_form::app.copy') }}
                        </button>
                    </div>
                </div>
            </div>

            <div class="form-group">
                <label>
                    {{ __('web_form::app.code-snippet') }}
                </label>

                <div class="pre copy-input">
                   
                   <input
                       type="text"
                       class="control"
                       v-on:focus="$event.target.select()" 
                       id="codeSnippet"
                       value='{{ "<script src=" . route('admin.settings.web_forms.form_js', $webForm->form_id) . " crossorigin=\"anonymous\"></script>" }}'
                   />
   
                   <div class="input-group-append copy-button" >
                       <button
                           class="btn btn-primary btn-md"
                           type="button"
                           id="coeSnippt"
                           title="{{ __('shop::app.customer.account.wishlist.copy-link') }}"
                           onclick="copyToClipboard('#codeSnippet','#coeSnippt')"
                       >
                       {{ __('web_form::app.copy') }}
                       </button>
                   </div>
               </div>
            </div>

        </div>
    </modal>
@stop

@push('scripts')

    <script type="text/x-template" id="web-form-component-template">
        <div class="web-form-container">
            <div class="form-group" :class="[errors.has('title') ? 'has-error' : '']">
                <label class="required">
                    {{ __('web_form::app.title') }}
                </label>

                <input
                    type="text"
                    name="title"
                    class="control"
                    value="{{ old('title') ?: $webForm->title }}"
                    v-validate="'required'"
                    data-vv-as="{{ __('web_form::app.title') }}"
                />

                <span class="control-error" v-if="errors.has('title')">
                    @{{ errors.first('title') }}
                </span>
            </div>

            <div class="form-group">
                <label>
                    {{ __('web_form::app.description') }}
                </label>

                <textarea name="description" class="control">{{ old('description') ?: $webForm->description }}</textarea>
            </div>

            <div class="form-group" :class="[errors.has('submit_button_label') ? 'has-error' : '']">
                <label class="required">
                    {{ __('web_form::app.submit-button-label') }}
                </label>

                <input
                    type="text"
                    name="submit_button_label"
                    class="control"
                    value="{{ old('submit_button_label') ?: $webForm->submit_button_label }}"
                    v-validate="'required'"
                    data-vv-as="{{ __('web_form::app.submit_button_label') }}"
                />

                <span class="control-error" v-if="errors.has('submit_button_label')">
                    @{{ errors.first('submit_button_label') }}
                </span>
            </div>

            <div class="form-group" :class="[errors.has('submit_success_content') ? 'has-error' : '']">
                <label class="required">
                    {{ __('web_form::app.submit-success-action') }}
                </label>

                <div class="form-group input-group">
                    <div class="input-group-prepend">
                        <select name="submit_success_action" class="control" v-model="submit_success_action">
                            <option value="message">
                                {{ __('web_form::app.display-custom-message') }}
                            </option>

                            <option value="redirect">
                                {{ __('web_form::app.redirect-to-url') }}
                            </option>
                        </select>
                    </div>

                    <input
                        type="text"
                        name="submit_success_content"
                        value="{{ old('submit_success_content') ?: $webForm->submit_success_content }}"
                        class="control"
                        v-validate="'required'"
                        :data-vv-as="[submit_success_action == 'message'
                            ? '{{ __('web_form::app.display-custom-message') }}'
                            : '{{ __('web_form::app.redirect-to-url') }}'
                        ]"
                        :placeholder="[submit_success_action == 'message'
                            ? '{{ __('web_form::app.enter-message-placeholder') }}'
                            : '{{ __('web_form::app.enter-url-placeholder') }}'
                        ]"
                    />
                </div>

                <span class="control-error" v-if="errors.has('submit_success_content')">
                    @{{ errors.first('submit_success_content') }}
                </span>
            </div>

            <div class="form-group">
                <label>
                    {{ __('web_form::app.create-lead') }}
                </label>

                <label class="switch">
                    <input
                        type="checkbox"
                        name="create_lead"
                        class="control"
                        id="create_lead"
                        v-model="create_lead"
                    />
                    <span class="slider round"></span>
                </label>
            </div>

            <div class="panel-separator"></div>

            <div class="web-form-panel">
                <div class="header">
                    <label>{{ __('web_form::app.customize-web-form') }}</label>
                    <p>{{ __('web_form::app.customize-web-form-info') }}</p>
                </div>

                <div class="form-group">
                    <label>
                        {{ __('web_form::app.background-color') }}
                    </label>

                    <color-picker
                        name="background_color"
                        value="{{ old('background_color') ?? $webForm->background_color }}"
                    ></color-picker>
                </div>

                <div class="form-group">
                    <label>
                        {{ __('web_form::app.form-background-color') }}
                    </label>

                    <color-picker
                        name="form_background_color"
                        value="{{ old('form_background_color') ?? $webForm->form_background_color }}"
                    ></color-picker>
                </div>

                <div class="form-group">
                    <label>
                        {{ __('web_form::app.form-title-color') }}
                    </label>

                    <color-picker
                        name="form_title_color"
                        value="{{ old('form_title_color') ?? $webForm->form_title_color }}"
                    ></color-picker>
                </div>

                <div class="form-group">
                    <label>
                      {{ old('form_submit_button_color') }}  {{ __('web_form::app.form-submit-button-color') }}
                    </label>

                    <color-picker
                        name="form_submit_button_color"
                        value="{{ old('form_submit_button_color') ?? $webForm->form_submit_button_color }}"
                    ></color-picker>
                </div>

                <div class="form-group">
                    <label>
                        {{ __('web_form::app.attribute-label-color') }}
                    </label>

                    <color-picker
                        name="attribute_label_color"
                        value="{{ old('attribute_label_color') ?? $webForm->attribute_label_color }}"
                    ></color-picker>
                </div>
            </div>

            <div class="panel-separator"></div>

            <div class="web-form-panel">
                <div class="header">
                    <label>{{ __('web_form::app.attributes') }}</label>
                    <p>{{ __('web_form::app.attributes-info') }}</p>
                </div>

                <button type="button" class="btn btn-md btn-primary dropdown-toggle">
                    {{ __('web_form::app.add-attribute') }}
                </button>

                <div class="dropdown-list" style="width: 240px">
                    <div class="dropdown-container">
                        <ul>
                            <li>
                                <span class="">{{ __('web_form::app.persons') }}</span>

                                <ul>
                                    <li
                                        v-for='(attribute, index) in grouped_attributes.persons'
                                        :data-id="attribute.id"
                                        @click="addAttribute(attribute)"
                                    >
                                        @{{ attribute.name }}
                                    </li>
                                </ul>
                            </li>

                            <li v-if="create_lead">
                                <span class="">{{ __('web_form::app.leads') }}</span>

                                <ul>
                                    <li
                                        v-for='(attribute, index) in grouped_attributes.leads'
                                        :data-id="attribute.id"
                                        @click="addAttribute(attribute)"
                                    >
                                        @{{ attribute.name }}
                                    </li>
                                </ul>
                            </li>
                        </ul>
                    </div>
                </div>

                <div class="table web-form-attributes dragable-container" style="overflow-x: unset;">
                    <table>
                        <draggable tag="tbody" :list="addedAttributes" draggable=".draggable">
                            <tr :key="attribute.id" v-for="(attribute, index) in addedAttributes" class="draggable">
                                <td class="dragable-icon" style="width: 40px">
                                    <i class="icon align-justify-icon"></i>

                                    <input
                                        type="hidden"
                                        :value="attribute['attribute']['id']"
                                        :name="'attributes[' + attribute.id + '][attribute_id]'"
                                    />
                                </td>

                                <td>
                                    <div class="form-group">
                                        <label>@{{ attribute['name'] + ' (' + attribute['attribute']['entity_type'] + ')' }}</label>

                                        <input
                                            type="text"
                                            :name="'attributes[' + attribute.id + '][name]'"
                                            class="control"
                                            v-model="attribute['name']"
                                        />

                                        <input
                                            type="hidden"
                                            :value="index + 1"
                                            :name="'attributes[' + attribute.id + '][sort_order]'"
                                        />
                                    </div>
                                </td>

                                <td>
                                    <div class="form-group">
                                        <input
                                            type="text"
                                            :name="'attributes[' + attribute.id + '][placeholder]'"
                                            class="control"
                                            v-model="attribute['placeholder']"
                                            :placeholder="getPlaceholderValue(attribute)"
                                        />
                                    </div>
                                </td>

                                <td>
                                    <span class="checkbox">
                                        <input
                                            type="checkbox"
                                            :name="'attributes[' + attribute.id + '][is_required]'"
                                            :id="'is_required_' + attribute.id"
                                            :checked="attribute.is_required"
                                            :disabled="attribute.is_required"
                                        />

                                        <label :for="'is_required_' + attribute.id" class="checkbox-view"></label>

                                        {{ __('web_form::app.required') }}
                                    </span>

                                    <input
                                        type="hidden"
                                        :name="'attributes[' + attribute.id + '][is_required]'"
                                        value="1"
                                        v-if="attribute.is_required"
                                    />
                                </td>

                                <td class="delete-icon" style="width: 40px">
                                    <i
                                        class="icon trash-icon"
                                        v-if="attribute['attribute']['code'] != 'name' && attribute['attribute']['code'] != 'emails'"
                                        @click="removeAttribute(attribute)"
                                    ></i>
                                </td>
                            </tr>
                        </draggable>
                    </table>
                </div>
            </div>
        </div>
    </script>

    <script type="text/x-template" id="attribute-item-template">

    </script>

    <script>
        Vue.component('web-form-component', {

            template: '#web-form-component-template',

            inject: ['$validator'],

            data: function() {
                return {
                    submit_success_action: "{{ old('submit_success_action') ?: $webForm->submit_success_action }}",

                    create_lead: "{{ (old('create_lead') ?: $webForm->create_lead) ? true : false }}",

                    attributes: @json($attributes),

                    addedAttributes: @json($webForm->attributes()->with('attribute')->get()),

                    attributeCount: 0,
                }
            },

            computed: {
                grouped_attributes: function() {
                    let groupedAttributes = this.attributes.reduce((r, a) => {
                        r[a.entity_type] = [...r[a.entity_type] || [], a];
                        return r;
                    }, {});

                    return groupedAttributes;
                },
            },

            watch: {
                create_lead: function(newValue, oldValue) {
                    if (newValue) {
                        return;
                    }

                    this.addedAttributes = this.addedAttributes.filter(attribute => attribute.attribute.entity_type != 'leads');
                }
            },

            methods: {
                addAttribute: function (attribute) {
                    this.addedAttributes.push({
                        'id': 'attribute_' + this.attributeCount++,
                        'name': attribute.name,
                        'is_required': attribute.is_required,
                        'attribute': attribute,
                    });

                    const index = this.attributes.indexOf(attribute);

                    Vue.delete(this.attributes, index);
                },

                removeAttribute: function (attribute) {
                    this.attributes.push(attribute['attribute']);

                    const index = this.addedAttributes.indexOf(attribute);

                    Vue.delete(this.addedAttributes, index);
                },

                getPlaceholderValue: function (attribute) {
                    if (attribute.type == 'select'
                        || attribute.type == 'multiselect'
                        || attribute.type == 'checkbox'
                        || attribute.type == 'boolean'
                        || attribute.type == 'lookup'
                        || attribute.type == 'datetime'
                        || attribute.type == 'date'
                    ) {
                        return "{{ __('web_form::app.choose-value') }}";
                    } else if (attribute.type == 'file') {
                        return "{{ __('web_form::app.select-file') }}";
                    } else if (attribute.type == 'image') {
                        return "{{ __('web_form::app.select-image') }}";
                    } else {
                        return "{{ __('web_form::app.enter-value') }}";
                    }
                }
            }
        });

        function copyToClipboard(ref,btn){
            $(ref).focus();
            document.execCommand('copy');
            $(btn).text("copied!");          
        }
    </script>
@endpush