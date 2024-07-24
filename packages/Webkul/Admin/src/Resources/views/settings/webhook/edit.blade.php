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

        @include('admin::common.custom-attributes.edit.lookup')
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
                                    @lang('Enter the details of webhooks')
                                </p>
                            </div>
                        </div>

                        <!-- Name -->
                        <x-admin::form.control-group class="!w-1/2">
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
                        <x-admin::form.control-group class="!w-1/2">
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
                        <x-admin::form.control-group class="!w-1/2">
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

                        <!-- Method and URL endpoint -->
                        <x-admin::form.control-group class="!w-1/2">
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

                        <x-admin::form.control-group class="!w-1/2 my-2">
                            <x-admin::form.control-group.label class="!text-gray-600 required">
                                @lang('Parameters')
                            </x-admin::form.control-group.label>
                        
                            <div class="flex flex-col">
                                <div 
                                    class="flex gap-3 my-2 items-center justify-between"
                                    v-for="(parameter, index) in parameters"
                                    :key="index"
                                >
                                    <div class="w-1/2">
                                        <x-admin::form.control-group.control
                                            type="text"
                                            ::id="`query_params[${index}][key]`"
                                            ::name="`query_params[${index}][key]`"
                                            v-model="parameter.key"
                                            rules="required"
                                            :label="trans('Key')"
                                            :placeholder="trans('Key')"
                                        />
                        
                                        <x-admin::form.control-group.error ::name="`query_params[${index}][key]`" />
                                    </div>
                        
                                    <div class="w-full">
                                        <x-admin::form.control-group.control
                                            type="text"
                                            ::id="`query_params[${index}][value]`"
                                            ::name="`query_params[${index}][value]`"
                                            v-model="parameter.value"
                                            rules="required"
                                            :label="trans('Value')"
                                            :placeholder="trans('Value')"
                                        />
                        
                                        <x-admin::form.control-group.error ::name="`query_params[${index}][value]`" />
                                    </div>
                        
                                    <i 
                                        class="cursor-pointer rounded-md p-1.5 ml-1 text-2xl transition-all hover:bg-gray-100 dark:hover:bg-gray-950 icon-delete"
                                        @click="removeParameter(index)"
                                        v-if="parameters.length > 1"
                                    ></i>
                                </div>
                        
                                <p 
                                    class="py-2 text-xs text-brandColor hover:underline hover:text-sky-500 cursor-pointer"
                                    @click="addParameter(index)" 
                                >
                                    @lang('Add New Parameters')
                                </p>
                            </div>
                        </x-admin::form.control-group>
                        
                        <div class="flex items-center justify-between rounded-sm border w-1/2 bg-white border-gray-200 px-4 py-2 text-sm dark:border-gray-800 dark:bg-gray-900 dark:text-gray-300">
                            <div class="flex gap-3 my-2">
                                <div class="text-xs font-sm dark:text-gray-300">
                                    @lang('URL Preview :') <span class="text-sm font-medium text-gray-800 dark:text-gray-300">@{{ urlEndPoint }}</span>
                                </div>
                            </div>
                        </div>

                        <hr class="my-4 w-1/2"/>

                        <x-admin::form.control-group class="!w-1/2">
                            <x-admin::form.control-group.label class="!text-gray-600 required">
                                @lang('Headers')
                            </x-admin::form.control-group.label>
                        
                            <div class="flex flex-col">
                                <div 
                                    class="flex gap-3 my-2 items-center justify-between"
                                    v-for="(header, index) in headers"
                                    :key="index"
                                >
                                    <div class="w-1/2">
                                        <x-admin::form.control-group.control
                                            type="text"
                                            ::id="`headers[${index}][key]`"
                                            ::name="`headers[${index}][key]`"
                                            v-model="header.key"
                                            rules="required"
                                            :label="trans('Key')"
                                            :placeholder="trans('Key')"
                                        />
                        
                                        <x-admin::form.control-group.error ::name="`headers[${index}][key]`"/>
                                    </div>
                        
                                    <div class="w-full">
                                        <x-admin::form.control-group.control
                                            type="text"
                                            ::id="`headers[${index}][value]`"
                                            ::name="`headers[${index}][value]`"
                                            v-model="header.value"
                                            rules="required"
                                            :label="trans('Value')"
                                            :placeholder="trans('Value')"
                                        />
                        
                                        <x-admin::form.control-group.error ::name="`headers[${index}][value]`"/>
                                    </div>
                        
                                    <i 
                                        class="cursor-pointer rounded-md p-1.5 ml-1 text-2xl transition-all hover:bg-gray-100 dark:hover:bg-gray-950 icon-delete"
                                        @click="removeHeader(index)"
                                        v-if="headers.length > 1"
                                    ></i>
                                </div>
                        
                                <p 
                                    class="py-2 text-xs text-brandColor hover:underline hover:text-sky-500 cursor-pointer"
                                    @click="addHeader(index)" 
                                >
                                    @lang('Add New Headers')
                                </p>
                            </div>
                        </x-admin::form.control-group>

                        <hr class="my-4 w-1/2"/>

                        <x-admin::form.control-group class="!w-1/2">
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
                                            @click="contentType = 'raw'"
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
                                                    @click="rawType = 'Default'"
                                                >
                                                    <div class="items flex items-center gap-1.5">
                                                        @lang('Default')
                                                    </div>
                                                </span>

                                                <span
                                                    class="whitespace-no-wrap flex cursor-pointer items-center justify-between gap-1.5 rounded-t px-2 py-2 text-sm text-gray-600 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-950"
                                                    @click="rawType = 'Json'"
                                                >
                                                    <div class="items flex items-center gap-1.5">
                                                        @lang('JSON')
                                                    </div>
                                                </span>

                                                <span
                                                    class="whitespace-no-wrap flex cursor-pointer items-center justify-between gap-1.5 rounded-t px-2 py-2 text-sm text-gray-600 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-950"
                                                    @click="rawType = 'Text'"
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
                           
                            <x-admin::form.control-group.control
                                type="textarea"
                                id="payload"
                                name="payload"
                                rules="required"
                                :value="old('payload') ?? $webhook->payload"
                                :label="trans('Payload')"
                                :placeholder="trans('Payload')"
                            />

                            <x-admin::form.control-group.error control-name="payload" />
                        </x-admin::form.control-group>
                    </div>
                </div>
            </div>
        </script>

        <script type="module">
            app.component('v-webhooks', {
                template: '#webhooks-template',

                data() {
                    return {
                        baseUrl: '{{ $webhook->end_point ?? '' }}',

                        parameters: @json($webhook->query_params),

                        headers: @json($webhook->headers),

                        contentType: '{{ $webhook->payload_type ?? 'default' }}',

                        rawType: '{{ $webhook->raw_payload_type ?? 'Json' }}',
                    };
                },

                computed: {
                    /**
                     * Get the URL endpoint with the parameters
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
                     * Add a new parameter
                     * 
                     * @returns {void}
                     */
                    addParameter() {
                        this.parameters.push({ key: '', value: '' });
                    },

                    /**
                     * Remove a parameter
                     * 
                     * @returns {void}
                     */
                    removeParameter(index) {
                        this.parameters.splice(index, 1);
                    },

                    /**
                     * Add a new parameter
                     * 
                     * @returns {void}
                     */
                    addHeader() {
                        this.headers.push({ key: '', value: '' });
                    },

                    /**
                     * Remove a header
                     * 
                     * @returns {void}
                     */
                    removeHeader(index) {
                        this.headers.splice(index, 1);
                    },
                },
            });
        </script>
    @endPushOnce
</x-admin::layouts>
