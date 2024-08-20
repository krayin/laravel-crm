

<x-admin::layouts>
    <x-slot:title>
        @lang('admin::app.mail.index.' . request('route'))
    </x-slot>

    <div class="flex items-center justify-between rounded-lg border border-gray-200 bg-white px-4 py-2 text-sm dark:border-gray-800 dark:bg-gray-900 dark:text-gray-300">
        <div class="flex flex-col gap-2">
            <div class="flex cursor-pointer items-center">
                <!-- breadcrumbs -->
                <x-admin::breadcrumbs
                    name="mail.route"
                    :entity="request('route')"
                />
            </div>

            <div class="text-xl font-bold dark:text-gray-300">
                <!-- title -->
                @lang('admin::app.mail.index.' . request('route'))
            </div>
        </div>

        <div class="flex items-center gap-x-2.5">
            <!-- Create button for person -->
            <div class="flex items-center gap-x-2.5">
                <button
                    type="button"
                    class="primary-button"
                    @click="$refs.composeMail.toggleModal()"
                >
                    @lang('admin::app.mail.index.compose-mail-btn')
                </button>
            </div>
        </div>
    </div>

    <!-- Compose Mail Vue Component -->
    <v-mail ref="composeMail">
        <!-- Datagrid Shimmer -->
        <x-admin::shimmer.datagrid />
    </v-mail>

    @pushOnce('scripts')
        <script
            type="text/x-template"
            id="v-mail-template"
        >
            {!! view_render_event('krayin.admin.mail.'.request('route').'.datagrid.before') !!}

            <!-- DataGrid -->
           <!-- DataGrid -->
           <x-admin::datagrid
                ref="datagrid"
                src="{{ route('admin.mail.index', request('route')) }}"
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
                            class="row grid items-center gap-2.5 border-b px-4 py-4 text-gray-600 transition-all hover:bg-gray-50 dark:border-gray-800 dark:text-gray-300 dark:hover:bg-gray-950"
                            :style="`grid-template-columns: repeat(${gridsCount}, minmax(0, 1fr))`"
                        >
                            <!-- Group ID -->
                            <p>@{{ record.id }}</p>

                            <!-- Attachments -->
                            <p :class="record.attachments ? 'icon-attachmetent' : ''">
                                @{{ record.attachments ?? 'N/A'}}
                            </p>

                            <!-- Name -->
                            <p>@{{ record.name }}</p>
        
                            <!-- Subject -->
                            <p v-html="record.subject"></p>

                            <!-- Created At -->
                            <p v-html="record.created_at"></p>
        
                            <!-- Actions -->
                            <div class="flex justify-end">
                                <a @click="selectedMail=true; editModal(record.actions.find(action => action.index === 'edit'))">
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
                    </template>
                </template>
            </x-admin::datagrid>

            {!! view_render_event('krayin.admin.mail.'.request('route').'.datagrid.after') !!}

            <x-admin::form
                v-slot="{ meta, errors, handleSubmit }"
                enctype="multipart/form-data"
                as="div"
            >
                <form
                    @submit="handleSubmit($event, save)"
                    ref="mailForm"
                >
                    <x-admin::modal
                        ref="toggleComposeModal"
                        position="bottom-right"
                    >
                        <x-slot:header>
                            <h3 class="text-lg font-bold text-gray-800 dark:text-white">
                                @lang('admin::app.mail.index.mail.title')
                            </h3>
                        </x-slot>

                        <x-slot:content>
                            <x-admin::form.control-group.control
                                type="hidden"
                                name="id"
                                id="id"
                                v-model="draft.id"
                            />

                            <!-- To -->
                            <x-admin::form.control-group>
                                <x-admin::form.control-group.label class="required">
                                    @lang('admin::app.mail.index.mail.to')
                                </x-admin::form.control-group.label>

                                <div class="relative">
                                    <x-admin::form.control-group.controls.tags
                                        name="reply_to"
                                        rules="required"
                                        input-rules="email"
                                        ::data="draft.reply_to"
                                        :label="trans('admin::app.mail.index.mail.to')"
                                        :placeholder="trans('admin::app.mail.index.mail.enter-emails')"
                                    />
                                    

                                    <div class="absolute right-2 top-[9px] flex items-center gap-2">
                                        <span
                                            class="cursor-pointer font-medium hover:underline dark:text-white"
                                            @click="showCC = ! showCC"
                                        >
                                            @lang('admin::app.mail.index.mail.cc')
                                        </span>

                                        <span
                                            class="cursor-pointer font-medium hover:underline dark:text-white"
                                            @click="showBCC = ! showBCC"
                                        >
                                            @lang('admin::app.mail.index.mail.bcc')
                                        </span>
                                    </div>
                                </div>

                                <x-admin::form.control-group.error control-name="reply_to" />
                            </x-admin::form.control-group>

                            <template v-if="showCC">
                                <!-- Cc -->
                                <x-admin::form.control-group>
                                    <x-admin::form.control-group.label>
                                        @lang('admin::app.mail.index.mail.cc')
                                    </x-admin::form.control-group.label>

                                    <x-admin::form.control-group.controls.tags
                                        name="cc"
                                        input-rules="email"
                                        ::data="draft.cc"
                                        :label="trans('admin::app.mail.index.mail.cc')"
                                        :placeholder="trans('admin::app.mail.index.mail.enter-emails')"
                                    />

                                    <x-admin::form.control-group.error control-name="cc" />
                                </x-admin::form.control-group>
                            </template>

                            <template v-if="showBCC">
                                <!-- Cc -->
                                <x-admin::form.control-group>
                                    <x-admin::form.control-group.label>
                                        @lang('admin::app.mail.index.mail.bcc')
                                    </x-admin::form.control-group.label>

                                    <x-admin::form.control-group.controls.tags
                                        name="bcc"
                                        input-rules="email"
                                        ::data="draft.bcc"
                                        :label="trans('admin::app.mail.index.mail.bcc')"
                                        :placeholder="trans('admin::app.mail.index.mail.enter-emails')"
                                    />

                                    <x-admin::form.control-group.error control-name="bcc" />
                                </x-admin::form.control-group>
                            </template>

                            <!-- Subject -->
                            <x-admin::form.control-group>
                                <x-admin::form.control-group.label class="required">
                                    @lang('admin::app.mail.index.mail.subject')
                                </x-admin::form.control-group.label>

                                <x-admin::form.control-group.control
                                    type="text"
                                    id="subject"
                                    name="subject"
                                    rules="required"
                                    v-model="draft.subject"
                                    :label="trans('admin::app.mail.index.mail.subject')"
                                    :placeholder="trans('admin::app.mail.index.mail.subject')"
                                />

                                <x-admin::form.control-group.error control-name="subject" />
                            </x-admin::form.control-group>

                            <!-- Content -->
                            <x-admin::form.control-group>
                                <x-admin::form.control-group.control
                                    type="textarea"
                                    name="reply"
                                    id="reply"
                                    rules="required"
                                    rows="8"
                                    ::value="draft.reply"
                                    :label="trans('admin::app.mail.index.mail.message')"
                                />

                                <x-admin::form.control-group.error control-name="reply" />
                            </x-admin::form.control-group>

                            <!-- Attachments -->
                            <x-admin::form.control-group class="!mb-0">
                                <x-admin::attachments
                                    allow-multiple="true"
                                    hide-button="true"
                                />
                            </x-admin::form.control-group>
                        </x-slot>

                        <x-slot:footer>
                            <div class="flex w-full items-center justify-between">
                                <label
                                    class="icon-attachment cursor-pointer rounded-md p-1 text-2xl transition-all hover:bg-gray-200 dark:hover:bg-gray-800"
                                    for="file-upload"
                                ></label>

                                <div class="flex items-center gap-4">
                                    <x-admin::button
                                        type="submit"
                                        ref="submitBtn"
                                        class="transparent-button hover:bg-gray-200 dark:text-white dark:hover:bg-gray-800"
                                        :title="trans('admin::app.mail.index.mail.draft')"
                                        ::loading="isStoring.draft"
                                        ::disabled="isStoring.draft"
                                        @click="saveAsDraft = 1"
                                    />

                                    <x-admin::button
                                        class="primary-button"
                                        type="submit"
                                        ref="submitBtn"
                                        :title="trans('admin::app.mail.index.mail.send-btn')"
                                        ::loading="isStoring.sent"
                                        ::disabled="isStoring.sent"
                                        @click="saveAsDraft = 0"
                                    />
                                </div>
                            </div>
                        </x-slot>
                    </x-admin::modal>
                </form>
            </x-admin::form>
        </script>

        <script type="module">
            app.component('v-mail', {
                template: '#v-mail-template',

                data() {
                    return {
                        selectedMail: false,

                        showCC: false,

                        showBCC: false,

                        isStoring: {
                            draft: false,

                            sent: false,
                        },

                        saveAsDraft: 0,

                        draft: {
                            id: null,
                            reply_to: [],
                            cc: [],
                            bcc: [],
                            subject: '',
                            reply: '',
                            attachments: [],
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
                },

                methods: {
                    toggleModal() {
                        this.$refs.toggleComposeModal.toggle();
                    },

                    save(params, { resetForm, setErrors  }) {
                        this.isStoring[this.saveAsDraft ? 'draft' : 'sent'] = true;

                        let formData = new FormData(this.$refs.mailForm);

                        formData.append('is_draft', this.saveAsDraft);

                        if (this.draft.id) {
                            formData.append('_method', 'PUT');
                        }

                        this.$axios.post(this.draft.id ? "{{ route('admin.mail.update', ':id') }}".replace(':id', this.draft.id) : '{{ route('admin.mail.store') }}', formData, {
                                headers: {
                                    'Content-Type': 'multipart/form-data',
                                },
                            })
                            .then ((response) => {
                                this.$refs.datagrid.get();

                                this.$emitter.emit('add-flash', { type: 'success', message: response.data?.message });

                                resetForm();
                            })
                            .catch ((error) => {
                                if (error?.response?.status == 422) {
                                    setErrors(error.response.data.errors);
                                } else {
                                    this.$emitter.emit('add-flash', { type: 'error', message: error.response.data.message });
                                }
                            }).finally(() => {
                                this.$refs.toggleComposeModal.close();

                                this.isStoring[this.saveAsDraft ? 'draft' : 'sent'] = false;
                            });
                    },

                    editModal(row) {
                        if(row.title == 'View') {
                            window.location.href = row.url;

                            return;
                        }

                        this.$axios.get(row.url)
                            .then(response => {
                                this.draft = response.data.data;

                                this.$refs.toggleComposeModal.toggle();

                                this.showCC = this.draft.cc.length > 0;

                                this.showBCC = this.draft.bcc.length > 0;
                                
                            })
                            .catch(error => {});
                    },
                },
            });
        </script>
    @endPushOnce
</x-admin::layouts>
