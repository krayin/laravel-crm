@php($placeholders = app('\Webkul\Automation\Helpers\Entity')->getEmailTemplatePlaceholders())

<x-admin::layouts>
    <!-- Page Title -->
    <x-slot:title>
        @lang('admin::app.settings.webhooks.edit.title')
    </x-slot>

    {!! view_render_event('admin.settings.webhook.edit.form.before', ['webhook' => $webhook]) !!}

    <x-admin::form
        :action="route('admin.settings.webhooks.update', $webhook->id)"
        method="PUT"
    >
        <div class="flex flex-col gap-4">
            <div class="flex items-center justify-between rounded-lg border border-gray-200 bg-white px-4 py-2 text-sm dark:border-gray-800 dark:bg-gray-900 dark:text-gray-300">
                <div class="flex flex-col gap-2">
                    {!! view_render_event('admin.settings.webhook.edit.breadcrumbs.before', ['webhook' => $webhook]) !!}

                    <!-- Breadcrumbs -->
                    <x-admin::breadcrumbs
                        name="settings.webhooks.edit"
                        :entity="$webhook"
                    />

                    {!! view_render_event('admin.settings.webhook.edit.breadcrumbs.after', ['webhook' => $webhook]) !!}
        
                    <div class="text-xl font-bold dark:text-white">
                        @lang('admin::app.settings.webhooks.edit.title')
                    </div>
                </div>
        
                <div class="flex items-center gap-x-2.5">
                    <div class="flex items-center gap-x-2.5">
                        {!! view_render_event('admin.settings.webhook.edit.save_button.before', ['webhook' => $webhook]) !!}

                        <!-- Create button for person -->
                        <button
                            type="submit"
                            class="primary-button"
                        >
                            @lang('admin::app.settings.webhooks.edit.save-btn')
                        </button>

                        {!! view_render_event('admin.settings.webhook.edit.save_button.after', ['webhook' => $webhook]) !!}
                    </div>
                </div>
            </div>

            <v-webhooks :errors="errors"></v-webhooks>
        </div>
    </x-admin::form>

    {!! view_render_event('admin.settings.webhook.edit.form.after', ['webhook' => $webhook]) !!}

    @pushOnce('scripts')
        <script
            type="text/x-template"
            id="v-webhooks-template"
        >
            <div class="flex gap-2.5 max-xl:flex-wrap">
                {!! view_render_event('admin.settings.webhook.edit.left.before', ['webhook' => $webhook]) !!}

                <!-- Left sub-component -->
                <div class="flex flex-1 flex-col gap-2 max-xl:flex-auto">
                    <div class="box-shadow rounded-lg border border-gray-200 bg-white p-4 dark:border-gray-800 dark:bg-gray-900">
                        <div class="mb-4 flex items-center justify-between gap-4">
                            <div class="flex flex-col gap-1">
                                <p class="text-base font-semibold text-gray-800 dark:text-white">
                                    @lang('admin::app.settings.webhooks.edit.title')
                                </p>

                                <p class="text-sm text-gray-600 dark:text-white">
                                    @lang('admin::app.settings.webhooks.edit.info')
                                </p>
                            </div>
                        </div>

                        <!-- Basic Details -->
                        <div class="flex flex-col gap-4">
                            <div>
                                <!-- Method and URL endpoint -->
                                <x-admin::form.control-group>
                                    <x-admin::form.control-group.label class="required">
                                        @lang('admin::app.settings.webhooks.edit.url-and-parameters')
                                    </x-admin::form.control-group.label>

                                    <div class="flex">
                                        <x-admin::form.control-group.control
                                            type="select"
                                            name="method"
                                            id="method"
                                            :value="old('method') ?? $webhook->method"
                                            class="!w-1/6 rounded-r-none"
                                            :label="trans('admin::app.settings.webhooks.edit.method')"
                                        >
                                            <option value="post">@lang('admin::app.settings.webhooks.edit.post')</option>
                                            <option value="put">@lang('admin::app.settings.webhooks.edit.put')</option>
                                        </x-admin::form.control-group.control>

                                        <x-admin::form.control-group.control
                                            type="text"
                                            name="end_point"
                                            id="end_point"
                                            class="rounded-l-none"
                                            :value="old('end_point') ?? $webhook->end_point"
                                            rules="required|url"
                                            :label="trans('admin::app.settings.webhooks.edit.url-endpoint')"
                                            :placeholder="trans('admin::app.settings.webhooks.edit.url-endpoint')"
                                            v-debounce="500"
                                            v-model.lazy="baseUrl"
                                        />
                                    </div>
                                    <x-admin::form.control-group.error control-name="end_point"/>
                                </x-admin::form.control-group>

                                <!-- Parameters -->
                                <v-key-and-value
                                    title="@lang('admin::app.settings.webhooks.edit.parameters')"
                                    name="query_params"
                                    :add-btn-title="'@lang('admin::app.settings.webhooks.edit.add-new-parameter')'"
                                    :fields="parameters"
                                    :placeholders="placeholders"
                                ></v-key-and-value>
                                
                                <!-- URL Preview -->
                                <div class="flex w-full items-center justify-between rounded-sm border border-gray-200 bg-white px-4 py-2 text-sm dark:border-gray-800 dark:bg-gray-900 dark:text-gray-300">
                                    <div class="my-2 flex gap-3">
                                        <div class="font-sm text-xs dark:text-gray-300">
                                            @lang('admin::app.settings.webhooks.edit.url-preview')

                                            <span class="text-sm font-medium text-gray-800 dark:text-gray-300">@{{ urlEndPoint() }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Headers -->
                            <div class="border-y border-gray-200 dark:border-gray-800">
                                <v-key-and-value
                                    title="@lang('admin::app.settings.webhooks.edit.headers')"
                                    name="headers"
                                    :add-btn-title="'@lang('admin::app.settings.webhooks.edit.add-new-header')'"
                                    :fields="headers"
                                    :placeholders="placeholders"
                                ></v-key-and-value>
                            </div>

                            <!-- Content -->
                            <x-admin::form.control-group>
                                <x-admin::form.control-group.label class="required">
                                    @lang('admin::app.settings.webhooks.edit.body')
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
                                            @lang('admin::app.settings.webhooks.edit.default')
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
                                            @lang('admin::app.settings.webhooks.edit.x-www-form-urlencoded')
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
                                                @click="contentType = 'raw';rawType = 'json'"
                                            >
                                                @lang('admin::app.settings.webhooks.edit.raw')
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
                                                        :class="{'bg-gray-100 dark:bg-gray-950': rawType === 'json'}"
                                                        @click="rawType = 'json'"
                                                    >
                                                        <div class="items flex items-center gap-1.5">
                                                            @lang('admin::app.settings.webhooks.edit.json')
                                                        </div>
                                                    </span>

                                                    <span
                                                        class="whitespace-no-wrap flex cursor-pointer items-center justify-between gap-1.5 rounded-t px-2 py-2 text-sm text-gray-600 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-950"
                                                        :class="{'bg-gray-100 dark:bg-gray-950': rawType === 'text'}"
                                                        @click="rawType = 'text'"
                                                    >
                                                        <div class="items flex items-center gap-1.5">
                                                            @lang('admin::app.settings.webhooks.edit.text')
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
                                        title="@lang('admin::app.settings.webhooks.edit.key-and-value')"
                                        name="payload"
                                        :add-btn-title="'@lang('admin::app.settings.webhooks.edit.add-new-payload')'"
                                        :fields="tempPayload"
                                        :placeholders="placeholders"
                                    ></v-key-and-value>
                                </template>

                                <x-admin::form.control-group.error control-name="payload" />
                            </x-admin::form.control-group>
                        </div>
                    </div>
                </div>

                {!! view_render_event('admin.settings.webhook.edit.left.after', ['webhook' => $webhook]) !!}

                {!! view_render_event('admin.settings.webhook.edit.right.before', ['webhook' => $webhook]) !!}

                <!-- Right sub-component -->
                <div class="flex w-[360px] max-w-full flex-col gap-2 max-sm:w-full">
                    <x-admin::accordion>
                        <x-slot:header>
                            <div class="flex items-center justify-between">
                                <p class="p-2.5 text-base font-semibold text-gray-800 dark:text-white">
                                    @lang('admin::app.settings.webhooks.edit.general')
                                </p>
                            </div>
                        </x-slot>
    
                        <x-slot:content>
                            <!-- Name -->
                            <x-admin::form.control-group>
                                <x-admin::form.control-group.label class="required">
                                    @lang('admin::app.settings.webhooks.edit.name')
                                </x-admin::form.control-group.label>

                                <x-admin::form.control-group.control
                                    type="text"
                                    id="name"
                                    name="name"
                                    rules="required"
                                    :value="old('admin::app.settings.webhooks.edit.name') ?? $webhook->name"
                                    :label="trans('Admin::app.settings.webhooks.edit.name')"
                                    :placeholder="trans('Admin::app.settings.webhooks.edit.name')"
                                />

                                <x-admin::form.control-group.error control-name="name" />
                            </x-admin::form.control-group>

                            <!-- Entity Type -->
                            <x-admin::form.control-group>
                                <x-admin::form.control-group.label class="required">
                                    @lang('admin::app.settings.webhooks.edit.entity-type')
                                </x-admin::form.control-group.label>

                                <x-admin::form.control-group.control
                                    type="select"
                                    id="entity_type"
                                    name="entity_type"
                                    :value="old('entity_type') ?? $webhook->entity_type"
                                    rules="required"
                                    :label="trans('admin::app.settings.webhooks.edit.entity-type')"
                                    :placeholder="trans('admin::app.settings.webhooks.edit.entity-type')"
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
                            <x-admin::form.control-group>
                                <x-admin::form.control-group.label class="required">
                                    @lang('admin::app.settings.webhooks.edit.description')
                                </x-admin::form.control-group.label>

                                <x-admin::form.control-group.control
                                    type="textarea"
                                    id="description"
                                    name="description"
                                    :value="old('description') ?? $webhook->description"
                                    rules="required"
                                    :label="trans('admin::app.settings.webhooks.edit.description')"
                                    :placeholder="trans('admin::app.settings.webhooks.edit.description')"
                                />

                                <x-admin::form.control-group.error control-name="description" />
                            </x-admin::form.control-group>
                        </x-slot>
                    </x-admin::accordion>
                </div>

                {!! view_render_event('admin.settings.webhook.edit.right.before', ['webhook' => $webhook]) !!}
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
                                        @lang('admin::app.settings.webhooks.edit.insert-placeholder')
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
                        baseUrl: '{{ $webhook->end_point ?? '' }}',
                        
                        codeMirrorInstance: null,
                        
                        tempPayload: [],
                        
                        payload: @json($webhook->payload),

                        parameters: @json($webhook->query_params),

                        headers: @json($webhook->headers),

                        placeholders: @json($placeholders),

                        contentType: '{{ $webhook->payload_type ?? 'default' }}',

                        rawType: '{{ $webhook->raw_payload_type !== "" ? $webhook->raw_payload_type : 'json' }}',
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
                     * 
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
                     * 
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
                                    theme: document.documentElement.classList.contains('dark') ? 'ayu-dark' : 'default',
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
