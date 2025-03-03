<x-admin::layouts>
    <!-- Page Title -->
    <x-slot:title>
        @lang('admin::app.settings.roles.create.title')
    </x-slot>

    {!! view_render_event('admin.settings.roles.create.form.before') !!}

    <!-- Create Form -->
    <x-admin::form :action="route('admin.settings.roles.store')">
        <div class="flex flex-col gap-4">
            <div class="flex items-center justify-between rounded-lg border border-gray-200 bg-white px-4 py-2 text-sm dark:border-gray-800 dark:bg-gray-900 dark:text-gray-300">
                <div class="flex flex-col gap-2">
                    {!! view_render_event('admin.settings.roles.create.breadcrumbs.before') !!}

                    <!-- Breadcrumbs -->
                    <x-admin::breadcrumbs name="settings.roles.create" />

                    {!! view_render_event('admin.settings.roles.create.breadcrumbs.after') !!}

                    <div class="text-xl font-bold dark:text-white">
                        @lang('admin::app.settings.roles.create.title')
                    </div>
                </div>

                <div class="flex items-center gap-x-2.5">
                    <div class="flex items-center gap-x-2.5">
                        {!! view_render_event('admin.settings.roles.create.create_button.before') !!}

                        @if (bouncer()->hasPermission('settings.user.roles.create'))
                            <!-- Create button for Roles -->
                            <button
                                type="submit"
                                class="primary-button"
                            >
                                @lang('admin::app.settings.roles.create.save-btn')
                            </button>
                        @endif

                        {!! view_render_event('admin.settings.roles.create.create_button.after') !!}
                    </div>
                </div>
            </div>

            {!! view_render_event('admin.settings.roles.create.content.before') !!}

            <!-- body content -->
            <div class="flex gap-2.5 max-xl:flex-wrap">
                {!! view_render_event('admin.settings.roles.create.content.left.before') !!}

                <!-- Left sub-component -->
                <div class="flex flex-1 flex-col gap-2 max-xl:flex-auto">
                    <div class="box-shadow rounded-lg border border-gray-200 bg-white p-4 dark:border-gray-800 dark:bg-gray-900">
                        <!-- Create Role for -->
                        <v-access-control>
                            <!-- Shimmer Effect -->
                            <div class="mb-4">
                                <div class="shimmer mb-1.5 h-4 w-24"></div>

                                <div class="custom-select h-11 w-full rounded-md border bg-white px-3 py-2.5 text-sm font-normal text-gray-600 transition-all hover:border-gray-400 dark:border-gray-800 dark:bg-gray-900 dark:text-gray-300 dark:hover:border-gray-400"></div>
                            </div>

                            <!-- Roles Checkbox -->
                            <x-admin::shimmer.tree />
                        </v-access-control>
                    </div>
                </div>

                {!! view_render_event('admin.settings.roles.create.content.left.after') !!}

                {!! view_render_event('admin.settings.roles.create.content.right.before') !!}

                <!-- Right sub-component -->
                <div class="flex w-[360px] max-w-full flex-col gap-2 max-sm:w-full">
                    {!! view_render_event('admin.settings.roles.create.accordion.general.before') !!}

                    <x-admin::accordion class="rounded-lg">
                        <x-slot:header>
                            <div class="flex items-center justify-between">
                                <p class="p-2.5 text-base font-semibold text-gray-800 dark:text-white">
                                    @lang('admin::app.settings.roles.create.general')
                                </p>
                            </div>
                        </x-slot>

                        <x-slot:content>
                            {!! view_render_event('admin.settings.roles.create.form.name.before') !!}

                            <!-- Name -->
                            <x-admin::form.control-group>
                                <x-admin::form.control-group.label class="required">
                                    @lang('admin::app.settings.roles.create.name')
                                </x-admin::form.control-group.label>

                                <x-admin::form.control-group.control
                                    type="text"
                                    id="name"
                                    name="name"
                                    rules="required"
                                    value="{{ old('name') }}"
                                    :label="trans('admin::app.settings.roles.create.name')"
                                    :placeholder="trans('admin::app.settings.roles.create.name')"
                                />

                                <x-admin::form.control-group.error control-name="name" />
                            </x-admin::form.control-group>

                            {!! view_render_event('admin.settings.roles.create.form.name.after') !!}

                            {!! view_render_event('admin.settings.roles.create.form.description.before') !!}

                            <!-- Description -->
                            <x-admin::form.control-group class="!mb-0">
                                <x-admin::form.control-group.label class="required">
                                    @lang('admin::app.settings.roles.create.description')
                                </x-admin::form.control-group.label>

                                <x-admin::form.control-group.control
                                    type="textarea"
                                    id="description"
                                    name="description"
                                    rules="required"
                                    :value="old('description')"
                                    :label="trans('admin::app.settings.roles.create.description')"
                                    :placeholder="trans('admin::app.settings.roles.create.description')"
                                />

                                <x-admin::form.control-group.error control-name="description" />
                            </x-admin::form.control-group>

                            {!! view_render_event('admin.settings.roles.create.form.description.after') !!}
                        </x-slot>
                    </x-admin::accordion>

                    {!! view_render_event('admin.settings.roles.create.accordion.general.after') !!}
                </div>

                {!! view_render_event('admin.settings.roles.create.content.right.after') !!}
            </div>

            {!! view_render_event('admin.settings.roles.create.content.after') !!}
        </div>
    </x-admin::form>

    {!! view_render_event('admin.settings.roles.create.form.after') !!}

    @pushOnce('scripts')
        <script
            type="text/x-template"
            id="v-access-control-template"
        >
            <div>
                {!! view_render_event('admin.settings.roles.create.form.permission_type.before') !!}

                <!-- Permission Type -->
                <x-admin::form.control-group class="!mb-0">
                    <x-admin::form.control-group.label class="required">
                        @lang('admin::app.settings.roles.create.permissions')
                    </x-admin::form.control-group.label>

                    <x-admin::form.control-group.control
                        type="select"
                        name="permission_type"
                        id="permission_type"
                        rules="required"
                        :label="trans('admin::app.settings.roles.create.permissions')"
                        :placeholder="trans('admin::app.settings.roles.create.permissions')"
                        v-model="permission_type"
                    >
                        <option value="custom">
                            @lang('admin::app.settings.roles.create.custom')
                        </option>

                        <option value="all">
                            @lang('admin::app.settings.roles.create.all')
                        </option>
                    </x-admin::form.control-group.control>

                    <x-admin::form.control-group.error control-name="permission_type" />
                </x-admin::form.control-group>

                {!! view_render_event('admin.settings.roles.create.form.permission_type.after') !!}

                <div v-if="permission_type == 'custom'">
                    <x-admin::form.control-group.error control-name="permissions" />

                    {!! view_render_event('admin.settings.roles.create.form.tree-view.before') !!}

                    <x-admin::tree.view
                        input-type="checkbox"
                        value-field="key"
                        id-field="key"
                        :items="json_encode(acl()->getItems())"
                        :fallback-locale="config('app.fallback_locale')"
                    />

                    {!! view_render_event('admin.settings.roles.create.form.tree-view.after') !!}
                </div>
            </div>
        </script>

        <script type="module">
            app.component('v-access-control', {
                template: '#v-access-control-template',

                data() {
                    return {
                        permission_type: 'custom',
                    };
                },
            });
        </script>
    @endPushOnce
</x-admin::layouts>
