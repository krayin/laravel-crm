@props([
    'entity'            => null,
    'entityControlName' => null,
])

<!-- File Button -->
<div class="">
    <button
        class="flex h-[74px] w-[84px] flex-col items-center justify-center gap-1 rounded-lg border border-transparent bg-cyan-200 font-medium text-cyan-900 transition-all hover:border-cyan-400"
        @click="$refs.fileActionComponent.openModal('mail')"
    >
        <span class="icon-file text-2xl dark:!text-cyan-900"></span>

        @lang('admin::app.components.activities.actions.file.btn')
    </button>

    <!-- File Action Vue Component -->
    <v-file-activity
        ref="fileActionComponent"
        :entity="{{ json_encode($entity) }}"
        entity-control-name="{{ $entityControlName }}"
    ></v-file-activity>
</div>

@pushOnce('scripts')
    <script type="text/x-template" id="v-file-activity-template">
        <Teleport to="body">
            <x-admin::form
                v-slot="{ meta, errors, handleSubmit }"
                as="div"
                ref="modalForm"
            >
                <form @submit="handleSubmit($event, save)">
                    <x-admin::modal ref="fileActivityModal" position="bottom-right">
                        <x-slot:header>
                            <h3 class="text-base font-semibold">
                                @lang('admin::app.components.activities.actions.file.title')
                            </h3>
                        </x-slot>

                        <x-slot:content>
                            <!-- Activity Type -->
                            <x-admin::form.control-group.control
                                type="hidden"
                                name="type"
                                value="file"
                            />
                            
                            <!-- Id -->
                            <x-admin::form.control-group.control
                                type="hidden"
                                ::name="entityControlName"
                                ::value="entity.id"
                            />

                            <!-- Title -->
                            <x-admin::form.control-group>
                                <x-admin::form.control-group.label>
                                    @lang('admin::app.components.activities.actions.file.title-control')
                                </x-admin::form.control-group.label>
                                
                                <x-admin::form.control-group.control
                                    type="text"
                                    name="title"
                                />
                            </x-admin::form.control-group>

                            <!-- Description -->
                            <x-admin::form.control-group>
                                <x-admin::form.control-group.label>
                                    @lang('admin::app.components.activities.actions.file.description')
                                </x-admin::form.control-group.label>
                                
                                <x-admin::form.control-group.control
                                    type="textarea"
                                    name="comment"
                                />
                            </x-admin::form.control-group>
                            
                            <!-- File Name -->
                            <x-admin::form.control-group>
                                <x-admin::form.control-group.label>
                                    @lang('admin::app.components.activities.actions.file.name')
                                </x-admin::form.control-group.label>

                                <x-admin::form.control-group.control
                                    type="text"
                                    name="name"
                                />
                            </x-admin::form.control-group>

                            <!-- File -->
                            <x-admin::form.control-group class="!mb-0">
                                <x-admin::form.control-group.label class="required">
                                    @lang('admin::app.components.activities.actions.file.file')
                                </x-admin::form.control-group.label>
                                
                                <x-admin::form.control-group.control
                                    type="file"
                                    id="file"
                                    name="file"
                                    rules="required"
                                    :label="trans('admin::app.components.activities.actions.file.file')"
                                />

                                <x-admin::form.control-group.error control-name="file" />
                            </x-admin::form.control-group>
                        </x-slot>

                        <x-slot:footer>
                            <x-admin::button
                                class="primary-button"
                                :title="trans('admin::app.components.activities.actions.file.save-btn')"
                                ::loading="isStoring"
                                ::disabled="isStoring"
                            />
                        </x-slot>
                    </x-admin::modal>
                </form>
            </x-admin::form>
        </Teleport>
    </script>

    <script type="module">
        app.component('v-file-activity', {
            template: '#v-file-activity-template',

            props: {
                entity: {
                    type: Object,
                    required: true,
                    default: () => {}
                },

                entityControlName: {
                    type: String,
                    required: true,
                    default: ''
                }
            },

            data: function () {
                return {
                    isStoring: false,
                }
            },

            methods: {
                openModal(type) {
                    this.$refs.fileActivityModal.open();
                },

                save(params, { setErrors }) {
                    this.isStoring = true;

                    this.$axios.post("{{ route('admin.activities.store') }}", params, {
                            headers: {
                                'Content-Type': 'multipart/form-data',
                            }
                        })
                        .then (response => {
                            this.isStoring = false;

                            this.$emitter.emit('add-flash', { type: 'success', message: response.data.message });

                            this.$emitter.emit('on-activity-added', response.data.data);

                            this.$refs.fileActivityModal.close();
                        })
                        .catch (error => {
                            this.isStoring = false;

                            if (error.response.status == 422) {
                                setErrors(error.response.data.errors);
                            } else {
                                this.$emitter.emit('add-flash', { type: 'error', message: error.response.data.message });

                                this.$refs.fileActivityModal.close();
                            }
                        });
                },
            },
        });
    </script>
@endPushOnce