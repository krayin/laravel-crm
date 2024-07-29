@php($placeholders = app('\Webkul\Automation\Helpers\Entity')->getEmailTemplatePlaceholders())

<x-admin::layouts>
    <!-- Page Title -->
    <x-slot:title>
        @lang('Create Webhooks')
    </x-slot>

    {!! view_render_event('krayin.admin.activities.edit.form.before') !!}

    <x-admin::form
        :action="route('admin.settings.webhooks.update', $webhook->id)"
        method="PUT"
    >
        <div class="flex items-center justify-between rounded-lg border border-gray-200 bg-white px-4 py-2 text-sm dark:border-gray-800 dark:bg-gray-900 dark:text-gray-300">
            <div class="flex flex-col gap-2">
                <div class="flex cursor-pointer items-center">
                    <x-admin::breadcrumbs
                        name="settings.webhooks.edit"
                        :entity="$webhook"
                    />
                </div>
    
                <div class="text-xl font-bold dark:text-gray-300">
                    @lang('Create Webhooks')
                </div>
            </div>
    
            <div class="flex items-center gap-x-2.5">
                <!-- Create button for person -->
                <div class="flex items-center gap-x-2.5">
                    <button
                        type="submit"
                        class="primary-button"
                    >
                        @lang('Save Webhooks')
                    </button>
                </div>
            </div>
        </div>

        <v-webhooks></v-webhooks>
    </x-admin::form>

    @pushOnce('scripts')
        <script
            type="text/x-template"
            id="webhooks-template"
        >
            <div class="mt-3.5 flex gap-2.5 max-xl:flex-wrap">
                <!-- Left sub-component -->
                <div class="flex flex-1 flex-col gap-2 max-xl:flex-auto">
                    <!-- Events -->
                    <div class="box-shadow rounded bg-white p-4 dark:bg-gray-900">
                        <div class="mb-4 flex items-center justify-between gap-4">
                            <div class="flex flex-col gap-1">
                                <p class="text-base font-semibold text-gray-800 dark:text-white">
                                    @lang('Webhooks')
                                </p>

                                <p class="text-sm text-gray-600 dark:text-white">
                                    @lang('Edit the details of webhooks')
                                </p>
                            </div>
                        </div>

                        <!-- Method and URL endpoint -->
                        <x-admin::form.control-group>
                            <x-admin::form.control-group.label class="!text-gray-600 required">
                                @lang('URL And Parameters')
                            </x-admin::form.control-group.label>

                            <div class="flex">
                                <x-admin::form.control-group.control
                                    type="select"
                                    name="method"
                                    id="method"
                                    :value="old('method') ?? $webhook->method"
                                    class="!w-1/6 rounded-r-none"
                                    :label="trans('Method')"
                                >
                                    <option value="post">POST</option>
                                    <option value="put">PUT</option>
                                </x-admin::form.control-group.control>

                                <x-admin::form.control-group.control
                                    type="text"
                                    name="end_point"
                                    id="end_point"
                                    class="rounded-l-none"
                                    :value="old('end_point')"
                                    rules="required"
                                    :label="trans('URL endpoint')"
                                    :placeholder="trans('URL endpoint')"
                                    v-model="urlEndPoint"
                                />
                            </div>
                            <x-admin::form.control-group.error control-name="end_point"/>
                        </x-admin::form.control-group>

                        <v-key-and-value
                            title="@lang('Parameters')"
                            name="query_params"
                            :remove-btn-title="'Add new parameter'"
                            :fields="parameters"
                            :placeholders="placeholders"
                        ></v-key-and-value>
                        
                        <div class="flex items-center justify-between rounded-sm border w-full bg-white border-gray-200 px-4 py-2 text-sm dark:border-gray-800 dark:bg-gray-900 dark:text-gray-300">
                            <div class="flex gap-3 my-2">
                                <div class="text-xs font-sm dark:text-gray-300">
                                    @lang('URL Preview :') <span class="text-sm font-medium text-gray-800 dark:text-gray-300">@{{ urlEndPoint }}</span>
                                </div>
                            </div>
                        </div>

                        <hr class="my-4 w-full"/>

                        <v-key-and-value
                            title="@lang('Headers')"
                            name="headers"
                            :remove-btn-title="'Add new header'"
                            :fields="headers"
                            :placeholders="placeholders"
                        ></v-key-and-value>

                        <hr class="my-4 w-full"/>

                        <x-admin::form.control-group>
                            <x-admin::form.control-group.label class="!text-gray-600 required">
                                @lang('Body')
                            </x-admin::form.control-group.label>

                            <div class="flex gap-2 items-center mb-4">
                                <div class="flex items-center justify-center cursor-pointer">
                                    <input
                                        id="default"
                                        type="radio"
                                        v-model="contentType"
                                        value="default"
                                        name="payload_type"
                                        class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 dark:ring-offset-gray-800 dark:bg-gray-700 dark:border-gray-600 cursor-pointer"
                                    >
                                    <label
                                        for="default"
                                        class="ms-2 text-xs font-normal text-gray-900 dark:text-gray-300 cursor-pointer"
                                    >
                                        @lang('Default')
                                    </label>
                                </div>
                                
                                <div class="flex items-center justify-center cursor-pointer">
                                    <input
                                        id="x-www-form-urlencoded"
                                        type="radio"
                                        v-model="contentType"
                                        value="x-www-form-urlencoded"
                                        name="payload_type"
                                        class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 dark:ring-offset-gray-800 dark:bg-gray-700 dark:border-gray-600 cursor-pointer"
                                    >
                                    <label
                                        for="x-www-form-urlencoded"
                                        class="ms-2 text-xs font-normal text-gray-900 dark:text-gray-300 cursor-pointer"
                                    >
                                        @lang('x-www-form-urlencoded')
                                    </label>
                                </div>

                                <div class="flex gap-5 items-center justify-center">
                                    <div class="flex items-center justify-center cursor-pointer">
                                        <input
                                            id="raw"
                                            type="radio"
                                            v-model="contentType"
                                            value="raw"
                                            name="payload_type"
                                            class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 dark:ring-offset-gray-800 dark:bg-gray-700 dark:border-gray-600 cursor-pointer"
                                            @click="contentType = 'raw'"
                                        >

                                        <label
                                            for="raw"
                                            class="ms-2 text-xs font-normal text-gray-900 dark:text-gray-300 cursor-pointer"
                                            @click="contentType = 'raw';rawType = 'json'"
                                        >
                                            @lang('Raw')
                                        </label>
                                    </div>

                                    <template v-if="contentType == 'raw'">
                                        <x-admin::dropdown class="rounded-lg dark:border-gray-800 dark:bg-gray-900 dark:text-gray-300 dark:hover:border-gray-400 dark:focus:border-gray-400">
                                            <x-slot:toggle>
                                                <div class="flex items-center justify-center">
                                                    <span class="text-xs font-normal text-brandColor dark:text-gray-300 cursor-pointer">@{{ rawType }}</span>

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
                                                        @lang('JSON')
                                                    </div>
                                                </span>

                                                <span
                                                    class="whitespace-no-wrap flex cursor-pointer items-center justify-between gap-1.5 rounded-t px-2 py-2 text-sm text-gray-600 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-950"
                                                    :class="{'bg-gray-100 dark:bg-gray-950': rawType === 'text'}"
                                                    @click="rawType = 'text'"
                                                >
                                                    <div class="items flex items-center gap-1.5">
                                                        @lang('Text')
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
                                    title="@lang('Key and Value')"
                                    name="payload"
                                    :remove-btn-title="'Add new payload'"
                                    :fields="tempPayload"
                                    :placeholders="placeholders"
                                ></v-key-and-value>
                            </template>

                            <x-admin::form.control-group.error control-name="payload" />
                        </x-admin::form.control-group>
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
                            <!-- Name -->
                            <x-admin::form.control-group>
                                <x-admin::form.control-group.label class="!text-gray-600 required">
                                    @lang('Name')
                                </x-admin::form.control-group.label>

                                <x-admin::form.control-group.control
                                    type="text"
                                    id="name"
                                    name="name"
                                    rules="required"
                                    :value="old('name') ?? $webhook->name"
                                    :label="trans('Name')"
                                    :placeholder="trans('Name')"
                                />

                                <x-admin::form.control-group.error control-name="name" />
                            </x-admin::form.control-group>

                            <!-- Entity Type -->
                            <x-admin::form.control-group>
                                <x-admin::form.control-group.label class="!text-gray-600 required">
                                    @lang('Entity Type')
                                </x-admin::form.control-group.label>

                                <x-admin::form.control-group.control
                                    type="select"
                                    id="entity_type"
                                    name="entity_type"
                                    :value="old('entity_type') ?? $webhook->entity_type"
                                    rules="required"
                                    :label="trans('Entity Type')"
                                    :placeholder="trans('Entity Type')"
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
                                <x-admin::form.control-group.label class="!text-gray-600 required">
                                    @lang('Description')
                                </x-admin::form.control-group.label>

                                <x-admin::form.control-group.control
                                    type="textarea"
                                    id="description"
                                    name="description"
                                    :value="old('description') ?? $webhook->description"
                                    rules="required"
                                    :label="trans('Description')"
                                    :placeholder="trans('Description')"
                                />

                                <x-admin::form.control-group.error control-name="description" />
                            </x-admin::form.control-group>
                        </x-slot>
                    </x-admin::accordion>
                </div>
            </div>
        </script>

        <script
            type="text/x-template"
            id="v-key-and-value-template"
        >
            <x-admin::form.control-group class="my-2">
                <x-admin::form.control-group.label class="!text-gray-600 required">
                    @{{ title }}
                </x-admin::form.control-group.label>
            
                <div class="flex flex-col">
                    <div 
                        class="flex gap-3 my-2 items-center justify-between group"
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
                            class="cursor-pointer rounded-md p-1.5 ml-1 text-2xl transition-all hover:bg-gray-100 dark:hover:bg-gray-950 icon-delete"
                            @click="remove(index)"
                            v-if="fields.length > 1"
                        ></i>

                        <div class="w-1/5">
                            <x-admin::dropdown class="rounded-lg group-hover:visible dark:border-gray-800 dark:bg-gray-900 dark:text-gray-300 dark:hover:border-gray-400 dark:focus:border-gray-400">
                                <x-slot:toggle>
                                    <span
                                        class="py-2 text-xs text-brandColor hover:underline hover:text-brandColor cursor-pointer invisible group-hover:visible"
                                    >
                                        @lang('Insert Placeholder')
                                    </span>
                                </x-slot>
            
                                <x-slot:menu class="!p-0 dark:border-gray-800 max-h-80 overflow-y-auto">
                                    <div
                                        v-for="entity in placeholders"
                                        :key="entity.text"
                                        class="mb-4"
                                    >
                                        <div class="font-bold text-lg m-2">@{{ entity.text }}</div>

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
            
                    <span
                        class="py-2 text-xs text-brandColor hover:underline hover:text-sky-500 cursor-pointer"
                        @click="add(index)" 
                    >
                        @{{ removeBtnTitle }}
                    </span>
                </div>
            </x-admin::form.control-group>
        </script>

        <script type="module">
            app.component('v-webhooks', {
                template: '#webhooks-template',

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
                    }
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

                    /**
                     * Get the URL endpoint with the parameters.
                     * 
                     * @returns {string}
                     */
                    urlEndPoint() {
                        if (this.baseUrl === '') {
                            return '';
                        }

                        const url = new URL(this.baseUrl);

                        url.search = '';

                        this.parameters.forEach(param => {
                            if (param.key && param.value) {
                                url.searchParams.append(param.key, param.value);
                            }
                        });

                        return url.toString();
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
                                    theme: document.documentElement.classList.contains('dark') ? 'ayu-dark' : 'eclipse',
                                });

                                this.codeMirrorInstance.on('changes', () => this.payload = this.codeMirrorInstance.getValue());

                                return;
                            }

                            this.codeMirrorInstance?.setOption('mode', mode);
                        }, 0);
                    }
                },
            });

            app.component('v-key-and-value', {
                template: '#v-key-and-value-template',

                props: ['title', 'name', 'removeBtnTitle', 'fields', 'placeholders'],

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
        <link rel="stylesheet" href="https://codemirror.net/5/theme/ayu-dark.css">
    @endPushOnce
</x-admin::layouts>
