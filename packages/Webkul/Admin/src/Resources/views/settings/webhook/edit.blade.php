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
                            </div>
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

        <script type="module">
            app.component('v-webhooks', {
                template: '#v-webhooks-template',

                props: ['errors'],

                data() {
                    return {
                        baseUrl: '{{ $webhook->end_point ?? '' }}',
                    };
                },

                watch: {
                    baseUrl() {
                        this.urlEndPoint();
                    },
                },

                methods: {
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
        </script>
    @endPushOnce
</x-admin::layouts>
