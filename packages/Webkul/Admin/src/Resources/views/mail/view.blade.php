<x-admin::layouts>
    <div class="flex flex-col gap-4">
        <div class="flex items-center justify-between rounded-lg border border-gray-200 bg-white px-4 py-2 text-sm dark:border-gray-800 dark:bg-gray-900 dark:text-gray-300">
            <div class="flex flex-col gap-2">
                <div class="flex cursor-pointer items-center">
                    <!-- Bredcrumbs -->
                    <x-admin::breadcrumbs name="mail" />
                </div>
    
                <div class="text-xl font-bold dark:text-gray-300">
                    @lang('Mails')
                </div>
            </div>
    
            <div class="flex items-center gap-x-2.5">
                <!-- Create button for person -->
                <div class="flex items-center gap-x-2.5">
                    <button
                        type="button"
                        class="primary-button"
                    >
                        @lang('Link Mail')
                    </button>
                </div>
            </div>
        </div>
    
        <v-email-list></v-email-list>
    </div>

    @pushOnce('scripts')
        <script
            type="text/x-template"
            id="v-email-list-template"
        >  
            <v-email-item
                :email="email"
                :key="0"
                :index="0"
                :action="action"
                @on-discard="action = {}"
                @onEmailAction="emailAction($event)"
            ></v-email-item>

            <v-email-item
                v-for='(email, index) in email.emails'
                :email="email"
                :key="index + 1"
                :index="index + 1"
                :action="action"
                @on-discard="action = {}"
                @onEmailAction="emailAction($event)"
            ></v-email-item>
        </script>

        <script
            type="text/x-template"
            id="v-email-item-template"
        >
            <div class="flex gap-2.5 box-shadow rounded bg-white p-4 dark:bg-gray-900 max-xl:flex-wrap">
                <div class="flex flex-col gap-4 w-full">
                    <div class="flex gap-4 w-full items-center justify-between">
                        <div class="flex gap-4">
                            <!-- Mailer Sort name -->
                            <div class="flex h-9 w-9 items-center justify-center rounded-full bg-green-200 text-xs font-medium">
                                SK
                            </div>
                    
                            <!-- Mailer receivers -->
                            <div class="flex flex-col gap-1">
                                <!-- Mailer Name -->
                                <span>@{{ email.name }}</span>
                                
                                <div class="flex flex-col gap-1">
                                    <div class="flex">
                                        <!-- Mail To -->
                                        <span>@lang('To') @{{ email.reply_to.join(', ') }}</span>

                                        <!-- Show More Button -->
                                        <i
                                            v-if="email?.cc?.length || email?.bcc?.length"
                                            class="text-2xl cursor-pointer"
                                            :class="email.showMore ? 'icon-up-arrow' : 'icon-down-arrow'"
                                            @click="email.showMore = ! email.showMore"
                                        ></i>
                                    </div>

                                    <!-- Show more emails -->
                                    <div
                                        class="flex flex-col"
                                        v-if="email.showMore"
                                    >
                                        <span>@lang('Cc:') @{{ email.cc.join(', ') }}</span>
                                        <span>@lang('Bcc:') @{{ email.bcc.join(', ') }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Time and Actions -->
                        <div class="flex gap-2 items-center justify-center">
                            <div>
                                2 hours ago
                            </div>

                            <div class="flex select-none items-center">
                                <button class="icon-more flex h-7 w-7 cursor-pointer items-center justify-center rounded-md text-2xl transition-all hover:bg-gray-200"></button>
                            </div>
                        </div>
                    </div>

                    <!-- Mail Body -->
                    <div v-html="email.reply"></div>

                    <hr class="h-1">

                    <!-- Reply, Reply All and Forward email -->
                    <template v-if="!action[email.id]">
                        <div class="flex gap-4">
                            <label
                                class="flex gap-2 items-center text-brandColor cursor-pointer"
                                @click="emailAction({type: 'reply'})"
                            >
                                @lang('Reply')

                                <i class="icon-reply text-2xl"></i>
                            </label>

                            <label
                                class="flex gap-2 items-center text-brandColor cursor-pointer"
                                @click="emailAction({type: 'replyAll'})"
                            >
                                @lang('Reply All')

                                <i class="icon-reply-all text-2xl"></i>
                            </label>

                            <label
                                class="flex gap-2 items-center text-brandColor cursor-pointer"
                                @click="emailAction({type: 'forward'})"
                            >
                                @lang('Forward')

                                <i class="icon-forward text-2xl"></i>
                            </label>
                        </div>
                    </template>

                    <template v-else>
                        <v-email-form
                            :action="action"
                            @on-discard="$emit('onDiscard')"
                        ></v-email-form>
                    </template>
                </div>
            </div>
        </script>

        <script
            type="text/x-template"
            id="v-email-form-template"
        >
            <div class="flex gap-2 w-full">
                <div class="flex h-9 w-9 items-center justify-center rounded-full bg-green-200 text-xs font-medium">
                    SK
                </div>
                
                <div class="gap-2 w-[926px] border rounded p-4">
                    <x-admin::form
                        v-slot="{ meta, errors, handleSubmit }"
                        enctype="multipart/form-data"
                        as="div"
                    >
                        <form
                            @submit="handleSubmit($event, save)"
                            ref="mailActionForm"
                        >
                            <div class="flex flex-col gap-2">
                                <div>
                                    <!-- Activity Type -->
                                    <x-admin::form.control-group.control
                                        type="hidden"
                                        name="parent_id"
                                        value="{{ request('id') }}"
                                    />

                                    <!-- To -->
                                    <x-admin::form.control-group>
                                        <x-admin::form.control-group.label class="required">
                                            @lang('admin::app.components.activities.actions.mail.to')
                                        </x-admin::form.control-group.label>

                                        <div class="relative">
                                            <x-admin::form.control-group.control
                                                type="tags"
                                                name="reply_to"
                                                rules="required"
                                                input-rules="email"
                                                ::data=""
                                                :label="trans('admin::app.components.activities.actions.mail.to')"
                                                :placeholder="trans('admin::app.components.activities.actions.mail.enter-emails')"
                                            />

                                            <div class="absolute right-2 top-[9px] flex items-center gap-2">
                                                <span
                                                    class="cursor-pointer font-medium hover:underline"
                                                    @click="showCC = ! showCC"
                                                >
                                                    @lang('admin::app.components.activities.actions.mail.cc')
                                                </span>

                                                <span
                                                    class="cursor-pointer font-medium hover:underline"
                                                    @click="showBCC = ! showBCC"
                                                >
                                                    @lang('admin::app.components.activities.actions.mail.bcc')
                                                </span>
                                            </div>
                                        </div>

                                        <x-admin::form.control-group.error control-name="reply_to" />
                                    </x-admin::form.control-group>

                                    <template v-if="showCC">
                                        <!-- Cc -->
                                        <x-admin::form.control-group>
                                            <x-admin::form.control-group.label>
                                                @lang('admin::app.components.activities.actions.mail.cc')
                                            </x-admin::form.control-group.label>

                                            <x-admin::form.control-group.control
                                                type="tags"
                                                name="cc"
                                                input-rules="email"
                                                :label="trans('admin::app.components.activities.actions.mail.cc')"
                                                :placeholder="trans('admin::app.components.activities.actions.mail.enter-emails')"
                                            />

                                            <x-admin::form.control-group.error control-name="cc" />
                                        </x-admin::form.control-group>
                                    </template>

                                    <template v-if="showBCC">
                                        <!-- Cc -->
                                        <x-admin::form.control-group>
                                            <x-admin::form.control-group.label>
                                                @lang('admin::app.components.activities.actions.mail.bcc')
                                            </x-admin::form.control-group.label>

                                            <x-admin::form.control-group.control
                                                type="tags"
                                                name="bcc"
                                                input-rules="email"
                                                :label="trans('admin::app.components.activities.actions.mail.bcc')"
                                                :placeholder="trans('admin::app.components.activities.actions.mail.enter-emails')"
                                            />

                                            <x-admin::form.control-group.error control-name="bcc" />
                                        </x-admin::form.control-group>
                                    </template>

                                    <!-- Content -->
                                    <x-admin::form.control-group>
                                        <x-admin::form.control-group.control
                                            type="textarea"
                                            name="reply"
                                            id="reply"
                                            rules="required"
                                            tinymce="true"
                                            :label="trans('admin::app.components.activities.actions.mail.message')"
                                        />

                                        <x-admin::form.control-group.error control-name="reply" />
                                    </x-admin::form.control-group>

                                    <!-- Attachments -->
                                    <x-admin::form.control-group>
                                        <x-admin::attachments
                                            allow-multiple="true"
                                            hide-button="true"
                                        />
                                    </x-admin::form.control-group>

                                    <!-- Divider -->
                                    <hr class="h-1">
                                </div>
                            
                                <!-- Action and Attachement -->
                                <div class="flex w-full items-center justify-between">
                                    <label
                                        class="flex cursor-pointer items-center gap-1"
                                        for="file-upload"
                                    >
                                        <i class="icon-attachmetent text-xl font-medium"></i>
                        
                                        @lang('Add Attachments')
                                    </label>
                                                            
                                    <div class="flex gap-2 items-center justify-center">
                                        <label
                                            class="flex cursor-pointer items-center gap-1"
                                            @click="$emit('onDiscard')"
                                        >
                                            @lang('Discard')
                                        </label>

                                        <x-admin::button
                                            class="primary-button"
                                            :title="trans('Send')"
                                            ::loading="isStoring"
                                            ::disabled="isStoring"
                                        />
                                    </div>
                                </div>
                            </div>
                        </form>
                    </x-admin::form>    
                </div>
            </div>
        </script>

        <script type="module">
            app.component('v-email-list', {
                template: '#v-email-list-template',

                data() {
                    return {
                        email: @json($email),

                        action: {},
                    };
                },
                
                methods: {
                    emailAction(action) {
                        this.action = action;

                        console.log(action);
                        

                        if (! this.action.email) {
                            this.action.email = this.lastEmail();
                        }
                    },

                    lastEmail() {
                        if (
                            this.email.emails === undefined 
                            || ! this.email.emails.length
                        ) {
                            return this.email;
                        }

                        return this.email.emails[this.email.emails.length - 1];
                    },
                },
            });
        </script>

        <script type="module">
            app.component('v-email-item', {
                template: '#v-email-item-template',

                props: ['index', 'email', 'action'],

                data() {
                    return {
                        hovering: '',
                    }
                },
    
                methods: {
                    emailAction(type) {
                        if (type != 'delete') {
                            this.$emit('onEmailAction', {'type': type, 'email': this.email});
                        } else {
                            if (! confirm('{{ __('admin::app.common.delete-confirm') }}')) {
                                return;
                            }
                            
                            this.$refs['form-' + this.email.id].submit()
                        }
                    },
                }
            });
        </script>

        <script type="module">
            app.component('v-email-form', {
                template: '#v-email-form-template',

                props: ['action'],

                data() {
                    return {
                        showCC: false,

                        showBCC: false,

                        isStoring: false,
                    }
                },

                mounted() {
                    
                },

                computed: {
                    reply_to() {
                        if (this.action.type == 'forward') {
                            return [];
                        }

                        return [this.action.email.from];
                    },

                    cc() {
                        if (this.action.type != 'reply-all') {
                            return [];
                        }

                        return this.action.email.cc;
                    },

                    bcc() {
                        if (this.action.type != 'reply-all') {
                            return [];
                        }

                        return this.action.email.bcc;
                    },

                    reply: function() {
                        if (this.action.type == 'forward') {
                            return this.action.email.reply;
                        }

                        return '';
                    }
                },

                methods: {
                    save(params, { resetForm, setErrors  }) {
                        let formData = new FormData(this.$refs.mailActionForm);

                        this.$axios.post("{{ route('admin.mail.store', 'replaceLeadId') }}", formData, {
                                headers: {
                                    'Content-Type': 'multipart/form-data'
                                }
                            })
                            .then ((response) => {
                                this.isStoring = false;

                                this.$emitter.emit('add-flash', { type: 'success', message: response.data.message });

                                this.$emitter.emit('on-activity-added', response.data.data);
                            })
                            .catch ((error) => {
                                this.isStoring = false;

                                console.log(error);
                                

                                if (error.response.status == 422) {
                                    setErrors(error.response.data.errors);
                                } else {
                                    this.$emitter.emit('add-flash', { type: 'error', message: error.response.data.message });

                                    this.$refs.mailActivityModal.close();
                                }
                            });
                    },
                },
            });
        </script>
    @endPushOnce
</x-admin::layouts>