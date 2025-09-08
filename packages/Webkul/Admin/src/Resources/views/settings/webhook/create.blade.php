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
                    {!! view_render_event('admin.settings.webhook.edit.breadrumbs.before') !!}

                    <!-- Breadcrumbs -->
                    <x-admin::breadcrumbs name="settings.webhooks.create" />

                    {!! view_render_event('admin.settings.webhook.edit.breadrumbs.after') !!}

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
                            </div>
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

        <script type="module">
            app.component('v-webhooks', {
                template: '#v-webhooks-template',

                props: ['errors'],

                data() {
                    return {
                        baseUrl: '',
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
