<v-upload>
    <button
        type="button"
        class="secondary-button"
    >
        @lang('admin::app.leads.index.upload.upload-pdf')
    </button>
</v-upload>

@pushOnce('scripts')
    <script
        type="text/x-template"
        id="upload-template"
    >
        <div>
            <button
                type="button"
                class="secondary-button"
                @click="$refs.userUpdateAndCreateModal.open()"
            >
                @lang('admin::app.leads.index.upload.upload-pdf')
            </button>

            <x-admin::form
                v-slot="{ meta, values, errors, handleSubmit }"
                as="div"
                ref="modalForm"
            >
                <form 
                    @submit="handleSubmit($event, create)"
                    enctype="multipart/form-data"
                    ref="userForm"
                >
                    <x-admin::modal ref="userUpdateAndCreateModal">
                        <!-- Modal Header -->
                        <x-slot:header>
                            <p class="text-lg font-bold text-gray-800 dark:text-white">
                                @lang('admin::app.leads.index.upload.create-lead')
                            </p>
                        </x-slot>

                        <!-- Modal Content -->
                        <x-slot:content>
                            <x-admin::form.control-group>
                                <x-admin::form.control-group.label class="required">
                                    @lang('admin::app.leads.index.upload.file')
                                </x-admin::form.control-group.label>

                                <x-admin::form.control-group.control
                                    type="file"
                                    id="file"
                                    name="file"
                                    rules="required|mimes:pdf"
                                    :label="trans('admin::app.leads.index.upload.file')"
                                    ::disabled="isLoading"
                                />

                                <p class="mt-1 text-xs text-gray-600 dark:text-gray-300">@lang('admin::app.leads.index.upload.file-info')</p>

                                <x-admin::form.control-group.error control-name="file" />
                            </x-admin::form.control-group>

                            <!-- Sample Downloadable file -->
                            <a
                                href="{{ Storage::url('/lead-samples/sample.pdf') }}"
                                target="_blank"
                                id="source-sample"
                                class="cursor-pointer text-sm text-blue-600 transition-all hover:underline"
                                download
                            >
                                @lang('admin::app.leads.index.upload.sample-pdf')
                            </a>
                        </x-slot>

                        <!-- Modal Footer -->
                        <x-slot:footer>
                            <x-admin::button
                                button-type="submit"
                                class="primary-button justify-center"
                                :title="trans('admin::app.leads.index.upload.save-btn')"
                                ::loading="isLoading"
                                ::disabled="isLoading"
                            />
                        </x-slot>
                    </x-admin::modal>
                </form>
            </x-admin::form>
        </div>
    </script>

    <script type="module">
        app.component('v-upload', {
            template: '#upload-template',

            data() {
                return {
                    isLoading: false,
                };
            },

            methods: {
                create (params, {resetForm, setErrors}) {
                    this.isLoading = true;

                    const userForm = new FormData(this.$refs.userForm);

                    userForm.append('_method', 'post');

                    this.$axios.post("{{ route('admin.leads.create_by_ai') }}", userForm, {
                        headers: {
                            'Content-Type': 'multipart/form-data',
                        }
                    })
                    .then (response => {
                        this.isLoading = false;

                        this.$emitter.emit('add-flash', { type: 'success', message: response.data.message });

                        this.$refs.userUpdateAndCreateModal.close();

                        window.location.reload();
                    })
                    .catch (error => {
                        this.isLoading = false;

                        this.$emitter.emit('add-flash', { type: 'error', message: error.response.data.message });

                        this.$refs.userUpdateAndCreateModal.close();
                    });
                },
            },
        });
    </script>
@endPushOnce
