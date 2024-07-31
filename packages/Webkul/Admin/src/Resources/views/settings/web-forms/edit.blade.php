<x-admin::layouts>
    <x-slot:title>
        @lang('Edit WebForm')
    </x-slot>

    <x-admin::form
        :action="route('admin.settings.web_forms.update', $webForm->id)"
        method="PUT"
    >
        <div class="flex items-center justify-between rounded-lg border border-gray-200 bg-white px-4 py-2 text-sm dark:border-gray-800 dark:bg-gray-900 dark:text-gray-300">
            <div class="flex flex-col gap-2">
                <div class="flex cursor-pointer items-center">
                    <x-admin::breadcrumbs
                        name="settings.web_forms.edit" 
                        :entity="$webForm"
                    />
                </div>
    
                <div class="text-xl font-bold dark:text-gray-300">
                    @lang('Edit WebForm')
                </div>
            </div>
    
            <div class="flex items-center gap-x-2.5">
                <!-- Edit button for person -->
                <div class="flex items-center gap-x-2.5">
                    <button
                        type="button"
                        class="secondary-button"
                        @click="$refs.embed.openModal()"
                    >
                        @lang('Embed')
                    </button>

                    <a
                        href="{{ route('admin.settings.web_forms.preview', $webForm->form_id) }}"
                        target="_blank"
                        class="secondary-button"
                    >
                        @lang('Preview')
                    </a>

                    <button
                        type="submit"
                        class="primary-button"
                    >
                        @lang('Save WebForm')
                    </button>
                </div>
            </div>
        </div>

        <!-- Webform view component -->
        <v-webform ref="embed"></v-webform>
    </x-admin::form>

    @pushOnce('scripts')
        <script 
            type="text/x-template"
            id="v-webform-template"
        >
            <div class="mt-3.5 flex gap-2.5 max-xl:flex-wrap">
                <!-- Left sub-component -->
                <div class="flex flex-1 flex-col gap-2 max-xl:flex-auto">
                    <!-- Title -->
                    <div class="box-shadow rounded bg-white p-4 dark:bg-gray-900">
                        <div class="mb-4 flex items-center justify-between gap-4">
                            <div class="flex flex-col gap-1">
                                <p class="text-base font-semibold text-gray-800 dark:text-white">
                                    @lang('Edit WebForm')
                                </p>
                            </div>
                        </div>

                        <!-- Submit success actions -->
                        <x-admin::form.control-group>
                            <x-admin::form.control-group.label class="!text-gray-600 required">
                                @lang('Submit Success Action ')
                            </x-admin::form.control-group.label>

                            <div class="flex">
                                <x-admin::form.control-group.control
                                    type="select"
                                    name="submit_success_action"
                                    id="submit_success_action"
                                    value="message"
                                    class="!w-1/4 rounded-r-none"
                                    :label="trans('Submit Success Action')"
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
                            <x-admin::form.control-group.label class="!text-gray-600 required">
                                @lang('Create Lead')
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
                                    @lang('Customize Web Form')
                                </p>

                                <p class="text-sm text-gray-600 dark:text-white">
                                    @lang('Customize your web form with element colors of your choosing.')
                                </p>
                            </div>
                        </div>

                        <!-- Backgroud Color Picker -->
                        <v-color-picker
                            name="background_color"
                            title="@lang('Background Color')"
                            value="{{ old('background_color') ?? $webForm->background_color }}"
                        >
                            <x-admin::form.control-group>
                                <x-admin::form.control-group.label class="!text-gray-600">
                                    @lang('Background Color')
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
                            title="@lang('Form Background Color')"
                            value="{{ old('form_background_color') ?? $webForm->form_background_color }}"
                        >
                            <x-admin::form.control-group>
                                <x-admin::form.control-group.label class="!text-gray-600">
                                    @lang('Form Background Color')
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
                            title="@lang('Form Title Color')"
                            value="{{ old('form_title_color') ?? $webForm->form_title_color }}"
                        >
                            <x-admin::form.control-group>
                                <x-admin::form.control-group.label class="!text-gray-600">
                                    @lang('Form Title Color')
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
                            title="@lang('Form Submit Button Color')"
                            value="{{ old('form_submit_button_color') ?? $webForm->form_submit_button_color }}"
                        >
                            <x-admin::form.control-group>
                                <x-admin::form.control-group.label class="!text-gray-600">
                                    @lang('Form Title Color')
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
                            title="@lang('Attribute Label Color')"
                            value="{{ old('attribute_label_color') ?? $webForm->attribute_label_color }}"
                        >
                            <x-admin::form.control-group>
                                <x-admin::form.control-group.label class="!text-gray-600">
                                    @lang('Form Title Color')
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
                                    @lang('Attributes')
                                </p>

                                <p class="text-sm text-gray-600 dark:text-white">
                                    @lang('Add custom attributes to the form.')
                                </p>
                            </div>
                        </div>

                        <x-admin::dropdown class="rounded-lg group-hover:visible w-1/5 dark:border-gray-800 dark:bg-gray-900 dark:text-gray-300 dark:hover:border-gray-400 dark:focus:border-gray-400">
                            <x-slot:toggle>
                                <button
                                    type="button"
                                    class="primary-button"
                                >
                                    @lang('Add Attribute')
                                </button>
                            </x-slot>
        
                            <x-slot:menu class="!p-0 dark:border-gray-800 max-h-80 overflow-y-auto">
                                <template v-if="createLead">
                                    <div class="font-bold text-lg m-2">@lang('Persons')</div>

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
                                    <div class="font-bold text-lg m-2">@lang('Leads')</div>

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
                            handle=".icon-edit"
                            v-bind="{animation: 200}"
                            item-key="id"
                            :list="addedAttributes"
                        >
                            <template #item="{ element, index }">
                                <x-admin::table.thead.tr class="hover:bg-gray-50 dark:hover:bg-gray-950">
                                    <!-- Draggable Icon -->
                                    <x-admin::table.td class="!px-0 text-center">
                                        <i class="icon-edit cursor-grab text-xl transition-all group-hover:text-gray-700"></i>

                                        <input
                                            type="hidden"
                                            :value="element['attribute']['id']"
                                            :name="'attributes[' + element.id + '][attribute_id]'"
                                        />
                                    </x-admin::table.td>

                                    <!-- Attribute Name -->
                                    <x-admin::table.td>
                                        <x-admin::form.control-group>
                                            <x-admin::form.control-group.label class="!text-gray-600">
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
                                                class="icon-delete text-2xl cursor-pointer"
                                                v-if="element['attribute']['code'] != 'name' && element['attribute']['code'] != 'emails'"
                                                @click="removeAttribute(element)"
                                            ></i>
                                        </x-admin::form.control-group>
                                    </x-admin::table.td>
                                </x-admin::table.thead.tr>
                            </template>
                        </draggable>
                    </div>
                </div>

                <!-- Right sub-component -->
                <div class="flex w-[360px] max-w-full flex-col gap-2 max-sm:w-full">
                    <x-admin::accordion>
                        <x-slot:header>
                            <div class="flex items-center justify-between">
                                <p class="p-2.5 text-base font-semibold text-gray-800 dark:text-white">
                                    @lang('General')

                                    
                                </p>
                            </div>
                        </x-slot>

                        <x-slot:content>
                            <!-- Title -->
                            <x-admin::form.control-group>
                                <x-admin::form.control-group.label class="!text-gray-600 required">
                                    @lang('Title')
                                </x-admin::form.control-group.label>

                                <x-admin::form.control-group.control
                                    type="text"
                                    id="title"
                                    name="title"
                                    rules="required"
                                    :value="old('title') ?? $webForm->title"
                                    :label="trans('Title')"
                                    :placeholder="trans('Title')"
                                />

                                <x-admin::form.control-group.error control-name="title" />
                            </x-admin::form.control-group>

                            <!-- Description -->
                            <x-admin::form.control-group>
                                <x-admin::form.control-group.label class="!text-gray-600">
                                    @lang('admin::app.settings.webhooks.create.description')
                                </x-admin::form.control-group.label>

                                <x-admin::form.control-group.control
                                    type="textarea"
                                    id="description"
                                    name="description"
                                    :value="old('description') ?? $webForm->description"
                                    :label="trans('admin::app.settings.webhooks.create.description')"
                                    :placeholder="trans('admin::app.settings.webhooks.create.description')"
                                />

                                <x-admin::form.control-group.error control-name="description" />
                            </x-admin::form.control-group>

                            <!-- Submit button label -->
                            <x-admin::form.control-group>
                                <x-admin::form.control-group.label class="!text-gray-600 required">
                                    @lang('Submit Button Label')
                                </x-admin::form.control-group.label>

                                <x-admin::form.control-group.control
                                    type="text"
                                    id="submit_button_label"
                                    name="submit_button_label"
                                    :value="old('submit_button_label') ?? $webForm->submit_button_label"
                                    rules="required"
                                    :label="trans('Submit Button Label')"
                                    :placeholder="trans('Submit Button Label')"
                                />

                                <x-admin::form.control-group.error control-name="submit_button_label" />
                            </x-admin::form.control-group>
                        </x-slot>
                    </x-admin::accordion>
                </div>

                <x-admin::modal ref="embed">
                    <!-- Modal Header -->
                    <x-slot:header>
                        <p class="text-lg font-bold text-gray-800 dark:text-white">
                            @lang('Preview')
                        </p>
                    </x-slot>

                    <!-- Modal Content -->
                    <x-slot:content class="!border-b-0">
                        <x-admin::form.control-group>
                            <x-admin::form.control-group.label class="required">
                                @lang('Public Url')
                            </x-admin::form.control-group.label>

                            <x-admin::form.control-group.control
                                type="text"
                                id="publicUrl"
                                name="publicUrl"
                                rules="required"
                                :value="route('admin.settings.web_forms.preview', $webForm->form_id)"
                                :label="trans('Public Url')"
                                :placeholder="trans('Public Url')"
                            />

                            <span
                                id="publicUrlBtn"
                                class="text-xs font-normal text-brandColor cursor-pointer hover:text-sky-600 hover:underline"
                                @click="copyToClipboard('#publicUrl','#publicUrlBtn')"
                            >
                                Copy
                            </span>
                        </x-admin::form.control-group>

                        <x-admin::form.control-group>
                            <x-admin::form.control-group.label class="required">
                                @lang('Code Snippet')
                            </x-admin::form.control-group.label>

                            <input
                                type="text"
                                id="codeSnippet"
                                name="codeSnippet"
                                class="w-full rounded border border-gray-200 px-2.5 py-2 text-sm font-normal text-gray-800 transition-all hover:border-gray-400 focus:border-gray-400 dark:border-gray-800 dark:bg-gray-900 dark:text-gray-300 dark:hover:border-gray-400 dark:focus:border-gray-400"
                                value="{{ '<script src="'.route('admin.settings.web_forms.form_js', $webForm->form_id).'"></sript>' }}"
                            />

                            <span
                                id="coeSnippt"
                                class="text-xs font-normal text-brandColor cursor-pointer hover:text-sky-600 hover:underline"
                                @click="copyToClipboard('#codeSnippet','#coeSnippt')"
                            >
                                Copy
                            </span>
                        </x-admin::form.control-group>
                    </x-slot>
                </x-admin::modal>
            </div>
        </script>

        <script
            type="text/x-template"
            id="v-color-picker"
        >
            <x-admin::form.control-group>
                <x-admin::form.control-group.label class="!text-gray-600 required">
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
                        class="!w-12 !h-10 p-1 rounded-l-none"
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
                                { value: 'message', label: '@lang('Display custom message')' },
                                { value: 'redirect', label: '@lang('Redirect to Url')' },
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

                        btnElement.textContent = "Copied!";
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
                            return "@lang('web_form::app.choose-value')";
                        } else if (attribute.type == 'file') {
                            return "@lang('web_form::app.select-file')";
                        } else if (attribute.type == 'image') {
                            return "@lang('web_form::app.select-image')";
                        } else {
                            return "@lang('web_form::app.enter-value')";
                        }
                    },
                },
            });
        </script>

        <script type="module">
            app.component('v-color-picker', {
                template: '#v-color-picker',

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
