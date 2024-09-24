@php($placeholders = app('\Webkul\Automation\Helpers\Entity')->getEmailTemplatePlaceholders())

<x-admin::layouts>
    <!-- Page Title -->
    <x-slot:title>
        @lang('admin::app.settings.webhooks.create.title')
    </x-slot>

    {!! view_render_event('admin.settings.webhook.edit.form.before') !!}

    <x-admin::form :action="route('admin.settings.webhooks.store')">
        <div class="flex flex-col gap-4">
            <div class="flex items-center justify-between rounded-lg border border-gray-200 bg-white px-4 py-2 text-sm dark:border-gray-800 dark:bg-gray-900 dark:text-gray-300">
                <div class="flex flex-col gap-2">
                    <div class="flex cursor-pointer items-center">
                        {!! view_render_event('admin.settings.webhook.edit.breadrumbs.before') !!}

                        <!-- Breadcrumbs -->
                        <x-admin::breadcrumbs name="settings.webhooks.create" />

                        {!! view_render_event('admin.settings.webhook.edit.breadrumbs.after') !!}
                    </div>
        
                    <div class="text-xl font-bold dark:text-white">
                        @lang('admin::app.settings.webhooks.create.title')
                    </div>
                </div>
        
                <div class="flex items-center gap-x-2.5">
                    <div class="flex items-center gap-x-2.5">
                        {!! view_render_event('admin.settings.webhook.edit.save_button.before') !!}

                        <!-- Create button for person -->
                        <button
                            type="submit"
                            class="primary-button"
                        >
                            @lang('admin::app.settings.webhooks.create.save-btn')
                        </button>

                        {!! view_render_event('admin.settings.webhook.edit.save_button.after') !!}
                    </div>
                </div>
            </div>

            <v-webhooks :errors="errors"></v-webhooks>
        </div>
    </x-admin::form>

    {!! view_render_event('admin.settings.webhook.edit.form.after') !!}

    @pushOnce('scripts')
        <script
            type="text/x-template"
            id="v-webhooks-template"
        >
            <div class="flex gap-2.5 max-xl:flex-wrap">
                {!! view_render_event('admin.settings.webhook.edit.left.before') !!}

                <!-- Left sub-component -->
                <div class="flex flex-1 flex-col gap-2 max-xl:flex-auto">
                    <div class="box-shadow rounded-lg border border-gray-200 bg-white p-4 dark:border-gray-800 dark:bg-gray-900">
                        <div class="mb-4 flex items-center justify-between gap-4">
                            <div class="flex flex-col gap-1">
                                <p class="text-base font-semibold text-gray-800 dark:text-white">
                                    @lang('admin::app.settings.webhooks.create.title')
                                </p>
    
                                <p class="text-sm text-gray-600 dark:text-white">
                                    @lang('admin::app.settings.webhooks.create.info')
                                </p>
                            </div>
                        </div>

                        <!-- Basic Details -->
                        <div class="flex flex-col gap-4">
                            <div>
                                <!-- Method and URL endpoint -->
                                <x-admin::form.control-group>
                                    <x-admin::form.control-group.label class="required">
                                        @lang('admin::app.settings.webhooks.create.url-and-parameters')
                                    </x-admin::form.control-group.label>

                                    <div class="flex">
                                        <x-admin::form.control-group.control
                                            type="select"
                                            name="method"
                                            id="method"
                                            value="post"
                                            class="!w-1/6 rounded-r-none"
                                            :label="trans('admin::app.settings.webhooks.create.method')"
                                        >
                                            <option value="post">@lang('admin::app.settings.webhooks.create.post')</option>
                                            
                                            <option value="put">@lang('admin::app.settings.webhooks.create.put')</option>
                                        </x-admin::form.control-group.control>

                                        <x-admin::form.control-group.control
                                            type="text"
                                            name="end_point"
                                            id="end_point"
                                            class="rounded-l-none"
                                            :value="old('end_point')"
                                            rules="required|url"
                                            :label="trans('admin::app.settings.webhooks.create.url-endpoint')"
                                            :placeholder="trans('admin::app.settings.webhooks.create.url-endpoint')"
                                            v-debounce="500"
                                            v-model.lazy="baseUrl"
                                        />
                                    </div>
                                    <x-admin::form.control-group.error control-name="end_point"/>
                                </x-admin::form.control-group>

                                <!-- Parameters -->
                                <v-key-and-value
                                    title="@lang('admin::app.settings.webhooks.create.parameters')"
                                    name="query_params"
                                    :add-btn-title="'@lang('admin::app.settings.webhooks.create.add-new-parameter')'"
                                    :fields="parameters"
                                    :placeholders="placeholders"
                                ></v-key-and-value>

                                <!-- URL Preview -->
                                <div class="flex w-full items-center justify-between rounded-sm border border-gray-200 bg-white px-4 py-2 text-sm dark:border-gray-800 dark:bg-gray-900 dark:text-gray-300">
                                    <div class="my-2 flex gap-3">
                                        <div class="font-sm text-xs dark:text-gray-300">
                                            @lang('admin::app.settings.webhooks.create.url-preview')

                                            <span class="text-sm font-medium text-gray-800 dark:text-gray-300">@{{ urlEndPoint() }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Headers -->
                            <div class="border-y border-gray-200 dark:border-gray-800">
                                <v-key-and-value
                                    title="@lang('admin::app.settings.webhooks.create.headers')"
                                    name="headers"
                                    :add-btn-title="'@lang('admin::app.settings.webhooks.create.add-new-header')'"
                                    :fields="headers"
                                    :placeholders="placeholders"
                                ></v-key-and-value>
                            </div>

                            <!-- Content -->
                            <x-admin::form.control-group class="!mb-0">
                                <x-admin::form.control-group.label class="required">
                                    @lang('admin::app.settings.webhooks.create.body')
                                </x-admin::form.control-group.label>

                                <div class="mb-4 flex items-center gap-2">
                                    <div class="flex cursor-pointer items-center justify-center">
                                        <input
                                            id="default"
                                            type="radio"
                                            v-model="contentType"
                                            value="default"
                                            name="payload_type"
                                            class="h-4 w-4 cursor-pointer border-gray-300 bg-gray-100 text-blue-600 dark:border-gray-600 dark:bg-gray-700 dark:ring-offset-gray-800"
                                        >
                                        <label
                                            for="default"
                                            class="ms-2 cursor-pointer text-xs font-normal text-gray-900 dark:text-gray-300"
                                        >
                                            @lang('admin::app.settings.webhooks.create.default')
                                        </label>
                                    </div>
                                    
                                    <div class="flex cursor-pointer items-center justify-center">
                                        <input
                                            id="x-www-form-urlencoded"
                                            type="radio"
                                            v-model="contentType"
                                            value="x-www-form-urlencoded"
                                            name="payload_type"
                                            class="h-4 w-4 cursor-pointer border-gray-300 bg-gray-100 text-blue-600 dark:border-gray-600 dark:bg-gray-700 dark:ring-offset-gray-800"
                                        >
                                        <label
                                            for="x-www-form-urlencoded"
                                            class="ms-2 cursor-pointer text-xs font-normal text-gray-900 dark:text-gray-300"
                                        >
                                            @lang('admin::app.settings.webhooks.create.x-www-form-urlencoded')
                                        </label>
                                    </div>

                                    <div class="flex items-center justify-center gap-5">
                                        <div class="flex cursor-pointer items-center justify-center">
                                            <input
                                                id="raw"
                                                type="radio"
                                                v-model="contentType"
                                                value="raw"
                                                name="payload_type"
                                                class="h-4 w-4 cursor-pointer border-gray-300 bg-gray-100 text-blue-600 dark:border-gray-600 dark:bg-gray-700 dark:ring-offset-gray-800"
                                                @click="contentType = 'raw'"
                                            >

                                            <label
                                                for="raw"
                                                class="ms-2 cursor-pointer text-xs font-normal text-gray-900 dark:text-gray-300"
                                                @click="contentType = 'raw'"
                                            >
                                                @lang('admin::app.settings.webhooks.create.raw')
                                            </label>
                                        </div>

                                        <template v-if="contentType == 'raw'">
                                            <x-admin::dropdown class="rounded-lg dark:border-gray-800 dark:bg-gray-900 dark:text-gray-300 dark:hover:border-gray-400 dark:focus:border-gray-400">
                                                <x-slot:toggle>
                                                    <div class="flex items-center justify-center">
                                                        <span class="cursor-pointer text-xs font-normal text-brandColor dark:text-gray-300">@{{ rawType }}</span>

                                                        <i class="icon-down-arrow -mt-px text-xs text-brandColor"></i>
                                                    </div>
                                                </x-slot>
                            
                                                <x-slot:menu class="!p-0 dark:border-gray-800">
                                                    <input
                                                        type="hidden"
                                                        name="raw_payload_type"
                                                        v-model="rawType"
                                                    >

                                                    <span
                                                        class="whitespace-no-wrap flex cursor-pointer items-center justify-between gap-1.5 rounded-t px-2 py-2 text-sm text-gray-600 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-950"
                                                        @click="rawType = 'json'"
                                                    >
                                                        <div class="items flex items-center gap-1.5">
                                                            @lang('admin::app.settings.webhooks.create.json')
                                                        </div>
                                                    </span>

                                                    <span
                                                        class="whitespace-no-wrap flex cursor-pointer items-center justify-between gap-1.5 rounded-t px-2 py-2 text-sm text-gray-600 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-950"
                                                        @click="rawType = 'text'"
                                                    >
                                                        <div class="items flex items-center gap-1.5">
                                                            @lang('admin::app.settings.webhooks.create.text')
                                                        </div>
                                                    </span>
                                                </x-slot>
                                            </x-admin::dropdown>
                                        </template>
                                    </div>
                                </div>
                            
                                <template v-if="showEditor">
                                    <textarea
                                        ref="payload"
                                        id="payload"
                                        name="payload"
                                    >@{{ payload }}</textarea>
                                </template>

                                <template v-else>
                                    <v-key-and-value
                                        title="@lang('admin::app.settings.webhooks.create.key-and-value')"
                                        name="payload"
                                        :add-btn-title="'@lang('admin::app.settings.webhooks.create.add-new-payload')'"
                                        :fields="tempPayload"
                                        :placeholders="placeholders"
                                    ></v-key-and-value>
                                </template>

                                <x-admin::form.control-group.error control-name="payload" />
                            </x-admin::form.control-group>
                        </div>
                    </div>
                </div>

                {!! view_render_event('admin.settings.webhook.edit.left.after') !!}

                {!! view_render_event('admin.settings.webhook.edit.right.before') !!}

                <!-- Right sub-component -->
                <div class="flex w-[360px] max-w-full flex-col gap-2 max-sm:w-full">
                    <x-admin::accordion>
                        <x-slot:header>
                            <div class="flex items-center justify-between">
                                <p class="p-2.5 text-base font-semibold text-gray-800 dark:text-white">
                                    @lang('admin::app.settings.webhooks.create.general')
                                </p>
                            </div>
                        </x-slot>
    
                        <x-slot:content>
                            <!-- Name -->
                            <x-admin::form.control-group>
                                <x-admin::form.control-group.label class="required">
                                    @lang('admin::app.settings.webhooks.create.name')
                                </x-admin::form.control-group.label>

                                <x-admin::form.control-group.control
                                    type="text"
                                    id="name"
                                    name="name"
                                    rules="required"
                                    :label="trans('admin::app.settings.webhooks.create.name')"
                                    :placeholder="trans('admin::app.settings.webhooks.create.name')"
                                />

                                <x-admin::form.control-group.error control-name="name" />
                            </x-admin::form.control-group>

                            <!-- Entity Type -->
                            <x-admin::form.control-group>
                                <x-admin::form.control-group.label class="required">
                                    @lang('admin::app.settings.webhooks.create.entity-type')
                                </x-admin::form.control-group.label>

                                <x-admin::form.control-group.control
                                    type="select"
                                    id="entity_type"
                                    name="entity_type"
                                    rules="required"
                                    :label="trans('admin::app.settings.webhooks.create.entity-type')"
                                    :placeholder="trans('admin::app.settings.webhooks.create.entity-type')"
                                >
                                    @foreach (app('\Webkul\Automation\Helpers\Entity')->getEvents() as $item)
                                        <option value="{{ $item['id'] }}">
                                            {{ $item['name'] }}
                                        </option>
                                    @endforeach
                                </x-admin::form.control-group.control>

                                <x-admin::form.control-group.error control-name="entity_type" />
                            </x-admin::form.control-group>

                            <!-- Description -->
                            <x-admin::form.control-group class="!mb-0">
                                <x-admin::form.control-group.label class="required">
                                    @lang('admin::app.settings.webhooks.create.description')
                                </x-admin::form.control-group.label>

                                <x-admin::form.control-group.control
                                    type="textarea"
                                    id="description"
                                    name="description"
                                    rules="required"
                                    :label="trans('admin::app.settings.webhooks.create.description')"
                                    :placeholder="trans('admin::app.settings.webhooks.create.description')"
                                />

                                <x-admin::form.control-group.error control-name="description" />
                            </x-admin::form.control-group>
                        </x-slot>
                    </x-admin::accordion>
                </div>

                {!! view_render_event('admin.settings.webhook.edit.right.after') !!}
            </div>
        </script>

        <script
            type="text/x-template"
            id="v-key-and-value-template"
        >
            <x-admin::form.control-group class="my-2">
                <x-admin::form.control-group.label class="required">
                    @{{ title }}
                </x-admin::form.control-group.label>
            
                <div class="flex flex-col">
                    <div 
                        class="group my-2 flex items-center justify-between gap-3"
                        v-for="(field, index) in fields"
                        :key="index"
                    >
                        <div class="w-1/2">
                            <x-admin::form.control-group.control
                                type="text"
                                ::id="`${name}[${index}][key]`"
                                ::name="`${name}[${index}][key]`"
                                v-model="field.key"
                                rules="required"
                                :label="trans('Key')"
                                :placeholder="trans('Key')"
                            />
                            <x-admin::form.control-group.error ::name="`${name}[${index}][key]`" />
                        </div>

                        <div class="w-full">
                            <x-admin::form.control-group.control
                                type="text"
                                ::id="`${name}[${index}][value]`"
                                ::name="`${name}[${index}][value]`"
                                v-model="field.value"
                                rules="required"
                                :label="trans('Value')"
                                :placeholder="trans('Value')"
                            />
                            <x-admin::form.control-group.error ::name="`${name}[${index}][value]`" />
                        </div>

                        <i 
                            class="icon-delete ml-1 cursor-pointer rounded-md p-1.5 text-2xl transition-all hover:bg-gray-100 dark:hover:bg-gray-950"
                            @click="remove(index)"
                            v-if="fields.length > 1"
                        ></i>

                        <div class="w-1/5">
                            <x-admin::dropdown class="rounded-lg group-hover:visible dark:border-gray-800 dark:bg-gray-900 dark:text-gray-300 dark:hover:border-gray-400 dark:focus:border-gray-400">
                                <x-slot:toggle>
                                    <span
                                        class="invisible cursor-pointer py-2 text-xs text-brandColor hover:text-brandColor hover:underline group-hover:visible"
                                    >
                                        @lang('admin::app.settings.webhooks.create.insert-placeholder')
                                    </span>
                                </x-slot>
            
                                <x-slot:menu class="max-h-80 overflow-y-auto !p-0 dark:border-gray-800">
                                    <div
                                        v-for="entity in placeholders"
                                        :key="entity.text"
                                        class="mb-4"
                                    >
                                        <div class="m-2 text-lg font-bold">@{{ entity.text }}</div>

                                        <span
                                            v-for="placeholder in entity.menu"
                                            :key="placeholder.value"
                                            class="whitespace-no-wrap flex cursor-pointer items-center justify-between gap-1.5 rounded-t px-2 py-2 text-sm text-gray-600 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-950"
                                            @click="insertPlaceholder(index, placeholder.value)"
                                        >
                                            <div class="items flex items-center gap-1.5">
                                                @{{ placeholder.text }}
                                            </div>
                                        </span>
                                    </div>
                                </x-slot>
                            </x-admin::dropdown>
                        </div>
                    </div>
            
                    <div class="inline-block">
                        <button
                            type="button"
                            class="flex max-w-max items-center gap-2 text-brandColor"
                            @click="add(index)" 
                        >
                            <i class="icon-add text-md !text-brandColor"></i>

                            @{{ addBtnTitle }}
                        </button>
                    </div>
                </div>
            </x-admin::form.control-group>
        </script>

        <script type="module">
            app.component('v-webhooks', {
                template: '#v-webhooks-template',

                props: ['errors'],

                data() {
                    return {
                        baseUrl: '',

                        payload: '',

                        contentType: 'default',

                        rawType: 'Json',

                        parameters: [{ key: '', value: ''}],

                        tempPayload: [{ key: '', value: ''}],

                        headers: [{ key: 'Content Type', value: 'text/plain;charset=UTF', readOnly: true }],

                        placeholders: @json($placeholders),
                    };
                },

                created() {
                    this.initiateEditor();

                    if (Array.isArray(this.payload)) {
                        this.tempPayload = this.payload;
                    }

                    this.$emitter.on('change-theme', (theme) => this.handleEditorDisplay());
                },

                watch: {
                    /**
                     * Watch the raw type and update the tempPayload.
                     * 
                     * @return {void}
                     */
                    rawType(newValue, oldValue) {
                        this.handleEditorDisplay();
                    },

                    /**
                     * Watch the content type and update the tempPayload.
                     * 
                     * @return {void}
                     */
                    contentType(newValue, oldValue) {
                        this.handleEditorDisplay();
                    },

                    baseUrl() {
                        this.urlEndPoint();
                    },
                },

                computed: {
                    /**
                     * Check if the editor should be displayed.
                     * @returns {boolean}
                     */
                    showEditor() {
                        return (
                            this.contentType === 'default'
                            || this.contentType === 'raw'
                        ) && this.contentType !== 'application/x-www-form-urlencoded';
                    },
                },

                methods: {
                    /**
                     * Handle editor display.
                     * 
                     * @returns {void}
                     */
                    handleEditorDisplay() {
                        if (this.codeMirrorInstance) {
                            this.codeMirrorInstance.toTextArea();

                            this.codeMirrorInstance = null;
                        }

                        if (this.showEditor) {
                            this.initiateEditor();
                        }
                    },

                    /**
                     * Initiate Editor.
                     * 
                     * @param {string} rawType
                     * @return {void}
                     */
                    initiateEditor() {
                        this.$nextTick(() => {
                            const mode = this.rawType === 'json' ? 'application/json' : 'text/plain';

                            if (! this.codeMirrorInstance && this.showEditor) {
                                this.codeMirrorInstance = CodeMirror.fromTextArea(this.$refs.payload, {
                                    lineNumbers: true,
                                    mode: this.contentType === 'default' ? 'application/json' : mode,
                                    styleActiveLine: true,
                                    lint: true,
                                    theme: document.documentElement.classList.contains('dark') ? 'ayu-dark' : 'eclipse',
                                });

                                this.codeMirrorInstance.on('changes', () => this.payload = this.codeMirrorInstance.getValue());

                                return;
                            }

                            this.codeMirrorInstance?.setOption('mode', mode);
                        }, 0);
                    },

                    /**
                     * Get the URL endpoint with the parameters
                     * 
                     * @returns {string}
                     */
                    urlEndPoint() {
                        if (
                            this.baseUrl === ''
                            || this.errors.hasOwnProperty('end_point')
                        ) {
                            return this.baseUrl;
                        }

                        try {
                            const url = new URL(this.baseUrl);

                            url.search = '';

                            this.parameters.forEach(param => {
                                if (
                                    param.key 
                                    && param.value
                                ) {
                                    url.searchParams.append(param.key, param.value);
                                }
                            });

                            return decodeURI(url.toString());
                        } catch (error) {
                            return this.baseUrl;
                        }
                    },
                },
            });

            app.component('v-key-and-value', {
                template: '#v-key-and-value-template',

                props: ['title', 'name', 'addBtnTitle', 'fields', 'placeholders'],

                data() {
                    return {
                        selectedPlaceholder: '',

                        cursorPosition: 0,
                    };
                },

                methods: {
                    /**
                     * Add a new fields.
                     * 
                     * @returns {void}
                     */
                    add() {
                        this.fields.push({ key: '', value: '' });
                    },

                    /**
                     * Remove a fields.
                     * 
                     * @returns {void}
                     */
                    remove(index) {
                        this.fields.splice(index, 1);
                    },

                    /**
                     * Save the cursor position when the input is focused.
                     * 
                     * @param {Event} event
                     * @returns {void}
                     */
                    saveCursorPosition(event) {
                        this.cursorPosition = event.target.selectionStart;
                    },

                    /**
                     * Insert the selected placeholder into the subject.
                     * 
                     * @returns {void}
                     */
                    insertPlaceholder(index, value) {
                        if (this.cursorPosition >= 0) {
                            const before = this.fields[index].value.substring(0, this.cursorPosition);

                            const after = this.fields[index].value.substring(this.cursorPosition);

                            this.fields[index].value = `${before}${value}${after}`;

                            this.cursorPosition += value.length;
                        } else if (value) {
                            this.fields[index].value += value;
                        }

                        this.selectedPlaceholder = '';
                    },
                },
            });
        </script>


        <!-- Code mirror script CDN -->
        <script
            type="text/javascript"
            src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.30.0/codemirror.js"
        ></script>

        <!-- 
            Html mixed and xml cnd both are dependent 
            Used for html and css theme
        -->
        <script
            type="text/javascript"
            src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.30.0/mode/javascript/javascript.js"
        ></script>
    @endPushOnce

    @pushOnce('styles')
        <!-- Code mirror style cdn -->
        <link 
            rel="stylesheet"
            href="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.13.4/codemirror.css"
        ></link>

        <!-- Dark theme css -->
        <link
            rel="stylesheet"
            href="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.63.3/theme/ayu-dark.min.css"
        >
    @endPushOnce
</x-admin::layouts>
