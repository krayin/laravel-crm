<x-admin::layouts>
    <!-- Page Title -->
    <x-slot:title>
        @lang('admin::app.settings.webforms.create.title')
    </x-slot>

    <x-admin::form :action="route('admin.settings.web_forms.store')">
        <div class="flex flex-col gap-4">
            <div class="flex items-center justify-between rounded-lg border border-gray-200 bg-white px-4 py-2 text-sm dark:border-gray-800 dark:bg-gray-900 dark:text-gray-300">
                <div class="flex flex-col gap-2">
                    {!! view_render_event('admin.settings.webform.create.breadcrumbs.before') !!}

                    <!-- Breadcrumbs -->
                    <x-admin::breadcrumbs name="settings.web_forms.create" />

                    {!! view_render_event('admin.settings.webform.create.breadcrumbs.after') !!}

                    <div class="text-xl font-bold dark:text-white">
                        @lang('admin::app.settings.webforms.create.title')
                    </div>
                </div>

                <div class="flex items-center gap-x-2.5">
                    <div class="flex items-center gap-x-2.5">
                        {!! view_render_event('admin.settings.webform.create.save_button.before') !!}

                        <!-- Create Button -->
                        <button
                            type="submit"
                            class="primary-button"
                        >
                            @lang('admin::app.settings.webforms.create.save-btn')
                        </button>

                        {!! view_render_event('admin.settings.webform.create.save_button.after') !!}
                    </div>
                </div>
            </div>

            <v-webform></v-webform>
        </div>
    </x-admin::form>

    @pushOnce('scripts')
        <script
            type="text/x-template"
            id="v-webform-template"
        >
            <div class="flex gap-2.5 max-xl:flex-wrap">
                {!! view_render_event('admin.settings.webform.create.left.before') !!}

                <!-- Left Sub Component -->
                <div class="flex flex-1 flex-col gap-2 max-xl:flex-auto">
                    <div class="box-shadow rounded-lg border border-gray-200 bg-white p-4 dark:border-gray-800 dark:bg-gray-900">
                        <div class="mb-4 flex items-center justify-between gap-4">
                            <div class="flex flex-col gap-1">
                                <p class="text-base font-semibold text-gray-800 dark:text-white">
                                    @lang('admin::app.settings.webforms.create.title')
                                </p>
                            </div>
                        </div>

                        {!! view_render_event('admin.settings.webform.create.form_controls.before') !!}

                        <!-- Submit Success Actions -->
                        <x-admin::form.control-group>
                            <x-admin::form.control-group.label class="required">
                                @lang('admin::app.settings.webforms.create.submit-success-action')
                            </x-admin::form.control-group.label>

                            <div class="flex">
                                <x-admin::form.control-group.control
                                    type="select"
                                    name="submit_success_action"
                                    id="submit_success_action"
                                    value="message"
                                    class="!w-1/4 rounded-r-none"
                                    :label="trans('admin::app.settings.webforms.create.submit-success-action')"
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
                                    class="rounded-l-none"
                                    rules="required"
                                    :value="old('submit_success_content')"
                                    :label="trans('admin::app.settings.webforms.create.submit-success-action')"
                                    ::placeholder="placeholder"
                                />
                            </div>

                            <x-admin::form.control-group.error control-name="submit_success_content"/>
                        </x-admin::form.control-group>

                        <!-- Create Leads -->
                        <x-admin::form.control-group>
                            <x-admin::form.control-group.label for="create_lead">
                                @lang('admin::app.settings.webforms.create.create-lead')
                            </x-admin::form.control-group.label>

                            <input
                                type="hidden"
                                name="create_lead"
                                :value="0"
                            />

                            <x-admin::form.control-group.control
                                type="switch"
                                name="create_lead"
                                value="1"
                                :label="trans('admin::app.settings.webforms.create.create-lead')"
                                :checked="false"
                            />

                        </x-admin::form.control-group>

                        <!-- Customize Web-form -->
                        <div class="mb-4 flex items-center justify-between gap-4">
                            <div class="flex flex-col gap-1">
                                <p class="text-base font-semibold text-gray-800 dark:text-white">
                                    @lang('admin::app.settings.webforms.create.customize-webform')
                                </p>

                                <p class="text-sm text-gray-600 dark:text-white">
                                    @lang('admin::app.settings.webforms.create.customize-webform-info')
                                </p>
                            </div>
                        </div>

                        <!-- Background Color Picker -->
                        <v-color-picker
                            name="background_color"
                            title="@lang('admin::app.settings.webforms.create.background-color')"
                            value="{{ old('background_color') ?? '#F7F8F9' }}"
                            class="w-1/5"
                        >
                            <x-admin::form.control-group>
                                <x-admin::form.control-group.label class="">
                                    @lang('admin::app.settings.webforms.create.background-color')
                                </x-admin::form.control-group.label>

                                <x-admin::form.control-group.control
                                    type="text"
                                    name="background_color"
                                    id="background_color"
                                />

                                <x-admin::form.control-group.error control-name="background_color"/>
                            </x-admin::form.control-group>
                        </v-color-picker>

                        <!-- Background Color Picker -->
                        <v-color-picker
                            name="form_background_color"
                            title="@lang('admin::app.settings.webforms.create.form-background-color')"
                            value="{{ old('form_background_color') ?? '#FFFFFF' }}"
                            class="w-1/5"
                        >
                            <x-admin::form.control-group>
                                <x-admin::form.control-group.label class="">
                                    @lang('admin::app.settings.webforms.create.form-background-color')
                                </x-admin::form.control-group.label>

                                <x-admin::form.control-group.control
                                    type="text"
                                    name="form_background_color"
                                    id="form_background_color"
                                    :label="trans('admin::app.settings.webforms.create.form-background-color')"
                                    :placeholder="trans('admin::app.settings.webforms.create.form-background-color')"
                                />

                                <x-admin::form.control-group.error control-name="form_background_color"/>
                            </x-admin::form.control-group>
                        </v-color-picker>

                        <!-- Background Color Picker -->
                        <v-color-picker
                            name="form_title_color"
                            title="@lang('admin::app.settings.webforms.create.form-title-color')"
                            value="{{ old('form_title_color') ?? '#263238' }}"
                            class="w-1/5"
                        >
                            <x-admin::form.control-group>
                                <x-admin::form.control-group.label class="">
                                    @lang('admin::app.settings.webforms.create.form-title-color')
                                </x-admin::form.control-group.label>

                                <x-admin::form.control-group.control
                                    type="text"
                                    name="form_title_color"
                                    id="form_title_color"
                                    :label="trans('admin::app.settings.webforms.create.form-title-color')"
                                    :placeholder="trans('admin::app.settings.webforms.create.form-title-color')"
                                />

                                <x-admin::form.control-group.error control-name="form_title_color"/>
                            </x-admin::form.control-group>
                        </v-color-picker>

                        <!-- Form Submit Button Picker -->
                        <v-color-picker
                            name="form_submit_button_color"
                            title="@lang('admin::app.settings.webforms.create.form-submit-btn-color')"
                            value="{{ old('form_submit_button_color') ?? '#0E90D9' }}"
                            class="w-1/5"
                        >
                            <x-admin::form.control-group>
                                <x-admin::form.control-group.label>
                                    @lang('admin::app.settings.webforms.create.form-submit-btn-color')
                                </x-admin::form.control-group.label>

                                <x-admin::form.control-group.control
                                    type="text"
                                    name="form_submit_button_color"
                                    id="form_submit_button_color"
                                    :label="trans('admin::app.settings.webforms.create.form-submit-btn-color')"
                                    :placeholder="trans('admin::app.settings.webforms.create.form-submit-btn-color')"
                                />

                                <x-admin::form.control-group.error control-name="form_submit_button_color"/>
                            </x-admin::form.control-group>
                        </v-color-picker>

                        <!-- Attribute Label Color Picker -->
                        <v-color-picker
                            name="attribute_label_color"
                            title="@lang('admin::app.settings.webforms.create.attribute-label-color')"
                            value="{{ old('attribute_label_color') ?? '#546E7A' }}"
                            class="w-1/5"
                        >
                            <x-admin::form.control-group>
                                <x-admin::form.control-group.label class="">
                                    @lang('admin::app.settings.webforms.create.attribute-label-color')
                                </x-admin::form.control-group.label>

                                <x-admin::form.control-group.control
                                    type="text"
                                    name="attribute_label_color"
                                    id="attribute_label_color"
                                    :label="trans('admin::app.settings.webforms.create.attribute-label-color')"
                                    :placeholder="trans('admin::app.settings.webforms.create.attribute-label-color')"
                                />

                                <x-admin::form.control-group.error control-name="attribute_label_color"/>
                            </x-admin::form.control-group>
                        </v-color-picker>


                         <!-- Attributes -->
                        <div class="mb-4 flex items-center justify-between gap-4">
                            <div class="flex flex-col gap-1">
                                <p class="text-base font-semibold text-gray-800 dark:text-white">
                                    @lang('admin::app.settings.webforms.create.attributes')
                                </p>

                                <p class="text-sm text-gray-600 dark:text-white">
                                    @lang('admin::app.settings.webforms.create.attributes-info')
                                </p>
                            </div>
                        </div>

                        <div class= "flex flex-col gap-4">
                            <x-admin::dropdown class="w-1/5 rounded-lg group-hover:visible dark:border-gray-800 dark:bg-gray-900 dark:text-gray-300 dark:hover:border-gray-400 dark:focus:border-gray-400">
                                <x-slot:toggle>
                                    <button
                                        type="button"
                                        class="primary-button"
                                    >
                                        @lang('admin::app.settings.webforms.create.add-attribute-btn')
                                    </button>
                                </x-slot>

                                <x-slot:menu class="max-h-80 overflow-y-auto !p-0 dark:border-gray-800">
                                    <template v-if="createLead">
                                        <div class="m-2 text-lg font-bold">@lang('admin::app.settings.webforms.create.leads')</div>

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

                                    <template v-else>
                                        <div class="m-2 text-lg font-bold">@lang('admin::app.settings.webforms.create.person')</div>

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
                                </x-slot>
                            </x-admin::dropdown>

                            <!-- Attributes -->
                            <draggable
                                tag="tbody"
                                ghost-class="draggable-ghost"
                                handle=".icon-move"
                                v-bind="{animation: 200}"
                                item-key="id"
                                :list="addedAttributes"z
                            >
                                <template #item="{ element, index }">
                                    <x-admin::table.thead.tr class="hover:bg-gray-50 dark:hover:bg-gray-950">
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
                                                />

                                                <x-admin::form.control-group.error ::name="'attributes[' + element.id + '][placeholder]'"/>
                                            </x-admin::form.control-group>
                                        </x-admin::table.td>

                                        <!-- Required Or Not -->
                                        <x-admin::table.td>
                                            <x-admin::form.control-group class="!mt-6">
                                                <label :for="'attributes[' + element.id + '][is_required]'">
                                                    <!--
                                                        When the checkbox is disabled for name and email, we will set a hidden value
                                                        because the form will not send the value of disabled input fields.
                                                    -->
                                                    <input
                                                        type="hidden"
                                                        :name="'attributes[' + element.id + '][is_required]'"
                                                        :value="1"
                                                        v-if="['name', 'emails'].includes(element['attribute']['code'])"
                                                    >

                                                    <!--
                                                        Only the name and email fields are required, so we will disable the checkbox and
                                                        force the user to provide them.
                                                    -->
                                                    <input
                                                        type="checkbox"
                                                        :name="'attributes[' + element.id + '][is_required]'"
                                                        :id="'attributes[' + element.id + '][is_required]'"
                                                        :value="1"
                                                        class="peer hidden"
                                                        :checked="element.is_required"
                                                        :disabled="['name', 'emails'].includes(element['attribute']['code'])"
                                                    >

                                                    <!--
                                                        We will display a disabled checkbox for the name and email fields.
                                                    -->
                                                    <span
                                                        class='icon-checkbox-outline peer-checked:icon-checkbox-select cursor-pointer rounded-md text-2xl peer-checked:text-brandColor'
                                                        :class="{'opacity-50' : ['name', 'emails'].includes(element['attribute']['code'])}"
                                                    ></span>
                                                </label>
                                            </x-admin::form.control-group>
                                        </x-admin::table.td>

                                        <!-- Actions -->
                                        <x-admin::table.td>
                                            <x-admin::form.control-group class="!mt-6">
                                                <i
                                                    class="icon-delete cursor-pointer text-2xl"
                                                    v-if="! ['name', 'emails'].includes(element['attribute']['code'])"
                                                    @click="removeAttribute(element)"
                                                ></i>
                                            </x-admin::form.control-group>
                                        </x-admin::table.td>
                                    </x-admin::table.thead.tr>
                                </template>
                            </draggable>
                        </div>

                        {!! view_render_event('admin.settings.webform.create.form_controls.after') !!}
                    </div>
                </div>

                {!! view_render_event('admin.settings.webform.create.left.after') !!}

                {!! view_render_event('admin.settings.webform.create.right.before') !!}

                <!-- Right Sub Component -->
                <div class="flex w-[360px] max-w-full flex-col gap-2 max-sm:w-full">
                    <x-admin::accordion class="rounded-lg">
                        <x-slot:header>
                            <div class="flex items-center justify-between">
                                <p class="p-2.5 text-base font-semibold text-gray-800 dark:text-white">
                                    @lang('admin::app.settings.webforms.create.general')
                                </p>
                            </div>
                        </x-slot>

                        <x-slot:content>
                            <!-- Title -->
                            <x-admin::form.control-group>
                                <x-admin::form.control-group.label class="required">
                                    @lang('admin::app.settings.webforms.create.title')
                                </x-admin::form.control-group.label>

                                <x-admin::form.control-group.control
                                    type="text"
                                    id="title"
                                    name="title"
                                    rules="required"
                                    :value="old('title')"
                                    :label="trans('admin::app.settings.webforms.create.title')"
                                    :placeholder="trans('admin::app.settings.webforms.create.title')"
                                />

                                <x-admin::form.control-group.error control-name="title" />
                            </x-admin::form.control-group>

                            <!-- Description -->
                            <x-admin::form.control-group>
                                <x-admin::form.control-group.label class="">
                                    @lang('admin::app.settings.webforms.create.description')
                                </x-admin::form.control-group.label>

                                <x-admin::form.control-group.control
                                    type="textarea"
                                    id="description"
                                    name="description"
                                    :value="old('description')"
                                    :label="trans('admin::app.settings.webforms.create.description')"
                                    :placeholder="trans('admin::app.settings.webforms.create.description')"
                                />

                                <x-admin::form.control-group.error control-name="description" />
                            </x-admin::form.control-group>

                            <!-- Submit Button Label -->
                            <x-admin::form.control-group class="!mb-0">
                                <x-admin::form.control-group.label class="required">
                                    @lang('admin::app.settings.webforms.create.submit-button-label')
                                </x-admin::form.control-group.label>

                                <x-admin::form.control-group.control
                                    type="text"
                                    id="submit_button_label"
                                    name="submit_button_label"
                                    rules="required"
                                    :value="old('submit_button_label')"
                                    :label="trans('admin::app.settings.webforms.create.submit-button-label')"
                                    :placeholder="trans('admin::app.settings.webforms.create.submit-button-label')"
                                />

                                <x-admin::form.control-group.error control-name="submit_button_label" />
                            </x-admin::form.control-group>
                        </x-slot>
                    </x-admin::accordion>
                </div>

                {!! view_render_event('admin.settings.webform.create.right.after') !!}
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
                            value: '{{ old('submit_success_action', 'message') }}',

                            options: [
                                { value: 'message', label: '@lang('admin::app.settings.webforms.create.display-custom-message')' },
                                { value: 'redirect', label: '@lang('admin::app.settings.webforms.create.redirect-to-url')' },
                            ],
                        },

                        createLead: false,

                        attributes: @json($attributes['other']),

                        addedAttributes: [],

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

                created () {
                    @json($attributes['default']).forEach(function (attribute, key) {
                        this.addedAttributes.push({
                            'id': 'attribute_' + this.attributeCount++,
                            'name': attribute.name,
                            'is_required': attribute.is_required,
                            'attribute': attribute,
                        });
                    }, this);
                },

                methods: {
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
                            return "@lang('admin::app.settings.webforms.create.choose-value')";
                        } else if (attribute.type == 'file') {
                            return "@lang('admin::app.settings.webforms.create.select-file')";
                        } else if (attribute.type == 'image') {
                            return "@lang('admin::app.settings.webforms.create.select-image')";
                        } else {
                            return "@lang('admin::app.settings.webforms.create.enter-value')";
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
