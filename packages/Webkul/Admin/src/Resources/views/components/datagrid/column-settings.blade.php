{!! view_render_event('admin.components.datagrid.column_settings.before') !!}

<v-datagrid-column-settings
    :columns="available.columns"
    @update-column-visibility="updateColumnVisibility"
>
</v-datagrid-column-settings>

{!! view_render_event('admin.components.datagrid.column_settings.after') !!}

@pushOnce('scripts')
    <script
        type="text/x-template"
        id="v-datagrid-column-settings-template"
    >
        {!! view_render_event('admin.components.datagrid.column_settings.dropdown.before') !!}

        <x-admin::dropdown ::close-on-click="false">
            <!-- Dropdown Toggler -->
            <x-slot:toggle>
                {!! view_render_event('admin.components.datagrid.column_settings.dropdown.toggle_button.before') !!}

                <div class="icon-setting cursor-pointer rounded-md border p-2 text-lg text-gray-600 transition-all hover:border-gray-400 dark:border-gray-800 dark:text-gray-300 dark:hover:border-gray-400"></div>

                {!! view_render_event('admin.components.datagrid.column_settings.dropdown.toggle_button.after') !!}
            </x-slot>

            <!-- Dropdown Content -->
            <x-slot:content>
                {!! view_render_event('admin.components.datagrid.column_settings.dropdown.content.before') !!}

                <div class="grid w-72 max-h-[350px] gap-3 overflow-y-auto p-4">
                    <p class="text-base font-semibold dark:text-white">
                        @lang('admin::app.components.datagrid.toolbar.column-settings.title')
                    </p>

                    <div
                        class="flex items-center gap-2.5"
                        v-for="column in columns"
                        :key="column.index"
                    >
                        <input
                            type="checkbox"
                            :id="'column-setting-' + column.index"
                            :checked="column.visibility"
                            class="peer hidden"
                            @change="toggleColumn(column)"
                        />

                        <label
                            :for="'column-setting-' + column.index"
                            class="icon-checkbox-outline peer-checked:icon-checkbox-select cursor-pointer rounded-md text-2xl text-gray-500 peer-checked:text-brandColor"
                        >
                        </label>

                        <label
                            :for="'column-setting-' + column.index"
                            class="cursor-pointer text-sm text-gray-700 dark:text-gray-300"
                            v-html="column.label"
                        >
                        </label>
                    </div>
                </div>

                {!! view_render_event('admin.components.datagrid.column_settings.dropdown.content.after') !!}
            </x-slot>
        </x-admin::dropdown>

        {!! view_render_event('admin.components.datagrid.column_settings.dropdown.after') !!}
    </script>

    <script type="module">
        app.component('v-datagrid-column-settings', {
            template: '#v-datagrid-column-settings-template',

            props: ['columns'],

            emits: ['update-column-visibility'],

            methods: {
                /**
                 * Emits the toggled visibility for the given column.
                 *
                 * @param {object} column
                 * @returns {void}
                 */
                toggleColumn(column) {
                    this.$emit('update-column-visibility', column.index, ! column.visibility);
                },
            },
        });
    </script>
@endPushOnce
