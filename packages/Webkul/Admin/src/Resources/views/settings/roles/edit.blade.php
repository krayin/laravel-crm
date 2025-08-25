<x-admin::layouts>
    <!-- Page Title -->
    <x-slot:title>
        @lang('admin::app.settings.roles.edit.title')
    </x-slot>

    {!! view_render_event('admin.settings.roles.edit.form.before', ['role' => $role]) !!}

    <x-admin::form
        method="PUT"
        :action="route('admin.settings.roles.update', $role->id)"
    >
        <div class="flex flex-col gap-4">
            <div class="flex items-center justify-between rounded-lg border border-gray-200 bg-white px-4 py-2 text-sm dark:border-gray-800 dark:bg-gray-900 dark:text-gray-300">
                <div class="flex flex-col gap-2">
                    {!! view_render_event('admin.settings.roles.edit.breadcrumbs.before', ['role' => $role]) !!}

                    <!-- Breadcrumbs -->
                    <x-admin::breadcrumbs 
                        name="settings.roles.edit"
                        :entity="$role"
                    />

                    {!! view_render_event('admin.settings.roles.edit.breadcrumbs.after', ['role' => $role]) !!}

                    <div class="text-xl font-bold dark:text-white">
                        @lang('admin::app.settings.roles.edit.title')
                    </div>
                </div>

                <div class="flex items-center gap-x-2.5">
                    <div class="flex items-center gap-x-2.5">
                        {!! view_render_event('admin.settings.roles.edit.save_button.before', ['role' => $role]) !!}

                        @if (bouncer()->hasPermission('settings.user.roles.edit'))
                            <!-- Save button for roles -->
                            <button
                                type="submit"
                                class="primary-button"
                            >
                                @lang('admin::app.settings.roles.edit.save-btn')
                            </button>
                        @endif

                        {!! view_render_event('admin.settings.roles.edit.save_button.after', ['role' => $role]) !!}
                    </div>
                </div>
            </div>

            {!! view_render_event('admin.settings.roles.edit.content.before', ['role' => $role]) !!}

            <!-- body content -->
            <div class="flex gap-2.5 max-xl:flex-wrap">
                {!! view_render_event('admin.settings.roles.edit.content.left.before', ['role' => $role]) !!}

                <!-- Left sub-component -->
                <div class="flex flex-1 flex-col gap-2 max-xl:flex-auto">
                    <div class="box-shadow rounded-lg border border-gray-200 bg-white p-4 dark:border-gray-800 dark:bg-gray-900">
                        <p class="mb-4 text-base font-semibold text-gray-800 dark:text-white">
                            @lang('admin::app.settings.roles.edit.access-control')
                        </p>

                        <!-- Edit Role for  -->
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

                {!! view_render_event('admin.settings.roles.edit.content.left.after', ['role' => $role]) !!}

                {!! view_render_event('admin.settings.roles.edit.content.right.before', ['role' => $role]) !!}

                <!-- Right sub-component -->
                <div class="flex w-[360px] max-w-full flex-col gap-2 max-sm:w-full">
                    {!! view_render_event('admin.settings.roles.edit.accordion.general.before', ['role' => $role]) !!}

                    <x-admin::accordion>
                        <x-slot:header>
                            <div class="flex items-center justify-between">
                                <p class="p-2.5 text-base font-semibold text-gray-800 dark:text-white">
                                    @lang('admin::app.settings.roles.edit.general')
                                </p>
                            </div>
                        </x-slot>

                        <x-slot:content>
                            {!! view_render_event('admin.settings.roles.edit.form.name.before', ['role' => $role]) !!}

                            <!-- Name -->
                            <x-admin::form.control-group>
                                <x-admin::form.control-group.label class="required">
                                    @lang('admin::app.settings.roles.edit.name')
                                </x-admin::form.control-group.label>

                                <x-admin::form.control-group.control
                                    type="text"
                                    id="name"
                                    name="name"
                                    rules="required"
                                    value="{{ old('name') ?: $role->name }}"
                                    :label="trans('admin::app.settings.roles.edit.name')"
                                    :placeholder="trans('admin::app.settings.roles.edit.name')"
                                />

                                <x-admin::form.control-group.error control-name="name" />
                            </x-admin::form.control-group>

                            {!! view_render_event('admin.settings.roles.edit.form.name.after', ['role' => $role]) !!}

                            {!! view_render_event('admin.settings.roles.edit.form.description.before', ['role' => $role]) !!}

                            <!-- Description -->
                            <x-admin::form.control-group class="!mb-0">
                                <x-admin::form.control-group.label class="required">
                                    @lang('admin::app.settings.roles.edit.description')
                                </x-admin::form.control-group.label>

                                <x-admin::form.control-group.control
                                    type="textarea"
                                    id="description"
                                    name="description"
                                    rules="required"
                                    value="{{ old('description') ?: $role->description }}"
                                    :label="trans('admin::app.settings.roles.edit.description')"
                                    :placeholder="trans('admin::app.settings.roles.edit.description')"
                                />

                                <x-admin::form.control-group.error control-name="description" />
                            </x-admin::form.control-group>

                            {!! view_render_event('admin.settings.roles.edit.form.description.after', ['role' => $role]) !!}
                        </x-slot>
                    </x-admin::accordion>

                    {!! view_render_event('admin.settings.roles.edit.accordion.general.after', ['role' => $role]) !!}
                </div>
            </div>

            {!! view_render_event('admin.settings.roles.edit.content.after', ['role' => $role]) !!}
        </div>
    </x-admin::form>

    {!! view_render_event('admin.settings.roles.edit.form.after', ['role' => $role]) !!}

    @pushOnce('scripts')
        <script
            type="text/x-template"
            id="v-access-control-template"
        >
            <div>
                {!! view_render_event('admin.settings.roles.edit.form.permission_type.before', ['role' => $role]) !!}

                <!-- Permission Type -->
                <x-admin::form.control-group>
                    <x-admin::form.control-group.label class="required">
                        @lang('admin::app.settings.roles.edit.permissions')
                    </x-admin::form.control-group.label>

                    <x-admin::form.control-group.control
                        type="select"
                        id="permission_type"
                        name="permission_type"
                        v-model="permission_type"
                        :label="trans('admin::app.settings.roles.edit.permissions')"
                        :placeholder="trans('admin::app.settings.roles.edit.permissions')"
                    >
                        <option value="custom">
                            @lang('admin::app.settings.roles.edit.custom')
                        </option>

                        <option value="all">
                            @lang('admin::app.settings.roles.edit.all')
                        </option>
                    </x-admin::form.control-group.control>

                    <x-admin::form.control-group.error control-name="permission_type" />
                </x-admin::form.control-group>

                {!! view_render_event('admin.settings.roles.edit.form.permission_type.after', ['role' => $role]) !!}
                
                <!-- Tree structure -->
                <div v-if="permission_type == 'custom'">
                    <x-admin::form.control-group.error control-name="permissions" />

                    {!! view_render_event('admin.settings.roles.edit.form.tree_view.before', ['role' => $role]) !!}

                    <x-admin::tree.view
                        input-type="checkbox"
                        value-field="key"
                        id-field="key"
                        :items="json_encode(acl()->getItems())"
                        :value="json_encode($role->permissions ?? [])"
                        :fallback-locale="config('app.fallback_locale')"
                    />

                    {!! view_render_event('admin.settings.roles.edit.form.tree_view.after', ['role' => $role]) !!}
                </div>
            </div>
        </script>

        <script type="module">
            app.component('v-access-control', {
                template: '#v-access-control-template',

                data() {
                    return {
                        permission_type: "{{ $role->permission_type }}"
                    };
                }
            })
        </script>
    @endPushOnce
</x-admin::layouts>
