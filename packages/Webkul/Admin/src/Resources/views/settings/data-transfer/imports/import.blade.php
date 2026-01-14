<x-admin::layouts>
    <x-slot:title>
        @lang('admin::app.settings.data-transfer.imports.import.title')
    </x-slot>

    <!-- Page Header -->
    <div class="flex items-center justify-between rounded-lg border border-gray-200 bg-white px-4 py-2 text-sm dark:border-gray-800 dark:bg-gray-900 dark:text-gray-300">
        <div class="flex flex-col gap-2">
            {!! view_render_event('admin.settings.data_transfers.import.breadcrumbs.before') !!}

            <!-- Breadcrumbs -->
            <x-admin::breadcrumbs 
                name="settings.data_transfers.import"
                :entity="$import"
            />

            {!! view_render_event('admin.settings.data_transfers.import.breadcrumbs.after') !!}

            <div class="text-xl font-bold dark:text-white">
                @lang('admin::app.settings.data-transfer.imports.import.title')
            </div>
        </div>

        <div class="flex items-center gap-x-2.5">
            <!-- Create button for person -->
            <div class="flex items-center gap-x-2.5">
                {!! view_render_event('admin.settings.data_transfers.import.edit_button.before') !!}

                <!-- Edit Button -->
                @if (bouncer()->hasPermission('settings.automation.data_transfer.imports.edit'))
                    <a
                        href="{{ route('admin.settings.data_transfer.imports.edit', $import->id) }}"
                        class="primary-button"
                    >
                        @lang('admin::app.settings.data-transfer.imports.import.edit-btn')
                    </a>
                @endif

                {!! view_render_event('admin.settings.data_transfers.import.edit_button.after') !!}
            </div>
        </div>
    </div>

    <!-- Import Vue Compontent -->
    <v-import />

    @pushOnce('scripts')
        <script
            type="text/x-template"
            id="v-import-template"
        >
            <!-- Body Content -->
            <div class="box-shadow mt-3.5 grid gap-2.5 p-5 max-xl:flex-wrap">
                <!-- Validate CSV File -->
                <div
                    class="flex w-full place-content-between items-center rounded-sm border border-orange-200 bg-orange-50 p-3 dark:border-gray-800 dark:bg-gray-900 dark:text-white"
                    v-if="importResource.state == 'pending'"
                >
                    <p class="flex items-center gap-2">
                        <i class="icon-info rounded-full bg-orange-200 text-2xl text-orange-600 dark:!text-orange-600"></i>

                        @lang('admin::app.settings.data-transfer.imports.import.validate-info')
                    </p>

                    <button
                        class="primary-button place-self-start"
                        @click="validate"
                    >
                        @lang('admin::app.settings.data-transfer.imports.import.validate')
                    </button>
                </div>

                <!-- Validation In Process -->
                <div
                    class="flex w-full place-content-between items-center rounded-sm border border-blue-200 bg-blue-50 p-3 dark:border-gray-800 dark:bg-gray-900 dark:text-white"
                    v-if="importResource.state == 'validating'"
                >
                    <p class="flex items-center gap-2">
                        <i class="icon-info rounded-full bg-blue-200 text-2xl text-blue-600 dark:!text-blue-600"></i>

                        @lang('admin::app.settings.data-transfer.imports.import.validating-info')

                        <!-- Spinner -->
                        <x-admin::spinner />
                    </p>
                </div>

                <!-- Validation Results -->
                <div
                    class="flex w-full place-content-between rounded-sm border p-3"
                    :class="isValid ? 'border-green-200 bg-green-50 dark:bg-gray-900 dark:border-gray-800' : 'border-red-200 bg-red-50 dark:bg-gray-900 dark:border-gray-800'"
                    v-else-if="importResource.state == 'validated'"
                >
                    <!-- Import Stats -->
                    <div class="grid gap-2">
                        <p
                            class="mb-2 flex items-center gap-2 dark:text-white"
                            v-if="isValid"
                        >
                            <i class="icon-success h-fit rounded-full bg-green-200 text-2xl text-green-600 dark:!text-green-600"></i>

                            @lang('admin::app.settings.data-transfer.imports.import.validation-success-info')
                        </p>

                        <p
                            class="flex items-center gap-2 dark:text-white"
                            v-else
                        >
                            <i class="icon-error h-fit rounded-full bg-red-200 text-2xl text-red-600 dark:!text-red-600"></i>

                            @lang('admin::app.settings.data-transfer.imports.import.validation-failed-info')
                        </p>

                        <p class="flex items-center gap-2 dark:text-white">
                            <i
                                class="icon-info rounded-full text-2xl"
                                :class="isValid ? 'bg-green-200 text-green-600 dark:!text-green-600' : 'bg-red-200 text-red-600 dark:!text-red-600'"
                            ></i>

                            <span class="font-medium text-gray-800 dark:text-white">
                                @lang('admin::app.settings.data-transfer.imports.import.total-rows-processed')
                            </span>

                            @{{ importResource.processed_rows_count }}
                        </p>

                        <p class="flex items-center gap-2 dark:text-white">
                            <i
                                class="icon-info rounded-full text-2xl"
                                :class="isValid ? 'bg-green-200 text-green-600 dark:!text-green-600' : 'bg-red-200 text-red-600 dark:!text-red-600'"
                            ></i>

                            <span class="font-medium text-gray-800 dark:text-white">
                                @lang('admin::app.settings.data-transfer.imports.import.total-invalid-rows')
                            </span>

                            @{{ importResource.invalid_rows_count }}
                        </p>

                        <p class="flex items-center gap-2 dark:text-white">
                            <i
                                class="icon-info rounded-full text-2xl"
                                :class="isValid ? 'bg-green-200 text-green-600 dark:!text-green-600' : 'bg-red-200 text-red-600 dark:!text-red-600'"
                            ></i>

                            <span class="font-medium text-gray-800 dark:text-white">
                                @lang('admin::app.settings.data-transfer.imports.import.total-errors')
                            </span>

                            @{{ importResource.errors_count }}
                        </p>

                        <div
                            class="flex place-items-start items-center gap-2 dark:text-white"
                            v-if="importResource.errors.length"
                        >
                            <i class="icon-info rounded-full bg-red-200 text-2xl text-red-600 dark:!text-red-600"></i>

                            <div class="grid gap-2">
                                <p
                                    class="break-all"
                                    v-for="error in importResource.errors"
                                >
                                    @{{ error }}
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Actions -->
                    <div class="flex gap-2">
                        <button
                            class="primary-button place-self-start"
                            v-if="isValid"
                            @click="start"
                        >
                            @lang('admin::app.settings.data-transfer.imports.import.title')
                        </button>

                        <a
                            class="primary-button place-self-start"
                            href="{{ route('admin.settings.data_transfer.imports.download_error_report', $import->id) }}"
                            target="_blank"
                            v-if="importResource.errors_count"
                        >
                            @lang('admin::app.settings.data-transfer.imports.import.download-error-report')
                        </a>
                    </div>
                </div>

                <!-- Import In Process -->
                <div
                    class="grid w-full gap-2 rounded-sm border border-green-200 bg-green-50 p-3 dark:border-gray-800 dark:bg-gray-900 dark:text-white"
                    v-else-if="importResource.state == 'processing'"
                >
                    <p class="flex items-center gap-2">
                        <i class="icon-info rounded-full bg-green-200 text-2xl text-green-600 dark:!text-green-600"></i>

                        @lang('admin::app.settings.data-transfer.imports.import.importing-info')
                    </p>

                    <div class="h-5 w-full rounded-sm bg-green-200 dark:bg-green-700">
                        <div
                            class="h-5 rounded-sm bg-green-600"
                            :style="{ 'width': stats.progress + '%' }"
                        ></div>
                    </div>

                    <p class="flex items-center gap-2">
                        <span class="font-medium text-gray-800 dark:text-white">
                            @lang('admin::app.settings.data-transfer.imports.import.progress')
                        </span>

                        @{{ stats.progress }}%
                    </p>

                    <p class="flex items-center gap-2">
                        <span class="font-medium text-gray-800 dark:text-white">
                            @lang('admin::app.settings.data-transfer.imports.import.total-batches')
                        </span>

                        @{{ stats.batches.total }}
                    </p>

                    <p class="flex items-center gap-2">
                        <span class="font-medium text-gray-800 dark:text-white">
                            @lang('admin::app.settings.data-transfer.imports.import.completed-batches')
                        </span>

                        @{{ stats.batches.completed }}
                    </p>

                    <p class="flex items-center gap-2">
                        <span class="font-medium text-gray-800 dark:text-white">
                            @lang('admin::app.settings.data-transfer.imports.import.total-created')
                        </span>

                        @{{ stats.summary.created }}
                    </p>

                    <p class="flex items-center gap-2">
                        <span class="font-medium text-gray-800 dark:text-white">
                            @lang('admin::app.settings.data-transfer.imports.import.total-updated')
                        </span>

                        @{{ stats.summary.updated }}
                    </p>

                    <p class="flex items-center gap-2">
                        <span class="font-medium text-gray-800 dark:text-white">
                            @lang('admin::app.settings.data-transfer.imports.import.total-deleted')
                        </span>

                        @{{ stats.summary.deleted }}
                    </p>
                </div>

                <!-- Linking In Process -->
                <div
                    class="grid w-full gap-2 rounded-sm border border-green-200 bg-green-50 p-3 dark:border-gray-800 dark:bg-gray-900"
                    v-else-if="importResource.state == 'linking'"
                >
                    <p class="flex items-center gap-2">
                        <i class="icon-info rounded-full bg-green-200 text-2xl text-green-600 dark:!text-green-600"></i>

                        @lang('admin::app.settings.data-transfer.imports.import.linking-info')
                    </p>

                    <div class="h-5 w-full rounded-sm bg-green-200 dark:bg-green-700">
                        <div
                            class="h-5 rounded-sm bg-green-600"
                            :style="{ 'width': stats.progress + '%' }"
                        ></div>
                    </div>

                    <p class="flex items-center gap-2">
                        <span class="font-medium text-gray-800">
                            @lang('admin::app.settings.data-transfer.imports.import.progress')
                        </span>

                        @{{ stats.progress }}%
                    </p>

                    <p class="flex items-center gap-2">
                        <span class="font-medium text-gray-800">
                            @lang('admin::app.settings.data-transfer.imports.import.total-batches')
                        </span>

                        @{{ stats.batches.total }}
                    </p>

                    <p class="flex items-center gap-2">
                        <span class="font-medium text-gray-800">
                            @lang('admin::app.settings.data-transfer.imports.import.completed-batches')
                        </span>

                        @{{ stats.batches.completed }}
                    </p>
                </div>

                <!-- Indexing In Process -->
                <div
                    class="grid w-full gap-2 rounded-sm border border-green-200 bg-green-50 p-3"
                    v-else-if="importResource.state == 'indexing'"
                >

                    <p class="flex items-center gap-2">
                        <i class="icon-info rounded-full bg-green-200 text-2xl text-green-600 dark:!text-green-600"></i>

                        @lang('admin::app.settings.data-transfer.imports.import.indexing-info')
                    </p>

                    <div class="h-5 w-full rounded-sm bg-green-200 dark:bg-green-700">
                        <div
                            class="h-5 rounded-sm bg-green-600"
                            :style="{ 'width': stats.progress + '%' }"
                        ></div>
                    </div>

                    <p class="flex items-center gap-2">
                        <span class="font-medium text-gray-800">
                            @lang('admin::app.settings.data-transfer.imports.import.progress')
                        </span>

                        @{{ stats.progress }}%
                    </p>

                    <p class="flex items-center gap-2">
                        <span class="font-medium text-gray-800">
                            @lang('admin::app.settings.data-transfer.imports.import.total-batches')
                        </span>

                        @{{ stats.batches.total }}
                    </p>

                    <p class="flex items-center gap-2">
                        <span class="font-medium text-gray-800">
                            @lang('admin::app.settings.data-transfer.imports.import.completed-batches')
                        </span>

                        @{{ stats.batches.completed }}
                    </p>
                </div>

                <!-- Import Completed -->
                <div
                    class="flex w-full place-content-between rounded-sm border border-green-200 bg-green-50 p-3 dark:border-gray-800 dark:bg-gray-900 dark:text-white"
                    v-else-if="importResource.state == 'completed'"
                >
                    <!-- Stats -->
                    <div class="grid gap-2">
                        <p
                            class="mb-2 flex items-center gap-2 text-base dark:text-white"
                            v-if="isValid"
                        >
                            <i class="icon-success h-fit rounded-full bg-green-200 text-2xl text-green-600 dark:!text-green-600"></i>

                            @lang('admin::app.settings.data-transfer.imports.import.imported-info')
                        </p>

                        <p class="flex items-center gap-2">
                            <i class="icon-info rounded-full bg-green-200 text-2xl text-green-600 dark:!text-green-600"></i>

                            <span class="font-medium text-gray-800 dark:text-white">
                                @lang('admin::app.settings.data-transfer.imports.import.total-created')
                            </span>

                            @{{ importResource.summary.created }}
                        </p>

                        <p class="flex items-center gap-2">
                            <i class="icon-info rounded-full bg-green-200 text-2xl text-green-600 dark:!text-green-600"></i>

                            <span class="font-medium text-gray-800 dark:text-white">
                                @lang('admin::app.settings.data-transfer.imports.import.total-updated')
                            </span>

                            @{{ importResource.summary.updated }}
                        </p>

                        <p class="flex items-center gap-2">
                            <i class="icon-info rounded-full bg-green-200 text-2xl text-green-600 dark:!text-green-600"></i>

                            <span class="font-medium text-gray-800 dark:text-white">
                                @lang('admin::app.settings.data-transfer.imports.import.total-deleted')
                            </span>

                            @{{ importResource.summary.deleted }}
                        </p>
                    </div>
                </div>
            </div>
        </script>

        <script type="module">
            app.component('v-import', {
                template: '#v-import-template',

                data() {
                    return {
                        importResource: @json($import),

                        isValid: "{{ $isValid }}",

                        stats: @json($stats),
                    };
                },

                mounted() {
                    if (this.importResource.process_in_queue) {
                        if (
                            this.importResource.state == 'processing'
                            || this.importResource.state == 'linking'
                            || this.importResource.state == 'indexing'
                        ) {
                            this.getStats();
                        }
                    } else {
                        if (this.importResource.state == 'processing') {
                            this.start();
                        }

                        if (this.importResource.state == 'linking') {
                            this.link();
                        }

                        if (this.importResource.state == 'indexing') {
                            this.index();
                        }
                    }
                },

                methods: {
                    validate() {
                        this.importResource.state = 'validating';

                        this.$axios.get("{{ route('admin.settings.data_transfer.imports.validate', $import->id) }}")
                            .then((response) => {
                                this.importResource = response.data.import;

                                this.isValid = response.data.is_valid;
                            })
                            .catch(error => {
                                this.$emitter.emit('add-flash', { type: 'error', message: error.response.data.message });
                            });
                    },

                    start() {
                        this.importResource.state = 'processing';

                        this.$axios.get("{{ route('admin.settings.data_transfer.imports.start', $import->id) }}")
                            .then((response) => {
                                this.importResource = response.data.import;

                                this.stats = response.data.stats;

                                if (this.importResource.process_in_queue) {
                                    this.getStats();
                                } else {
                                    if (this.importResource.state == 'processing') {
                                        this.start();
                                    } else if (this.importResource.state == 'linking') {
                                        this.link();
                                    } else if (this.importResource.state == 'indexing') {
                                        this.index();
                                    }
                                }
                            })
                            .catch(error => {
                                this.importResource.state = 'validated';

                                this.$emitter.emit('add-flash', { type: 'error', message: error.response.data.message });
                            });
                    },

                    link() {
                        this.$axios.get("{{ route('admin.settings.data_transfer.imports.link', $import->id) }}")
                            .then((response) => {
                                this.importResource = response.data.import;

                                this.stats = response.data.stats;

                                if (this.importResource.state == 'linking') {
                                    this.link();
                                } else if (this.importResource.state == 'indexing') {
                                    this.index();
                                }
                            })
                            .catch(error => {
                                this.$emitter.emit('add-flash', { type: 'error', message: error.response.data.message });
                            });
                    },

                    index() {
                        this.$axios.get("{{ route('admin.settings.data_transfer.imports.index_data', $import->id) }}")
                            .then((response) => {
                                this.importResource = response.data.import;

                                this.stats = response.data.stats;

                                if (this.importResource.state == 'indexing') {
                                    this.index();
                                }
                            })
                            .catch(error => {
                                this.$emitter.emit('add-flash', { type: 'error', message: error.response.data.message });
                            });
                    },

                    getStats() {
                        let state = 'processed';

                        if (this.importResource.state == 'linking') {
                            state = 'linked';
                        } else if (this.importResource.state == 'indexing') {
                            state = 'indexed';
                        }

                        this.$axios.get("{{ route('admin.settings.data_transfer.imports.stats', $import->id) }}/" + state)
                            .then((response) => {
                                this.importResource = response.data.import;

                                this.stats = response.data.stats;

                                if (this.importResource.state != 'completed') {
                                    setTimeout(() => {
                                        this.getStats();
                                    }, 1000);
                                }
                            })
                            .catch(error => {
                                this.$emitter.emit('add-flash', { type: 'error', message: error.response.data.message });
                            });
                    }
                }
            })
        </script>
    @endPushOnce
</x-admin::layouts>
