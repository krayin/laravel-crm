<x-admin::layouts>
    <x-slot:title>
        @lang('admin::app.settings.users.index.title')
    </x-slot>

    <div class="flex flex-col gap-4">
        <div class="flex items-center justify-between rounded-lg border border-gray-200 bg-white px-4 py-2 text-sm dark:border-gray-800 dark:bg-gray-900 dark:text-gray-300">
            <div class="flex flex-col gap-2">
                <!-- Breadcrumbs -->
                <x-admin::breadcrumbs name="settings.users" />

                <div class="text-xl font-bold dark:text-white">
                    @lang('admin::app.settings.users.index.title')
                </div>
            </div>

            <div class="flex items-center gap-x-2.5">
                {!! view_render_event('admin.settings.users.index.create_button.before') !!}

                <!-- Create button for User -->
                @if (bouncer()->hasPermission('settings.user.users.create'))
                    <div class="flex items-center gap-x-2.5">
                        <button
                            type="button"
                            class="primary-button"
                            @click="$refs.userSettings.openModal()"
                        >
                            @lang('admin::app.settings.users.index.create-btn')
                        </button>
                    </div>
                @endif

                {!! view_render_event('admin.settings.users.index.create_button.after') !!}
            </div>
        </div>

        <v-users-settings ref="userSettings">
            <!-- DataGrid Shimmer -->
            <x-admin::shimmer.datagrid />
        </v-users-settings>
    </div>

    @pushOnce('scripts')
        <script
            type="text/x-template"
            id="users-settings-template"
        >
            {!! view_render_event('admin.settings.users.index.datagrid.before') !!}

            <!-- Datagrid -->
            <x-admin::datagrid
                :src="route('admin.settings.users.index')"
                ref="datagrid"
            >
                <template #body="{
                    isLoading,
                    available,
                    applied,
                    selectAll,
                    sort,
                    performAction
                }">
                    <template v-if="isLoading">
                        <x-admin::shimmer.datagrid.table.body />
                    </template>

                    <template v-else>
                        <div
                            v-for="record in available.records"
                            class="row grid items-center gap-2.5 border-b px-4 py-4 text-gray-600 transition-all hover:bg-gray-50 dark:border-gray-800 dark:text-gray-300 dark:hover:bg-gray-950 max-lg:hidden"
                            :style="`grid-template-columns: repeat(${gridsCount}, minmax(0, 1fr))`"
                        >
                            <!-- Mass Actions, Title and Created By -->
                            <div class="flex select-none items-center gap-16">
                                <input
                                    type="checkbox"
                                    :name="`mass_action_select_record_${record.id}`"
                                    :id="`mass_action_select_record_${record.id}`"
                                    :value="record.id"
                                    class="peer hidden"
                                    v-model="applied.massActions.indices"
                                >

                                <label
                                    class="icon-checkbox-outline peer-checked:icon-checkbox-select cursor-pointer rounded-md text-2xl text-gray-600 peer-checked:text-brandColor dark:text-gray-300"
                                    :for="`mass_action_select_record_${record.id}`"
                                ></label>
                            </div>

                            <!-- Users Id -->
                            <p>@{{ record.id }}</p>

                            <!-- Users Name and Profile -->
                            <div class="flex items-center gap-2.5">
                                <template v-if="record.name.image">
                                    <img
                                        class="flex h-9 w-9 items-center justify-center rounded-full"
                                        :src="record.name.image"
                                        alt="record.name"
                                    />
                                </template>

                                <template v-else>
                                    <x-admin::avatar ::name="record.name.name"/>
                                </template>

                                <div class="text-sm">
                                    @{{ record.name.name }}
                                </div>
                            </div>

                            <!-- Users Email -->
                            <p class="truncate">@{{ record.email }}</p>

                            <!-- Users Status -->
                            <span
                                :class="record.status == 1 ? 'label-active' : 'label-inactive'"
                            >
                                @{{ record.status == 1 ? '@lang('admin::app.settings.users.index.active')' : '@lang('admin::app.settings.users.index.inactive')' }}
                            </span>

                            <!-- Users Creation Date -->
                            <p>@{{ record.created_at }}</p>

                            <!-- Actions -->
                            <div class="flex justify-end">
                                <a @click="editModal(record.actions.find(action => action.index === 'edit')?.url)">
                                    <span
                                        :class="record.actions.find(action => action.index === 'edit')?.icon"
                                        class="cursor-pointer rounded-md p-1.5 text-2xl transition-all hover:bg-gray-200 dark:hover:bg-gray-800 max-sm:place-self-center"
                                    >
                                    </span>
                                </a>

                                <a @click="performAction(record.actions.find(action => action.index === 'delete'))">
                                    <span
                                        :class="record.actions.find(action => action.index === 'delete')?.icon"
                                        class="cursor-pointer rounded-md p-1.5 text-2xl transition-all hover:bg-gray-200 dark:hover:bg-gray-800 max-sm:place-self-center"
                                    >
                                    </span>
                                </a>
                            </div>
                        </div>

                        <!-- Mobile Card View -->
                        <div
                            class="hidden border-b px-4 py-4 text-black dark:border-gray-800 dark:text-gray-300 max-lg:block"
                            v-for="record in available.records"
                        >
                            <div class="mb-2 flex items-center justify-between">
                                <!-- Mass Actions for Mobile Cards -->
                                <div class="flex w-full items-center justify-between gap-2">
                                    <p v-if="available.massActions.length">
                                        <label :for="`mass_action_select_record_${record[available.meta.primary_column]}`">
                                            <input
                                                type="checkbox"
                                                :name="`mass_action_select_record_${record[available.meta.primary_column]}`"
                                                :value="record[available.meta.primary_column]"
                                                :id="`mass_action_select_record_${record[available.meta.primary_column]}`"
                                                class="peer hidden"
                                                v-model="applied.massActions.indices"
                                            >

                                            <span class="icon-checkbox-outline peer-checked:icon-checkbox-select cursor-pointer rounded-md text-2xl text-gray-500 peer-checked:text-brandColor">
                                            </span>
                                        </label>
                                    </p>

                                    <!-- Actions for Mobile -->
                                    <div
                                        class="flex w-full items-center justify-end"
                                        v-if="available.actions.length"
                                    >
                                        <!-- Actions -->
                                        <a @click="editModal(record.actions.find(action => action.index === 'edit')?.url)">
                                            <span
                                                :class="record.actions.find(action => action.index === 'edit')?.icon"
                                                class="cursor-pointer rounded-md p-1.5 text-2xl transition-all hover:bg-gray-200 dark:hover:bg-gray-800 max-sm:place-self-center"
                                            >
                                            </span>
                                        </a>

                                        <a @click="performAction(record.actions.find(action => action.index === 'delete'))">
                                            <span
                                                :class="record.actions.find(action => action.index === 'delete')?.icon"
                                                class="cursor-pointer rounded-md p-1.5 text-2xl transition-all hover:bg-gray-200 dark:hover:bg-gray-800 max-sm:place-self-center"
                                            >
                                            </span>
                                        </a>
                                    </div>
                                </div>
                            </div>

                            <!-- Card Content -->
                            <div class="grid gap-2">
                                <template v-for="column in available.columns">
                                    <div class="flex flex-wrap items-baseline gap-x-2">
                                        <span class="text-slate-600 dark:text-gray-300" v-html="column.label + ':'"></span>
                                        <span class="break-words font-medium text-slate-900 dark:text-white" v-html="record[column.index]"></span>
                                    </div>
                                </template>
                            </div>
                        </div>
                        </template>
                </template>
            </x-admin::datagrid>

            {!! view_render_event('admin.users.index.datagrid.after') !!}

            <x-admin::form
                v-slot="{ meta, values, errors, handleSubmit }"
                as="div"
                ref="modalForm"
            >
                <form
                    @submit="handleSubmit($event, updateOrCreate)"
                    ref="userForm"
                >
                    {!! view_render_event('admin.settings.users.index.form_controls.before') !!}

                    <x-admin::modal
                        ref="userUpdateAndCreateModal"
                        @toggle="handleToggle"
                    >
                        <!-- Modal Header -->
                        <x-slot:header>
                            <p class="text-lg font-bold text-gray-800 dark:text-white">
                                @{{
                                    selectedType == 'create'
                                    ? "@lang('admin::app.settings.users.index.create.title')"
                                    : "@lang('admin::app.settings.users.index.edit.title')"
                                }}
                            </p>
                        </x-slot>

                        <!-- Modal Content -->
                        <x-slot:content>
                            <x-admin::form.control-group.control
                                type="hidden"
                                name="id"
                                v-model="user.id"
                            />

                            {!! view_render_event('admin.settings.users.index.form.name.before') !!}

                            <!-- Name -->
                            <x-admin::form.control-group>
                                <x-admin::form.control-group.label class="required">
                                    @lang('admin::app.settings.users.index.create.name')
                                </x-admin::form.control-group.label>

                                <x-admin::form.control-group.control
                                    type="text"
                                    id="name"
                                    name="name"
                                    rules="required"
                                    v-model="user.name"
                                    :label="trans('admin::app.settings.users.index.create.name')"
                                    :placeholder="trans('admin::app.settings.users.index.create.name')"
                                />

                                <x-admin::form.control-group.error control-name="name" />
                            </x-admin::form.control-group>

                            {!! view_render_event('admin.settings.users.index.form.name.after') !!}

                            {!! view_render_event('admin.settings.users.index.form.email.before') !!}

                            <!-- Email -->
                            <x-admin::form.control-group>
                                <x-admin::form.control-group.label class="required">
                                    @lang('admin::app.settings.users.index.create.email')
                                </x-admin::form.control-group.label>

                                <x-admin::form.control-group.control
                                    type="email"
                                    id="email"
                                    name="email"
                                    v-model="user.email"
                                    rules="required"
                                    :label="trans('admin::app.settings.users.index.create.email')"
                                    :placeholder="trans('admin::app.settings.users.index.create.email')"
                                />

                                <x-admin::form.control-group.error control-name="email" />
                            </x-admin::form.control-group>

                            {!! view_render_event('admin.settings.users.index.form.email.after') !!}

                            {!! view_render_event('admin.settings.users.index.form.password.before') !!}

                            <div class="flex gap-4">
                                <!-- Password -->
                                <x-admin::form.control-group class="flex-1">
                                    <x-admin::form.control-group.label ::class="user.id ? '' : 'required'">
                                        @lang('admin::app.settings.users.index.create.password')
                                    </x-admin::form.control-group.label>

                                    <x-admin::form.control-group.control
                                        type="password"
                                        id="password"
                                        name="password"
                                        ::rules="user.id ? '' : 'required|min:6'"
                                        :label="trans('admin::app.settings.users.index.create.password')"
                                        :placeholder="trans('admin::app.settings.users.index.create.password')"
                                        ref="password"
                                    />

                                    <x-admin::form.control-group.error control-name="password" />
                                </x-admin::form.control-group>

                                <!-- Confirm Password -->
                                <x-admin::form.control-group class="flex-1">
                                    <x-admin::form.control-group.label>
                                        @lang('admin::app.settings.users.index.create.confirm-password')
                                    </x-admin::form.control-group.label>

                                    <x-admin::form.control-group.control
                                        type="password"
                                        id="confirm_password"
                                        name="confirm_password"
                                        ::rules="values.password ? 'confirmed:@password' : ''"
                                        :label="trans('admin::app.settings.users.index.create.password')"
                                        :placeholder="trans('admin::app.settings.users.index.create.confirm-password')"
                                    />

                                    <x-admin::form.control-group.error control-name="confirm_password" />
                                </x-admin::form.control-group>
                            </div>

                            {!! view_render_event('admin.settings.users.index.form.password.after') !!}

                            {!! view_render_event('admin.settings.users.index.form.role_id.before') !!}

                            <div class="flex gap-4">
                                <!-- Role -->
                                <x-admin::form.control-group class="flex-1">
                                    <x-admin::form.control-group.label class="required">
                                        @lang('admin::app.settings.users.index.create.role')
                                    </x-admin::form.control-group.label>

                                    <x-admin::form.control-group.control
                                        type="select"
                                        name="role_id"
                                        rules="required"
                                        v-model="user.role_id"
                                        :label="trans('admin::app.settings.users.index.create.role')"
                                    >
                                        <option
                                            v-for="role in roles"
                                            :key="role.id"
                                            :value="role.id"
                                        >
                                            @{{ role.name }}
                                        </option>
                                    </x-admin::form.control-group.control>

                                    <x-admin::form.control-group.error control-name="role_id" />
                                </x-admin::form.control-group>

                                <!-- Permission -->
                                <x-admin::form.control-group class="flex-1">
                                    <x-admin::form.control-group.label class="required">
                                        @lang('admin::app.settings.users.index.create.view-permission')
                                    </x-admin::form.control-group.label>

                                    <x-admin::form.control-group.control
                                        type="select"
                                        name="view_permission"
                                        rules="required"
                                        v-model="user.view_permission"
                                        value="global"
                                        :label="trans('admin::app.settings.users.index.create.view-permission')"
                                    >
                                        <!-- Default Option -->
                                        <option  value="global" selected>
                                            @lang('admin::app.settings.users.index.create.global')
                                        </option>

                                        <option value="group">
                                            @lang('admin::app.settings.users.index.create.group')
                                        </option>

                                        <option value="individual">
                                            @lang('admin::app.settings.users.index.create.individual')
                                        </option>
                                    </x-admin::form.control-group.control>

                                    <x-admin::form.control-group.error control-name="view_permission" />
                                </x-admin::form.control-group>
                            </div>

                            {!! view_render_event('admin.settings.users.index.form.role_id.after') !!}

                            {!! view_render_event('admin.settings.users.index.form.role_id.before') !!}

                            <template v-if="user.view_permission === 'group'">
                                <!-- Group -->
                                <x-admin::form.control-group>
                                    <x-admin::form.control-group.label class="required">
                                        @lang('admin::app.settings.users.index.create.group')
                                    </x-admin::form.control-group.label>

                                    <v-field
                                        name="groups[]"
                                        label="@lang('admin::app.settings.users.index.create.group')"
                                        multiple
                                        v-model="user.groups"
                                        rules="required"
                                    >
                                        <select
                                            name="groups[]"
                                            class="flex min-h-[39px] w-full rounded-md border px-3 py-2 text-sm text-gray-600 transition-all hover:border-gray-400 focus:border-gray-400 dark:border-gray-800 dark:bg-gray-900 dark:text-gray-300 dark:hover:border-gray-400 dark:focus:border-gray-400"
                                            :class="[errors['groups[]'] ? 'border !border-red-600 hover:border-red-600' : '']"
                                            multiple
                                            v-model="user.groups"
                                        >
                                            <option
                                                v-for="group in groups"
                                                :value="group.id"
                                                :text="group.name"
                                            >
                                            </option>
                                        </select>
                                    </v-field>

                                    <x-admin::form.control-group.error name="groups[]" />
                                </x-admin::form.control-group>
                            </template>

                            {!! view_render_event('admin.settings.users.index.form.role_id.after') !!}

                            {!! view_render_event('admin.settings.users.index.form.status.before') !!}

                            <!-- Status -->
                            <x-admin::form.control-group>
                                <x-admin::form.control-group.label for="status">
                                    @lang('admin::app.settings.users.index.create.status')
                                </x-admin::form.control-group.label>

                                <input
                                    type="hidden"
                                    name="status"
                                    :value="0"
                                />

                                <x-admin::form.control-group.control
                                    type="switch"
                                    name="status"
                                    value="1"
                                    :label="trans('admin::app.settings.users.index.create.status')"
                                    ::checked="parseInt(user.status || 0)"
                                />
                            </x-admin::form.control-group>

                            {!! view_render_event('admin.settings.users.index.form.status.after') !!}
                        </x-slot>

                        <!-- Modal Footer -->
                        <x-slot:footer>
                            {!! view_render_event('admin.settings.users.index.modal.footer.save_button.before') !!}

                            <!-- Save Button -->
                            <x-admin::button
                                button-type="submit"
                                class="primary-button justify-center"
                                :title="trans('admin::app.settings.users.index.create.save-btn')"
                                ::loading="isProcessing"
                                ::disabled="isProcessing"
                            />

                            {!! view_render_event('admin.settings.users.index.modal.footer.save_button.after') !!}
                        </x-slot>
                    </x-admin::modal>

                    {!! view_render_event('admin.settings.users.index.form_controls.after') !!}
                </form>
            </x-admin::form>
        </script>

        <script type="module">
            app.component('v-users-settings', {
                template: '#users-settings-template',

                data() {
                    return {
                        isProcessing: false,

                        roles: @json($roles),

                        groups:  @json($groups),

                        user: {
                            view_permission: 'global',
                        },
                    };
                },

                computed: {
                    gridsCount() {
                        let count = this.$refs.datagrid.available.columns.length;

                        if (this.$refs.datagrid.available.actions.length) {
                            ++count;
                        }

                        if (this.$refs.datagrid.available.massActions.length) {
                            ++count;
                        }

                        return count;
                    },

                    selectedType() {
                        return this.user.id ? 'edit' : 'create';
                    },
                },

                mounted() {
                    @if(request('action') === 'create')
                        this.openModal();
                    @endif
                },

                methods: {
                    handleToggle(state) {
                        if (state.isActive) {
                            return;
                        }

                        const url = new URL(window.location.href);

                        url.searchParams.delete('action');

                        window.history.replaceState({}, '', url);
                    },

                    openModal() {
                        this.user = {
                            groups: [],
                        };

                        this.$refs.userUpdateAndCreateModal.toggle();
                    },

                    updateOrCreate(params, {resetForm, setErrors}) {
                        const userForm = new FormData(this.$refs.userForm);

                        userForm.append('_method', params.id ? 'put' : 'post');

                        this.isProcessing = true;

                        this.$axios.post(
                                params.id
                                ? `{{ route('admin.settings.users.update', '') }}/${params.id}`
                                : "{{ route('admin.settings.users.store') }}", userForm
                            )
                            .then(response => {
                                this.isProcessing = false;

                                this.$refs.userUpdateAndCreateModal.toggle();

                                this.$emitter.emit('add-flash', { type: 'success', message: response.data.message });

                                this.$refs.datagrid.get();

                                resetForm();
                            }).catch(error => {
                                this.isProcessing = false;

                                if (error.response.status === 422) {
                                    setErrors(error.response.data.errors);
                                }
                            });
                    },

                    editModal(url) {
                        this.$axios.get(url)
                            .then(response => {
                                this.user = response.data.data;

                                this.user.groups = this.user.groups.map(group => group.id);

                                this.$refs.userUpdateAndCreateModal.toggle();
                            })
                            .catch(error => {});
                    },
                },
            });
        </script>
    @endPushOnce
</x-admin::layouts>
