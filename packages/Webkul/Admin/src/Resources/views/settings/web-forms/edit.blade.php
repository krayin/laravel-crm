<x-admin::layouts>
    <!-- Page Title -->
    <x-slot:title>
        @lang('admin::app.settings.webforms.edit.title')
    </x-slot>

    <x-admin::form
        :action="route('admin.settings.web_forms.update', $webForm->id)"
        method="PUT"
    >
        <div class="flex flex-col gap-4">
            <div class="flex items-center justify-between rounded-lg border border-gray-200 bg-white px-4 py-2 text-sm dark:border-gray-800 dark:bg-gray-900 dark:text-gray-300">
                <div class="flex flex-col gap-2">
                    <div class="flex cursor-pointer items-center">
                        {!! view_render_event('admin.settings.webform.edit.breadcrumbs.before', ['webform' => $webForm]) !!}

                        <!-- Breadcurmbs -->
                        <x-admin::breadcrumbs
                            name="settings.web_forms.edit" 
                            :entity="$webForm"
                        />

                        {!! view_render_event('admin.settings.webform.edit.breadcrumbs.after', ['webform' => $webForm]) !!}
                    </div>
        
                    <div class="text-xl font-bold dark:text-white">
                        @lang('admin::app.settings.webforms.edit.title')
                    </div>
                </div>
        
                <div class="flex items-center gap-x-2.5">
                    <div class="flex items-center gap-x-2.5">
                        {!! view_render_event('admin.settings.webform.edit.embed_button.before', ['webform' => $webForm]) !!}

                        <!-- Edit button for person -->
                        <button
                            type="button"
                            class="secondary-button"
                            @click="$refs.embed.openModal()"
                        >
                            @lang('admin::app.settings.webforms.edit.embed')
                        </button>

                        {!! view_render_event('admin.settings.webform.edit.embed_button.after', ['webform' => $webForm]) !!}

                        {!! view_render_event('admin.settings.webform.edit.preview_button.before', ['webform' => $webForm]) !!}

                        <a
                            href="{{ route('admin.settings.web_forms.preview', $webForm->form_id) }}"
                            target="_blank"
                            class="secondary-button"
                        >
                            @lang('admin::app.settings.webforms.edit.preview')
                        </a>

                        {!! view_render_event('admin.settings.webform.edit.preview_button.after', ['webform' => $webForm]) !!}

                        {!! view_render_event('admin.settings.webform.edit.save_button.before', ['webform' => $webForm]) !!}

                        <button
                            type="submit"
                            class="primary-button"
                        >
                            @lang('admin::app.settings.webforms.edit.save-btn')
                        </button>

                        {!! view_render_event('admin.settings.webform.edit.save_button.after', ['webform' => $webForm]) !!}
                    </div>
                </div>
            </div>

            <!-- Webform view component -->
            <v-webform ref="embed"></v-webform>
        </div>
    </x-admin::form>

    @pushOnce('scripts')
        <script 
            type="text/x-template"
            id="v-webform-template"
        >
            <div class="flex gap-2.5 max-xl:flex-wrap">
                {!! view_render_event('admin.settings.webform.edit.left.before', ['webform' => $webForm]) !!}

                <!-- Left sub-component -->
                <div class="flex flex-1 flex-col gap-2 max-xl:flex-auto">
                    <div class="box-shadow rounded-lg border border-gray-200 bg-white p-4 dark:border-gray-800 dark:bg-gray-900">
                        <div class="mb-4 flex items-center justify-between gap-4">
                            <div class="flex flex-col gap-1">
                                <p class="text-base font-semibold text-gray-800 dark:text-white">
                                    @lang('admin::app.settings.webforms.edit.title')
                                </p>
                            </div>
                        </div>

                        {!! view_render_event('admin.settings.webform.edit.form_controls.before', ['webform' => $webForm]) !!}

                        <!-- Submit success actions -->
                        <x-admin::form.control-group>
                            <x-admin::form.control-group.label class="required">
                                @lang('admin::app.settings.webforms.edit.submit-success-action')
                            </x-admin::form.control-group.label>

                            <div class="flex">
                                <x-admin::form.control-group.control
                                    type="select"
                                    name="submit_success_action"
                                    id="submit_success_action"
                                    value="message"
                                    class="!w-1/4 rounded-r-none"
                                    :label="trans('admin::app.settings.webforms.edit.submit-success-action')"
                                    v-model="submitSuccessAction.value"
                                >
                                    <template
                                        v-for="(option, index) in submitSuccessAction.options"
                                        :key="index"
                                    >
                                        <option
                                            :value="option.value"
                                            :text="option.label"
                                        ></option>
                                    </template>
                                </x-admin::form.control-group.control>

                                <x-admin::form.control-group.control
                                    type="text"
                                    name="submit_success_content"
                                    id="submit_success_content"
                                    :value="old('submit_success_content') ?? $webForm->submit_success_content"
                                    class="rounded-l-none"
                                    rules="required"
                                    label="Submit Success Content"
                                    ::placeholder="placeholder"
                                />
                            </div>
                            
                            <x-admin::form.control-group.error control-name="submit_success_content"/>
                        </x-admin::form.control-group>

                        <!-- Create Leads -->
                        <x-admin::form.control-group>
                            <x-admin::form.control-group.label class="required">
                                @lang('admin::app.settings.webforms.edit.create-lead')
                            </x-admin::form.control-group.label>

                            <label class="relative inline-flex cursor-pointer items-center">
                                
                                <input  
                                    type="checkbox"
                                    name="create_lead"
                                    id="create_lead"
                                    class="peer sr-only"
                                    v-model="createLead"
                                >
            
                                <div class="peer h-5 w-9 cursor-pointer rounded-full bg-gray-200 after:absolute after:top-0.5 after:h-4 after:w-4 after:rounded-full after:border after:border-gray-300 after:bg-white after:transition-all after:content-[''] peer-checked:bg-blue-600 peer-checked:after:border-white peer-focus:outline-none peer-focus:ring-blue-300 dark:bg-gray-800 dark:after:border-white dark:after:bg-white dark:peer-checked:bg-gray-950 after:ltr:left-0.5 peer-checked:after:ltr:translate-x-full after:rtl:right-0.5 peer-checked:after:rtl:-translate-x-full"></div>
                            </label>
                        </x-admin::form.control-group>

                        <!-- Customize webform -->
                        <div class="mb-4 flex items-center justify-between gap-4">
                            <div class="flex flex-col gap-1">
                                <p class="text-base font-semibold text-gray-800 dark:text-white">
                                    @lang('admin::app.settings.webforms.edit.customize-webform')
                                </p>

                                <p class="text-sm text-gray-600 dark:text-white">
                                    @lang('admin::app.settings.webforms.edit.customize-webform-info')
                                </p>
                            </div>
                        </div>

                        <!-- Backgroud Color Picker -->
                        <v-color-picker
                            name="background_color"
                            title="@lang('admin::app.settings.webforms.edit.background-color')"
                            value="{{ old('background_color') ?? $webForm->background_color }}"
                            class="w-1/5"
                        >
                            <x-admin::form.control-group>
                                <x-admin::form.control-group.label class="">
                                    @lang('admin::app.settings.webforms.edit.background-color')
                                </x-admin::form.control-group.label>

                                <x-admin::form.control-group.control
                                    type="text"
                                    name="background_color"
                                    id="background_color"
                                />

                                <x-admin::form.control-group.error control-name="background_color"/>
                            </x-admin::form.control-group>
                        </v-color-picker>

                        <!-- Backgroud Color Picker -->
                        <v-color-picker
                            name="form_background_color"
                            title="@lang('admin::app.settings.webforms.edit.form-background-color')"
                            value="{{ old('form_background_color') ?? $webForm->form_background_color }}"
                            class="w-1/5"
                        >
                            <x-admin::form.control-group>
                                <x-admin::form.control-group.label class="">
                                    @lang('admin::app.settings.webforms.edit.form-background-color')
                                </x-admin::form.control-group.label>

                                <x-admin::form.control-group.control
                                    type="text"
                                    name="form_background_color"
                                    id="form_background_color"
                                />

                                <x-admin::form.control-group.error control-name="form_background_color"/>
                            </x-admin::form.control-group>
                        </v-color-picker>

                        <!-- Backgroud Color Picker -->
                        <v-color-picker
                            name="form_title_color"
                            title="@lang('admin::app.settings.webforms.edit.form-title-color')"
                            value="{{ old('form_title_color') ?? $webForm->form_title_color }}"
                            class="w-1/5"
                        >
                            <x-admin::form.control-group>
                                <x-admin::form.control-group.label class="">
                                    @lang('admin::app.settings.webforms.edit.form-title-color')
                                </x-admin::form.control-group.label>

                                <x-admin::form.control-group.control
                                    type="text"
                                    name="form_title_color"
                                    id="form_title_color"
                                />

                                <x-admin::form.control-group.error control-name="form_title_color"/>
                            </x-admin::form.control-group>
                        </v-color-picker>

                        <!-- Form Submit Button Picker -->
                        <v-color-picker
                            name="form_submit_button_color"
                            title="@lang('admin::app.settings.webforms.edit.form-submit-button-color')"
                            value="{{ old('form_submit_button_color') ?? $webForm->form_submit_button_color }}"
                            class="w-1/5"
                        >
                            <x-admin::form.control-group>
                                <x-admin::form.control-group.label class="">
                                    @lang('admin::app.settings.webforms.edit.form-submit-button-color')
                                </x-admin::form.control-group.label>

                                <x-admin::form.control-group.control
                                    type="text"
                                    name="form_submit_button_color"
                                    id="form_submit_button_color"
                                />

                                <x-admin::form.control-group.error control-name="form_submit_button_color"/>
                            </x-admin::form.control-group>
                        </v-color-picker>

                        <!-- Attribute Label Color Picker -->
                        <v-color-picker
                            name="attribute_label_color"
                            title="@lang('admin::app.settings.webforms.edit.attribute-label-color')"
                            value="{{ old('attribute_label_color') ?? $webForm->attribute_label_color }}"
                            class="w-1/5"
                        >
                            <x-admin::form.control-group>
                                <x-admin::form.control-group.label class="">
                                    @lang('admin::app.settings.webforms.edit.attribute-label-color')
                                </x-admin::form.control-group.label>

                                <x-admin::form.control-group.control
                                    type="text"
                                    name="attribute_label_color"
                                    id="attribute_label_color"
                                />

                                <x-admin::form.control-group.error control-name="attribute_label_color"/>
                            </x-admin::form.control-group>
                        </v-color-picker>

                         <!-- Attributes -->
                        <div class="mb-4 flex items-center justify-between gap-4">
                            <div class="flex flex-col gap-1">
                                <p class="text-base font-semibold text-gray-800 dark:text-white">
                                    @lang('admin::app.settings.webforms.edit.attributes')
                                </p>

                                <p class="text-sm text-gray-600 dark:text-white">
                                    @lang('admin::app.settings.webforms.edit.attributes-info')
                                </p>
                            </div>
                        </div>

                        <div class="flex flex-col gap-4">
                            <x-admin::dropdown class="w-1/5 rounded-lg group-hover:visible dark:border-gray-800 dark:bg-gray-900 dark:text-gray-300 dark:hover:border-gray-400 dark:focus:border-gray-400">
                                <x-slot:toggle>
                                    <button
                                        type="button"
                                        class="primary-button"
                                    >
                                        @lang('admin::app.settings.webforms.edit.add-attribute-btn')
                                    </button>
                                </x-slot>
            
                                <x-slot:menu class="max-h-80 overflow-y-auto !p-0 dark:border-gray-800">
                                    <template v-if="createLead">
                                        <div class="m-2 text-lg font-bold">
                                            @lang('admin::app.settings.webforms.edit.person')
                                        </div>

                                        <span
                                            v-for="attribute in groupedAttributes.persons"
                                            class="whitespace-no-wrap flex cursor-pointer items-center justify-between gap-1.5 rounded-t px-2 py-2 text-sm text-gray-600 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-950"
                                            @click="addAttribute(attribute)"
                                        >
                                            <div class="items flex items-center gap-1.5">
                                                @{{ attribute.name }}
                                            </div>
                                        </span>
                                    </template>

                                    <template v-else>
                                        <div class="m-2 text-lg font-bold">
                                            @lang('admin::app.settings.webforms.edit.leads')
                                        </div>

                                        <span
                                            v-for="attribute in groupedAttributes.leads"
                                            class="whitespace-no-wrap flex cursor-pointer items-center justify-between gap-1.5 rounded-t px-2 py-2 text-sm text-gray-600 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-950"
                                            @click="addAttribute(attribute)"
                                        >
                                            <div class="items flex items-center gap-1.5">
                                                @{{ attribute.name }}
                                            </div>
                                        </span>
                                    </template>
                                </x-slot>
                            </x-admin::dropdown>

                            <!-- Attributes -->
                            <draggable
                                tag="tbody"
                                ghost-class="draggable-ghost"
                                handle=".icon-move"
                                v-bind="{animation: 200}"
                                item-key="id"
                                :list="addedAttributes"
                            >
                                <template #item="{ element, index }">
                                    <x-admin::table.thead.tr class="!rounded-lg hover:bg-gray-50 dark:hover:bg-gray-950">
                                        <!-- Draggable Icon -->
                                        <x-admin::table.td class="text-center">
                                            <i class="icon-move cursor-grab rounded-md text-2xl transition-all hover:bg-gray-100 dark:hover:bg-gray-950"></i>

                                            <input
                                                type="hidden"
                                                :value="element['attribute']['id']"
                                                :name="'attributes[' + element.id + '][attribute_id]'"
                                            />
                                        </x-admin::table.td>

                                        <!-- Attribute Name -->
                                        <x-admin::table.td>
                                            <x-admin::form.control-group>
                                                <x-admin::form.control-group.label class="">
                                                    @{{ element?.name + ' (' + element?.attribute?.entity_type + ')' }}
                                                </x-admin::form.control-group.label>

                                                <x-admin::form.control-group.control
                                                    type="text"
                                                    ::name="'attributes[' + element.id + '][name]'"
                                                    v-model="element.name"
                                                />

                                                <x-admin::form.control-group.error ::name="'attributes[' + element.id + '][name]'"/>
                                            </x-admin::form.control-group>
        
                                        </x-admin::table.td>

                                        <!-- Placeholder -->
                                        <x-admin::table.td>
                                            <x-admin::form.control-group class="!mt-6">
                                                <x-admin::form.control-group.control
                                                    type="text"
                                                    ::name="'attributes[' + element.id + '][placeholder]'"
                                                    ::rules="element.attribute.validation"
                                                    ::label="element?.name + ' (' + element?.attribute?.entity_type + ')'"
                                                    ::placeholder="getPlaceholderValue(element)"
                                                    v-model="element.placeholder"
                                                />

                                                <x-admin::form.control-group.error ::name="'attributes[' + element.id + '][placeholder]'"/>
                                            </x-admin::form.control-group>
                                        </x-admin::table.td>

                                        <!-- Required or Not -->
                                        <x-admin::table.td>
                                            <x-admin::form.control-group class="!mt-6">
                                                <label :for="'attributes[' + element.id + '][is_required]'">
                                                    <input
                                                        type="checkbox"
                                                        :name="'attributes[' + element.id + '][is_required]'"
                                                        :id="'attributes[' + element.id + '][is_required]'"
                                                        :value="1"
                                                        class="peer hidden"
                                                        :checked="element.is_required"
                                                        :disabled="element.attribute.is_required ? true : false"
                                                    >

                                                    <span
                                                        class='icon-checkbox-outline peer-checked:icon-checkbox-select cursor-pointer rounded-md text-2xl peer-checked:text-brandColor'
                                                        :class="{'opacity-50' : element.attribute.is_required}"
                                                    ></span>
                                                </label>
                                            </x-admin::form.control-group>
                                        </x-admin::table.td>

                                        <!-- Actions -->
                                        <x-admin::table.td>
                                            <x-admin::form.control-group class="!mt-6">
                                                <i
                                                    class="icon-delete cursor-pointer text-2xl"
                                                    v-if="element['attribute']['code'] != 'name' && element['attribute']['code'] != 'emails'"
                                                    @click="removeAttribute(element)"
                                                ></i>
                                            </x-admin::form.control-group>
                                        </x-admin::table.td>
                                    </x-admin::table.thead.tr>
                                </template>
                            </draggable>
                        </div>

                        {!! view_render_event('admin.settings.webform.edit.form_controls.before', ['webform' => $webForm]) !!}
                    </div>
                </div>

                {!! view_render_event('admin.settings.webform.edit.left.after', ['webform' => $webForm]) !!}

                {!! view_render_event('admin.settings.webform.edit.right.before', ['webform' => $webForm]) !!}

                <!-- Right sub-component -->
                <div class="flex w-[360px] max-w-full flex-col gap-2 max-sm:w-full">
                    <x-admin::accordion>
                        <x-slot:header>
                            <div class="flex items-center justify-between">
                                <p class="p-2.5 text-base font-semibold text-gray-800 dark:text-white">
                                    @lang('admin::app.settings.webforms.edit.general')
                                </p>
                            </div>
                        </x-slot>

                        <x-slot:content>
                            <!-- Title -->
                            <x-admin::form.control-group>
                                <x-admin::form.control-group.label class="required">
                                    @lang('admin::app.settings.webforms.edit.title')
                                </x-admin::form.control-group.label>

                                <x-admin::form.control-group.control
                                    type="text"
                                    id="title"
                                    name="title"
                                    rules="required"
                                    :value="old('title') ?? $webForm->title"
                                    :label="trans('admin::app.settings.webforms.edit.title')"
                                    :placeholder="trans('admin::app.settings.webforms.edit.title')"
                                />

                                <x-admin::form.control-group.error control-name="title" />
                            </x-admin::form.control-group>

                            <!-- Description -->
                            <x-admin::form.control-group>
                                <x-admin::form.control-group.label class="">
                                    @lang('admin::app.settings.webforms.edit.description')
                                </x-admin::form.control-group.label>

                                <x-admin::form.control-group.control
                                    type="textarea"
                                    id="description"
                                    name="description"
                                    :value="old('description') ?? $webForm->description"
                                    :label="trans('admin::app.settings.webforms.edit.description')"
                                    :placeholder="trans('admin::app.settings.webforms.edit.description')"
                                />

                                <x-admin::form.control-group.error control-name="description" />
                            </x-admin::form.control-group>

                            <!-- Submit button label -->
                            <x-admin::form.control-group class="!mb-0">
                                <x-admin::form.control-group.label class="required">
                                    @lang('admin::app.settings.webforms.edit.submit-button-label')
                                </x-admin::form.control-group.label>

                                <x-admin::form.control-group.control
                                    type="text"
                                    id="submit_button_label"
                                    name="submit_button_label"
                                    :value="old('submit_button_label') ?? $webForm->submit_button_label"
                                    rules="required"
                                    :label="trans('admin::app.settings.webforms.edit.submit-button-label')"
                                    :placeholder="trans('admin::app.settings.webforms.edit.submit-button-label')"
                                />

                                <x-admin::form.control-group.error control-name="submit_button_label" />
                            </x-admin::form.control-group>
                        </x-slot>
                    </x-admin::accordion>
                </div>

                {!! view_render_event('admin.settings.webform.edit.right.after', ['webform' => $webForm]) !!}

                <x-admin::modal ref="embed">
                    <!-- Modal Header -->
                    <x-slot:header>
                        <p class="text-lg font-bold text-gray-800 dark:text-white">
                            @lang('admin::app.settings.webforms.edit.preview')
                        </p>
                    </x-slot>

                    <!-- Modal Content -->
                    <x-slot:content class="!border-b-0">
                        {!! view_render_event('admin.settings.webform.edit.modal.form_controls.before', ['webform' => $webForm]) !!}

                        <x-admin::form.control-group>
                            <x-admin::form.control-group.label class="required">
                                @lang('admin::app.settings.webforms.edit.public-url')
                            </x-admin::form.control-group.label>

                            <x-admin::form.control-group.control
                                type="text"
                                id="publicUrl"
                                name="publicUrl"
                                rules="required"
                                :value="route('admin.settings.web_forms.preview', $webForm->form_id)"
                                :label="trans('admin::app.settings.webforms.edit.public-url')"
                                :placeholder="trans('admin::app.settings.webforms.edit.public-url')"
                            />

                            <span
                                id="publicUrlBtn"
                                class="cursor-pointer text-xs font-normal text-brandColor hover:text-sky-600 hover:underline"
                                @click="copyToClipboard('#publicUrl','#publicUrlBtn')"
                            >
                                @lang('admin::app.settings.webforms.edit.copy')
                            </span>
                        </x-admin::form.control-group>

                        <x-admin::form.control-group>
                            <x-admin::form.control-group.label class="required">
                                @lang('admin::app.settings.webforms.edit.code-snippet')
                            </x-admin::form.control-group.label>

                            <input
                                type="text"
                                id="codeSnippet"
                                name="codeSnippet"
                                class="w-full rounded border border-gray-200 px-2.5 py-2 text-sm font-normal text-gray-800 transition-all hover:border-gray-400 focus:border-gray-400 dark:border-gray-800 dark:bg-gray-900 dark:text-gray-300 dark:hover:border-gray-400 dark:focus:border-gray-400"
                                value="{{ '<script src="'.route('admin.settings.web_forms.form_js', $webForm->form_id).'"></script>' }}"
                            />

                            <span
                                id="coeSnippt"
                                class="cursor-pointer text-xs font-normal text-brandColor hover:text-sky-600 hover:underline"
                                @click="copyToClipboard('#codeSnippet','#coeSnippt')"
                            >
                                @lang('admin::app.settings.webforms.edit.copy')
                            </span>
                        </x-admin::form.control-group>

                        {!! view_render_event('admin.settings.webform.edit.modal.form_controls.after', ['webform' => $webForm]) !!}
                    </x-slot>
                </x-admin::modal>
            </div>
        </script>

        <script
            type="text/x-template"
            id="v-color-picker-template"
        >
            <x-admin::form.control-group>
                <x-admin::form.control-group.label class="required">
                    @{{ title }}
                </x-admin::form.control-group.label>

                <div class="flex">
                    <x-admin::form.control-group.control
                        type="text"
                        ::name="name"
                        ::id="name"
                        class="rounded-r-none"
                        rules="required"
                        ::label="title"
                        v-model="color"
                    />

                    <x-admin::form.control-group.control
                        type="color"
                        class="!h-10 !w-12 rounded-l-none p-1 dark:border-gray-800 dark:bg-gray-900"
                        name="color"
                        :label="trans('Submit Success Action')"
                        ::value="color"
                        @input="color = $event.target.value"
                    />
                </div>
                
                <x-admin::form.control-group.error ::name="name"/>
            </x-admin::form.control-group>
        </script>

        <script type="module">
            app.component('v-webform', {
                template: '#v-webform-template',

                data() {
                    return {
                        submitSuccessAction: {
                            value: "{{ old('submit_success_action') ?? $webForm->submit_success_action }}",

                            options: [
                                { value: 'message', label: '@lang('admin::app.settings.webforms.edit.display-custom-message')' },
                                { value: 'redirect', label: '@lang('admin::app.settings.webforms.edit.redirect-to-url')' },
                            ],
                        },

                        createLead: Boolean({{ (old('create_lead') ?? $webForm->create_lead) ? true : false }}),

                        attributes: @json($attributes),

                        addedAttributes: @json($webForm->attributes()->with('attribute')->get()),

                        attributeCount: 0,
                    }
                },

                watch: {
                    /**
                     * Watch for the createLead value and remove the added attributes if the value is true.
                     * 
                     * @param {Boolean} newValue
                     * @param {Boolean} oldValue
                     * 
                     * @return {void}
                     */
                    createLead(newValue, oldValue) {
                        if (newValue) {
                            return;
                        }

                        this.addedAttributes = this.addedAttributes.filter(attribute => attribute.attribute.entity_type != 'leads');
                    },
                },

                computed:{
                    /**
                     * Get the placeholder value based on the submit success action value.
                     * 
                     * @return {String}
                     */
                    placeholder() {
                        return this.submitSuccessAction.value === 'message' ? '@lang('Enter message to display')' : '@lang('Enter url to redirect')';
                    },

                    /**
                     * Get the grouped attributes based on the entity type.
                     * 
                     * @return {Object}
                     */
                    groupedAttributes() {
                        return this.attributes.reduce((r, a) => {
                            r[a.entity_type] = [...r[a.entity_type] || [], a];
                            return r;
                        }, {});
                    },
                },

                methods: {
                    /**
                     * Copy the value to the clipboard.
                     * 
                     * @param {String} ref
                     * @param {String} btn
                     * 
                     * @return {void}
                     */
                    copyToClipboard(ref, btn) {
                        const element = document.querySelector(ref);

                        const btnElement = document.querySelector(btn);

                        element.select();

                        document.execCommand('copy');

                        btnElement.textContent = "@lang('admin::app.settings.webforms.edit.copied')!";

                        setTimeout(() => btnElement.textContent = "Copy", 1000);
                    },

                    /**
                     * Open the modal based on the type.
                     * 
                     * @param {String} type
                     * 
                     * @return {void}
                     */
                    openModal() {
                        this.$refs.embed.toggle();
                    },

                    /**
                     * Add the attribute to the added attributes list.
                     * 
                     * @param {Object} attribute
                     * 
                     * @return {void}
                     */
                    addAttribute(attribute) {
                        this.addedAttributes.push({
                            id: 'attribute_' + this.attributeCount++,
                            name: attribute.name,
                            is_required: attribute.is_required,
                            attribute: attribute,
                        });

                        const index = this.attributes.indexOf(attribute);
                        
                        if (index > -1) {
                            this.attributes.splice(index, 1);
                        }
                    },

                    /**
                     * Remove the attribute from the added attributes list.
                     * 
                     * @param {Object} attribute
                     * 
                     * @return {void}
                     */
                    removeAttribute(attribute) {
                        this.attributes.push(attribute.attribute);

                        const index = this.addedAttributes.indexOf(attribute);

                        if (index > -1) {
                            this.addedAttributes.splice(index, 1);
                        }
                    },

                    /**
                     * Get the placeholder value based on the attribute type.
                     * 
                     * @param {Object} attribute
                     * 
                     * @return {String}
                     */
                    getPlaceholderValue(attribute) {
                        if (attribute.type == 'select'
                            || attribute.type == 'multiselect'
                            || attribute.type == 'checkbox'
                            || attribute.type == 'boolean'
                            || attribute.type == 'lookup'
                            || attribute.type == 'datetime'
                            || attribute.type == 'date'
                        ) {
                            return "@lang('admin::app.settings.webforms.edit.choose-value')";
                        } else if (attribute.type == 'file') {
                            return "@lang('admin::app.settings.webforms.edit.select-file')";
                        } else if (attribute.type == 'image') {
                            return "@lang('admin::app.settings.webforms.edit.select-image')";
                        } else {
                            return "@lang('admin::app.settings.webforms.edit.enter-value')";
                        }
                    },
                },
            });
        </script>

        <script type="module">
            app.component('v-color-picker', {
                template: '#v-color-picker-template',

                props: {
                    name: {
                        type: String,
                        required: true,
                    },

                    value: {
                        type: String,
                        required: true,
                        default: '#ffffff',
                    },

                    title: {
                        type: String,
                        required: true,
                    },
                },

                data() {
                    return {
                        color: this.value,
                    };
                },
            });
        </script>
    @endPushOnce
</x-admin::layouts>
