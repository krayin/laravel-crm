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
                </x-slot>

                <x-slot:footer>
                    <button
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
                            this.$emitter.emit('add-flash', { type: 'success', message: response.data.message });

                            this.$refs.exportModal.toggle();

                            this.pollGoogleContactsExportBatch(response.data.batch_id);
                        })
                        .catch((error) => {
                            this.$emitter.emit('add-flash', { type: 'error', message: error.response.data.message });
                        });
                },

                /**
                 * Poll an export batch until it finishes, then show a result summary.
                 *
                 * @param {number} batchId
                 * @returns {void}
                 */
                pollGoogleContactsExportBatch(batchId) {
                    this.$axios.get(`${this.googleContactsSrc}/${batchId}/stats`)
                        .then((response) => {
                            const stats = response.data;

                            if (! stats.finished) {
                                setTimeout(() => this.pollGoogleContactsExportBatch(batchId), 2000);

                                return;
                            }

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
