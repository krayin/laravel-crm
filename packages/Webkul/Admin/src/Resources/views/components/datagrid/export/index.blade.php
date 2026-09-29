<v-datagrid-export {{ $attributes }}>
    <div class="transparent-button hover:bg-gray-200 dark:text-white dark:hover:bg-gray-800">
        <span class="icon-export text-xl text-gray-600"></span>

        @lang('admin::app.export.export')
    </div>
</v-datagrid-export>

@pushOnce('scripts')
    <script
        type="text/x-template"
        id="v-datagrid-export-template"
    >
        <div>
            <x-admin::modal ref="exportModal">
                <x-slot:toggle>
                    <button class="transparent-button hover:bg-gray-200 dark:text-white dark:hover:bg-gray-800">
                        <span class="icon-export text-xl text-gray-600"></span>

                        @lang('admin::app.export.export')
                    </button>
                </x-slot>

                <x-slot:header>
                    <p class="text-lg font-bold text-gray-800 dark:text-white">
                        @lang('admin::app.export.download')
                    </p>
                </x-slot>

                <x-slot:content>
                    <template v-if="! exportingGoogleContacts">
                        <x-admin::form action="">
                            <x-admin::form.control-group class="!mb-0">
                                <x-admin::form.control-group.control
                                    type="select"
                                    name="format"
                                    v-model="format"
                                >
                                    <option value="csv">
                                        @lang('admin::app.export.csv')
                                    </option>

                                    <option value="xls">
                                        @lang('admin::app.export.xls')
                                    </option>

                                    <option value="xlsx">
                                        @lang('admin::app.export.xlsx')
                                    </option>

                                    <option
                                        value="google_contacts"
                                        v-if="googleContactsSrc"
                                    >
                                        @lang('admin::app.export.google-contacts')
                                    </option>
                                </x-admin::form.control-group.control>
                            </x-admin::form.control-group>
                        </x-admin::form>
                    </template>

                    <template v-else>
                        <div class="flex flex-col gap-2">
                            <div class="h-5 w-full overflow-hidden rounded-sm bg-green-200 dark:bg-green-700">
                                <div
                                    class="google-contacts-progress-bar h-5 rounded-sm bg-green-600"
                                    style="width: 33%"
                                ></div>
                            </div>

                            <p class="text-sm text-gray-600 dark:text-gray-300">
                                @lang('admin::app.export.google-contacts-in-progress')
                            </p>

                            <p class="flex items-center gap-2">
                                <span class="font-medium text-gray-800 dark:text-white">
                                    @lang('admin::app.export.google-contacts-total')
                                </span>

                                @{{ googleContactsStats?.total_count ?? 0 }}
                            </p>

                            <p class="flex items-center gap-2">
                                <span class="font-medium text-gray-800 dark:text-white">
                                    @lang('admin::app.export.google-contacts-exported')
                                </span>

                                @{{ googleContactsStats?.exported_count ?? 0 }}
                            </p>

                            <p class="flex items-center gap-2">
                                <span class="font-medium text-gray-800 dark:text-white">
                                    @lang('admin::app.export.google-contacts-duplicate')
                                </span>

                                @{{ googleContactsStats?.duplicate_count ?? 0 }}
                            </p>

                            <p class="flex items-center gap-2">
                                <span class="font-medium text-gray-800 dark:text-white">
                                    @lang('admin::app.export.google-contacts-failed')
                                </span>

                                @{{ googleContactsStats?.failed_count ?? 0 }}
                            </p>
                        </div>
                    </template>
                </x-slot>

                <x-slot:footer>
                    <button
                        v-if="! exportingGoogleContacts"
                        type="button"
                        class="primary-button"
                        @click="download"
                    >
                        @lang('admin::app.export.export')
                    </button>
                </x-slot>
            </x-admin::modal>
        </div>
    </script>

    <script type="module">
        app.component('v-datagrid-export', {
            template: '#v-datagrid-export-template',

            props: ['src', 'googleContactsSrc'],

            data() {
                return {
                    format: 'xls',

                    available: null,

                    applied: null,

                    exportingGoogleContacts: false,

                    googleContactsStats: null,
                };
            },

            mounted() {
                this.registerEvents();
            },

            methods: {
                /**
                 * Registers events to update properties and trigger the download process.
                 *
                 * @returns {void}
                 */
                registerEvents() {
                    this.$emitter.on('change-datagrid', this.updateProperties);
                },

                /**
                 * Updates the available and applied properties with new values.
                 *
                 * @param {object} data - Object containing available and applied properties.
                 * @returns {void}
                 */
                updateProperties({ available, applied }) {
                    this.available = available;

                    this.applied = applied;
                },

                /**
                 * Initiates the download process for exporting data.
                 *
                 * @returns {void}
                 */
                download() {
                    if (! this.available?.records?.length) {
                        this.$emitter.emit('add-flash', { type: 'warning', message: '@lang('admin::app.export.no-records')' });

                        this.$refs.exportModal.toggle();
                    } else {
                        let params = {
                            sort: {},

                            filters: {},
                        };

                        if (
                            this.applied.sort.column &&
                            this.applied.sort.order
                        ) {
                            params.sort = this.applied.sort;
                        }

                        this.applied.filters.columns.forEach(column => {
                            params.filters[column.index] = column.value;
                        });

                        if (this.format === 'google_contacts') {
                            this.exportToGoogleContacts(params);

                            return;
                        }

                        params.export = 1;

                        params.format = this.format;

                        this.$axios
                            .get(this.src, {
                                params,
                                responseType: 'blob',
                            })
                            .then((response) => {
                                const url = window.URL.createObjectURL(new Blob([response.data]));

                                /**
                                 * Link generation.
                                 */
                                const link = document.createElement('a');
                                link.href = url;
                                link.setAttribute('download', `${(Math.random() + 1).toString(36).substring(7)}.${this.format}`);

                                /**
                                 * Adding a link to a document, clicking on the link, and then removing the link.
                                 */
                                document.body.appendChild(link);
                                link.click();
                                document.body.removeChild(link);

                                this.$refs.exportModal.toggle();
                            });
                    }
                },

                /**
                 * Kick off an async export of every person matching the current
                 * filters/sort to the current user's connected Google account.
                 *
                 * @param {object} params
                 * @returns {void}
                 */
                exportToGoogleContacts(params) {
                    this.$axios.post(this.googleContactsSrc, params)
                        .then((response) => {
                            this.exportingGoogleContacts = true;

                            this.googleContactsStats = {
                                total_count: 0,
                                exported_count: 0,
                                duplicate_count: 0,
                                failed_count: 0,
                            };

                            this.pollGoogleContactsExportBatch(response.data.batch_id);
                        })
                        .catch((error) => {
                            this.$emitter.emit('add-flash', { type: 'error', message: error.response.data.message });
                        });
                },

                /**
                 * Poll an export batch until it finishes, updating the live progress
                 * bar, then show a result summary.
                 *
                 * @param {number} batchId
                 * @returns {void}
                 */
                pollGoogleContactsExportBatch(batchId) {
                    this.$axios.get(`${this.googleContactsSrc}/${batchId}/stats`)
                        .then((response) => {
                            const stats = response.data;

                            this.googleContactsStats = stats;

                            if (! stats.finished) {
                                setTimeout(() => this.pollGoogleContactsExportBatch(batchId), 2000);

                                return;
                            }

                            this.exportingGoogleContacts = false;

                            this.$emitter.emit('add-flash', {
                                type: stats.failed_count > 0 ? 'warning' : 'success',
                                message: "@lang('admin::app.export.google-contacts-summary')"
                                    .replace(':exported', stats.exported_count)
                                    .replace(':duplicate', stats.duplicate_count)
                                    .replace(':failed', stats.failed_count),
                            });
                        });
                },
            },
        });
    </script>
@endPushOnce

@pushOnce('styles')
    <style>
        @keyframes google-contacts-progress-slide {
            0% { transform: translateX(-100%); }
            100% { transform: translateX(400%); }
        }

        .google-contacts-progress-bar {
            animation: google-contacts-progress-slide 1.2s ease-in-out infinite;
        }
    </style>
@endPushOnce
