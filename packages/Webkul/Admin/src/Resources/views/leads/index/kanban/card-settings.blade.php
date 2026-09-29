{!! view_render_event('admin.leads.index.kanban.card_settings.before') !!}

<v-kanban-card-settings
    :card-fields="cardFields"
    @update-card-fields="updateCardFields"
>
</v-kanban-card-settings>

{!! view_render_event('admin.leads.index.kanban.card_settings.after') !!}

@pushOnce('scripts')
    <script
        type="text/x-template"
        id="v-kanban-card-settings-template"
    >
        {!! view_render_event('admin.leads.index.kanban.card_settings.dropdown.before') !!}

        <x-admin::dropdown ::close-on-click="false">
            <!-- Dropdown Toggler -->
            <x-slot:toggle>
                {!! view_render_event('admin.leads.index.kanban.card_settings.dropdown.toggle_button.before') !!}

                <div class="icon-setting cursor-pointer rounded-md border p-2 text-lg text-gray-600 transition-all hover:border-gray-400 dark:border-gray-800 dark:text-gray-300 dark:hover:border-gray-400"></div>

                {!! view_render_event('admin.leads.index.kanban.card_settings.dropdown.toggle_button.after') !!}
            </x-slot>

            <!-- Dropdown Content -->
            <x-slot:content>
                {!! view_render_event('admin.leads.index.kanban.card_settings.dropdown.content.before') !!}

                <div class="grid w-72 max-h-[350px] gap-3 overflow-y-auto p-4">
                    <p class="text-base font-semibold dark:text-white">
                        @lang('admin::app.leads.index.kanban.toolbar.card-settings.title')
                    </p>

                    <div
                        class="flex items-center gap-2.5"
                        v-for="field in fieldOptions"
                    >
                        <input
                            type="checkbox"
                            :id="'card-field-' + field.key"
                            :checked="fields[field.key]"
                            class="peer hidden"
                            @change="toggleField(field.key)"
                        />

                        <label
                            :for="'card-field-' + field.key"
                            class="icon-checkbox-outline peer-checked:icon-checkbox-select cursor-pointer rounded-md text-2xl text-gray-500 peer-checked:text-brandColor"
                        >
                        </label>

                        <label
                            :for="'card-field-' + field.key"
                            class="cursor-pointer text-sm text-gray-700 dark:text-gray-300"
                            v-text="field.label"
                        >
                        </label>
                    </div>
                </div>

                {!! view_render_event('admin.leads.index.kanban.card_settings.dropdown.content.after') !!}
            </x-slot>
        </x-admin::dropdown>

        {!! view_render_event('admin.leads.index.kanban.card_settings.dropdown.after') !!}
    </script>

    <script type="module">
        app.component('v-kanban-card-settings', {
            template: '#v-kanban-card-settings-template',

            props: ['cardFields'],

            emits: ['update-card-fields'],

            data() {
                return {
                    fields: { ...this.cardFields },

                    fieldOptions: [
                        { key: 'contactPerson', label: '@lang('admin::app.leads.index.kanban.toolbar.card-settings.contact-person')' },
                        { key: 'rottenDays', label: '@lang('admin::app.leads.index.kanban.toolbar.card-settings.rotten-indicator')' },
                        { key: 'assignedUser', label: '@lang('admin::app.leads.index.kanban.toolbar.card-settings.sales-person')' },
                        { key: 'leadValue', label: '@lang('admin::app.leads.index.kanban.toolbar.card-settings.estimated-lead-value')' },
                        { key: 'source', label: '@lang('admin::app.leads.index.kanban.toolbar.card-settings.source')' },
                        { key: 'type', label: '@lang('admin::app.leads.index.kanban.toolbar.card-settings.lead-type')' },
                        { key: 'tags', label: '@lang('admin::app.leads.index.kanban.toolbar.card-settings.tags')' },
                    ],
                };
            },

            watch: {
                cardFields: {
                    deep: true,

                    handler(newFields) {
                        this.fields = { ...newFields };
                    },
                },
            },

            methods: {
                /**
                 * Toggles the visibility of the specified card field and emits the updated fields.
                 *
                 * @param {string} key
                 * @returns {void}
                 */
                toggleField(key) {
                    this.fields[key] = ! this.fields[key];

                    this.$emit('update-card-fields', { ...this.fields });
                },
            },
        });
    </script>
@endPushOnce
