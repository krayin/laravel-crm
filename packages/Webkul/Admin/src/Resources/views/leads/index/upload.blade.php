<v-upload>
    <button
        type="button"
        class="secondary-button"
    >
        @lang('admin::app.leads.index.upload.upload-file')
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
                @lang('admin::app.leads.index.upload.upload-file')
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
                                    id="files"
                                    name="files"
                                    rules="required|mimes:pdf,bmp,jpeg,jpg,png,webp"
                                    :label="trans('admin::app.leads.index.upload.file')"
                                    ::disabled="isLoading"
                                    ref="file"
                                    accept="application/pdf,image/*"
                                    multiple
                                />

                                <p class="mt-1 text-xs text-gray-600 dark:text-gray-300">
                                    @lang('admin::app.leads.index.upload.file-info')
                                </p>

                                <x-admin::form.control-group.error control-name="files" />
                            </x-admin::form.control-group>
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
                create(params, { resetForm, setErrors }) {
                    const selectedFiles = this.$refs.file?.files;

                    if (selectedFiles.length === 0) {
                        this.$emitter.emit('add-flash', { type: 'error', message: "@lang('admin::app.leads.index.upload.file-required')" });

                        return;
                    }

                    this.isLoading = true;

                    const formData = new FormData();

                    selectedFiles.forEach((file, index) => {
                        formData.append(`files[${index}]`, file);
                    });

                    formData.append('_method', 'post');

                    this.sendRequest(formData);
                },

                sendRequest(formData) {
                    this.$axios.post("{{ route('admin.leads.create_by_ai') }}", formData, {
                        headers: {
                            'Content-Type': 'multipart/form-data',
                        }
                    })
                    .then(response => {
                        this.$emitter.emit('add-flash', { type: 'success', message: response.data.message });

                        this.$parent.$refs.leadsKanban.boot()
                    })
                    .catch(error => {
                        this.$emitter.emit('add-flash', { type: 'error', message: error.response.data.message });
                    })
                    .finally(() => {
                        this.isLoading = false;

                        this.$refs.userUpdateAndCreateModal.close();
                    });
                },
            },
        });
    </script>
@endPushOnce
