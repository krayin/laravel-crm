@php
    $html = ($email->lead_id && view()->exists($view = 'admin::components.attributes.view'))
        ? view($view, [
            'customAttributes' => app('Webkul\Attribute\Repositories\AttributeRepository')->findWhere([
                    'entity_type' => 'leads',
                ]),
                'entity' => $email->lead,
            ])->render() 
        : '';
@endphp

<x-admin::layouts>
    <div class="flex flex-col gap-4">
        <div class="flex items-center justify-between rounded-lg border border-gray-200 bg-white px-4 py-2 text-sm dark:border-gray-800 dark:bg-gray-900 dark:text-gray-300">
            <div class="flex flex-col gap-2">
                <div class="flex cursor-pointer items-center">
                    <!-- Bredcrumbs -->
                    <x-admin::breadcrumbs
                        name="mail.route.view"
                        :entity="$email"
                        :route="request('route')"
                    />
                </div>
    
                <div class="text-xl font-bold dark:text-gray-300">
                    @lang('Mails')
                </div>
            </div>
    
            <div class="flex items-center gap-x-2.5">
                <!-- Mail Linking -->
                <div class="flex items-center gap-x-2.5">
                    <!-- Link Mail -->
                    <v-action-email>
                        <button
                            type="button"
                            class="primary-button"
                        >
                            @lang('Link Mail')
                        </button>
                    </v-action-email>

                    <!-- Create Contact Modal -->
                    <v-create-contact></v-create-contact>
                </div>
            </div>
        </div>
    
        <v-email-list>
           <x-admin::shimmer.leads.view.mail :count="$email->count()"/>
        </v-email-list>
    </div>

    @pushOnce('scripts')
        <!-- Email List Template -->
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
                @on-email-action="emailAction($event)"
            ></v-email-item>

            <v-email-item
                v-for='(email, index) in email.emails'
                :email="email"
                :key="index + 1"
                :index="index + 1"
                :action="action"
                @on-discard="action = {}"
                @on-email-action="emailAction($event)"
            ></v-email-item>
        </script>

        <!-- Email Item Template -->
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
                                @{{ email.name.split(' ').map(word => word[0]).join('') }}
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
                                        <span v-if="email?.cc">
                                            @lang('Cc:')
                                            
                                            @{{ email.cc.join(', ') }}
                                        </span>

                                        <span v-if="email.bcc">
                                            @lang('Bcc:')

                                            @{{ email.bcc?.join(', ') }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Time and Actions -->
                        <div class="flex gap-2 items-center justify-center">
                            <div>
                                @{{ email.time_ago }}
                            </div>

                            <div class="flex select-none items-center">
                                <x-admin::dropdown position="bottom-right">
                                    <x-slot:toggle>
                                        <button class="icon-more flex h-7 w-7 cursor-pointer items-center justify-center rounded-md text-2xl transition-all hover:bg-gray-200"></button>
                                    </x-slot>
                        
                                    <!-- Admin Dropdown -->
                                    <x-slot:content class="!p-0">
                                        <div class="flex flex-col gap-2 pb-2.5">
                                            <label
                                                class="flex gap-2 cursor-pointer px-2 py-2 text-base text-gray-800 hover:bg-gray-100 dark:text-white dark:hover:bg-gray-950"
                                                @click="emailAction('reply')"
                                            >
                                                <i class="icon-reply text-2xl"></i>

                                                @lang('Reply')
                                            </label>

                                            <label
                                                class="flex gap-2 cursor-pointer px-2 py-2 text-base text-gray-800 hover:bg-gray-100 dark:text-white dark:hover:bg-gray-950"
                                                @click="emailAction('reply-all')"
                                            >
                                                <i class="icon-reply-all text-2xl"></i>

                                                @lang('Reply all')
                                            </label>

                                            <label
                                                class="flex gap-2 cursor-pointer px-2 py-2 text-base text-gray-800 hover:bg-gray-100 dark:text-white dark:hover:bg-gray-950"
                                                @click="emailAction('forward')"
                                            >
                                                <i class="icon-forward text-2xl"></i>

                                                @lang('Forward')
                                            </label>

                                            <label
                                                class="flex gap-2 cursor-pointer px-2 py-2 text-base text-gray-800 hover:bg-gray-100 dark:text-white dark:hover:bg-gray-950"
                                                @click="emailAction('delete')"
                                            >
                                                <i class="icon-delete text-2xl"></i>

                                                @lang('Delete')
                                            </label>
                                        </div>
                                    </x-slot>
                                </x-admin::dropdown>
                            </div>
                        </div>
                    </div>

                    <!-- Mail Body -->
                    <div v-html="email.reply"></div>

                    <a
                        v-for="attachment in email.attachments"
                        :href="'{{ route('admin.mail.attachment_download') }}/' + attachment.id"
                        class="flex items-center text-brandColor cursor-pointer"
                    >
                        <i class="icon-attachmetent text-2xl"></i>

                        @{{ attachment.name }}
                    </a>

                    <hr class="h-1">

                    <!-- Reply, Reply All and Forward email -->
                    <template v-if="!action[email.id]">
                        <div class="flex gap-4">
                            <label
                                class="flex gap-2 items-center text-brandColor cursor-pointer"
                                @click="emailAction('reply')"
                            >
                                @lang('Reply')

                                <i class="icon-reply text-2xl"></i>
                            </label>

                            <label
                                class="flex gap-2 items-center text-brandColor cursor-pointer"
                                @click="emailAction('reply-all')"
                            >
                                @lang('Reply All')

                                <i class="icon-reply-all text-2xl"></i>
                            </label>

                            <label
                                class="flex gap-2 items-center text-brandColor cursor-pointer"
                                @click="emailAction('forward')"
                            >
                                @lang('Forward')

                                <i class="icon-forward text-2xl"></i>
                            </label>
                        </div>
                    </template>

                    <template v-else>
                        <v-email-form
                            :action="action"
                            :email="email"
                            @on-discard="$emit('onDiscard')"
                        ></v-email-form>
                    </template>
                </div>
            </div>
        </script>

        <!-- Email Form Template -->
        <script
            type="text/x-template"
            id="v-email-form-template"
        >
            <div class="flex gap-2 w-full">
                <div class="flex h-9 w-9 items-center justify-center rounded-full bg-green-200 text-xs font-medium">
                    @{{ email.name.split(' ').map(word => word[0]).join('') }}
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
                                            <x-admin::form.control-group.controls.tags
                                                name="reply_to"
                                                rules="required"
                                                input-rules="email"
                                                ::data="reply_to"
                                                label="@lang('admin::app.components.activities.actions.mail.to')"
                                                placeholder="@lang('admin::app.components.activities.actions.mail.enter-emails')"
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
                                            ::value="reply"
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

        <!-- Contact Lookup Template -->
        <script
            type="text/x-template"
            id="v-contact-lookup-template"
        >
            <div>
                <template v-if="email?.person_id">
                    <div class="flex gap-2">
                        <div class="flex h-9 w-9 items-center justify-center rounded-full bg-green-200 text-xs font-medium">
                            @{{ email.person.name.split(' ').map(word => word[0]).join('') }}
                        </div>
                
                        <!-- Mailer receivers -->
                        <div class="flex flex-col gap-1">
                            <!-- Mailer Name -->
                            <span>@{{ email.person.name }}</span>

                            <!-- Mailer Additional Deatils -->
                            <div class="flex flex-col gap-1">
                                <div class="flex flex-col">
                                    <span class="text-sm">@{{ email.person.job_title }}</span>

                                    <span class="text-sm text-brandColor">@{{ email.person?.emails.map(item => item.value).join(', ') }}</span>

                                    <span class="text-sm text-brandColor">@{{ email.person?.contact_numbers.map(item => item.value).join(', ') }}</span>
                                </div>
                            </div>
                        </div>

                        <div>
                            <div class="flex gap-2">
                                <button
                                    type="button"
                                    class="icon-delete flex h-7 w-7 cursor-pointer items-center justify-center rounded-md text-2xl transition-all hover:bg-gray-200"
                                    @click="unlinkContact('person')"
                                ></button>

                                <a
                                    :href="'{{ route('admin.contacts.persons.edit', ':id') }}'.replace(':id', email.person_id)"
                                    class="icon-right-arrow flex h-7 w-7 cursor-pointer items-center justify-center rounded-md text-2xl transition-all hover:bg-gray-200"
                                    @click="unlinkContact(email)"
                                ></a>
                            </div>
                        </div>
                    </div>
                </template>

                <template v-else>
                    <div
                        class="relative"
                        ref="lookup"
                    >
                        <!-- Input Box (Button) -->
                        <div
                            class="relative inline-block w-full"
                            @click="toggle"
                        >
                            <!-- Input-like div -->
                            <div class="w-full bg-white border border-gray-300 rounded-md px-3 py-2 text-gray-700 cursor-pointer">
                                @{{ selectedItem.name ?? '@lang('Search an existing contact')'}}
                            </div>
                            
                            <!-- Arrow down icon -->
                            <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                                <i class="fas fa-chevron-down text-gray-400"></i>
                            </div>
                        </div>

                        <span class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-3">
                            <div class="flex items-center justify-center space-x-1">                        
                                <i 
                                    class="text-2xl"
                                    :class="showPopup ? 'icon-up-arrow': 'icon-down-arrow'"
                                ></i>
                            </div>
                        </span>

                        <!-- Popup Box -->
                        <div 
                            v-if="showPopup" 
                            class="flex flex-col gap-2 absolute top-full z-10 mt-1 w-full origin-top transform rounded-lg border bg-white p-2 shadow-lg transition-transform"
                        >
                            <!-- Search Bar -->
                            <div class="relative">
                                <!-- Input Box -->
                                <input
                                    type="text"
                                    v-model.lazy="searchTerm"
                                    v-debounce="500"
                                    class="w-full rounded border border-gray-200 px-2.5 py-2 text-sm font-normal text-gray-800 transition-all hover:border-gray-400 focus:border-gray-400 dark:border-gray-800 dark:bg-gray-900 dark:text-gray-300 dark:hover:border-gray-400 dark:focus:border-gray-400 pr-10" 
                                    placeholder="Search..."
                                    ref="searchInput"
                                    @keyup="search"
                                />
                            
                                <!-- Search Icon (absolute positioned) -->
                                <span class="absolute inset-y-0 right-0 flex items-center pr-3">
                                    <div class="flex items-center justify-center space-x-1">
                                        <!-- Loader (optional, based on condition) -->
                                        <div
                                            class="relative"
                                            v-if="isSearching"
                                        >
                                            <svg
                                                class="h-5 w-5 animate-spin"
                                                xmlns="http://www.w3.org/2000/svg"
                                                fill="none"
                                                aria-hidden="true"
                                                viewBox="0 0 24 24"
                                            >
                                                <circle
                                                    class="opacity-25"
                                                    cx="12"
                                                    cy="12"
                                                    r="10"
                                                    stroke="currentColor"
                                                    stroke-width="4"
                                                ></circle>
                                                <path
                                                    class="opacity-75"
                                                    fill="currentColor"
                                                    d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"
                                                ></path>
                                            </svg>
                                        </div>
                            
                                        <!-- Search Icon -->
                                        <i class="fas fa-search text-gray-500"></i>
                                    </div>
                                </span>
                            </div>

                            <!-- Results List -->
                            <ul class="max-h-40 divide-y divide-gray-100 overflow-y-auto">
                                <li 
                                    v-for="person in persons" 
                                    :key="person.id"
                                    class="flex gap-2 p-2 cursor-pointer text-gray-800 transition-colors hover:bg-blue-100"
                                    @click="linkContact('person', person)"
                                >
                                    <div class="flex h-9 w-9 items-center justify-center rounded-full bg-green-200 text-xs font-medium">
                                        @{{ person.name.split(' ').map(word => word[0]).join('') }}
                                    </div>
                            
                                    <!-- Mailer receivers -->
                                    <div class="flex flex-col gap-1">
                                        <!-- Mailer Name -->
                                        <span>@{{ person.name }}</span>
                                        
                                        <div class="flex flex-col gap-1">
                                            <div class="flex">
                                                <span class="text-sm">@{{ person.emails.map(item => item.value).join(', ') }}</span>
                                            </div>
                                        </div>
                                    </div>
                                </li>                       
                            
                                <li v-if="persons.length === 0" class="px-4 py-2 text-center text-gray-500">
                                    @lang('No results found')
                                </li>
                            </ul>

                            <!-- Add New Contact Button -->
                            <div
                                class="flex items-center gap-2 p-2 border-t border-gray-200 cursor-pointer text-blue-600 transition-colors hover:bg-gray-100"
                                @click="toggleContactModal"
                            >
                                <span>+ Add Item Contact</span>
                            </div>
                        </div>
                    </div>
                </template>
            </div>
        </script>

        <!-- Create Contact Template -->
        <script
            type="text/x-template"
            id="v-create-contact-template"
        >
            <x-admin::form
                v-slot="{ meta, errors, handleSubmit }"
                as="div"
            >
                <form
                    @submit="handleSubmit($event, create)"
                    ref="modalForm"
                >
                    <!-- Add Contact Modal -->
                    <x-admin::modal ref="contactModal">
                        <x-slot:header>
                            <div class="flex items-center justify-between">
                                <p class="text-xl font-semibold text-gray-800 dark:text-white">
                                    @lang('Create Contact')
                                </p>
                            </div>
                        </x-slot>

                        <x-slot:content>
                            <x-admin::attributes
                                :custom-attributes="app('Webkul\Attribute\Repositories\AttributeRepository')->findWhere([
                                    'entity_type' => 'persons',
                                ])"
                            />
                        </x-slot>

                        <x-slot:footer>
                            <x-admin::button
                                class="primary-button"
                                :title="trans('Save Contact')"
                                ::loading="isStoring"
                                ::disabled="isStoring"
                            />
                        </x-slot>
                    </x-admin::modal>
                </form>
            </x-admin::form>
        </script>

        <script
            type="text/x-template"
            id="v-action-email-template"
        >
            <x-admin::drawer
                width="350px"
                ref="filterDrawer"
            >
                <x-slot:toggle>
                    <button
                        type="button"
                        class="primary-button"
                    >
                        @lang('Link Mail')
                    </button>
                </x-slot>

                <x-slot:header class="p-3.5">
                    <!-- Apply Filter Title -->
                    <div class="flex items-center justify-between">
                        <p class="text-xl font-semibold text-gray-800 dark:text-white">
                            @lang('Link Mail')
                        </p>
                    </div>
                </x-slot>
                
                <x-slot:content class="p-3.5">
                    <div class="flex flex-col gap-4">
                        <!-- Link to contact -->
                        <x-admin::form.control-group class="flex gap-2 !mb-0 items-center">
                            <label 
                                for="link-to-contact"
                                class="flex items-center space-x-2"
                            >
                                <input 
                                    type="radio" 
                                    name="link"
                                    id="link-to-contact" 
                                    value="contact" 
                                    v-model="link"
                                    class="form-radio h-5 w-5 text-blue-600 transition duration-150 ease-in-out"
                                />
                    
                                <span class="text-gray-700 cursor-pointer">Link To Contact</span>
                            </label>
                        </x-admin::form.control-group>
                    
                        <template v-if="link == 'contact'">
                            <v-contact-lookup
                                @link-contact="linkContact"
                                @unlink-contact="unlinkContact"
                                :email="email"
                            ></v-contact-lookup>
                        </template>

                        <!-- Link to contact -->
                        <x-admin::form.control-group class="flex gap-2 !mb-0 items-center">
                            <label 
                                for="link-to-lead"
                                class="flex items-center space-x-2"
                            >
                                <input 
                                    type="radio" 
                                    name="link"
                                    id="link-to-lead" 
                                    value="lead" 
                                    v-model="link"
                                    class="form-radio h-5 w-5 text-blue-600 transition duration-150 ease-in-out"
                                />
                    
                                <span class="text-gray-700 cursor-pointer">Link To Lead</span>
                            </label>
                        </x-admin::form.control-group>
                    
                        <!-- Contact Lookup -->
                        <template v-if="link == 'lead'">
                            <v-contact-lookup></v-contact-lookup>
                        </template>
                    </div>
                </x-slot>
            </x-admin::drawer>
        </script>

        <!-- Email List Vue Component -->
        <script type="module">
            app.component('v-email-list', {
                template: '#v-email-list-template',

                data() {
                    return {
                        email: @json($email),

                        action: {},
                    };
                },
                
                mounted() {
                    this.$emitter.on('on-email-save', (email) => {
                        this.email.emails.push(email);

                        this.action = {};

                        setTimeout(() => this.scrollBottom(), 0);
                    });
                },

                methods: {
                    emailAction(action) {
                        this.action[action.email.id] = action;

                        if (! this.action.email) {
                            this.action.email = this.lastEmail();
                        }
                    },

                    scrollBottom() {
                        const scrollTop = window.pageYOffset || document.documentElement.scrollTop;

                        const windowHeight = window.innerHeight;

                        const scrollBottom = scrollTop + windowHeight;

                        window.scrollTo({
                            top: scrollBottom,
                            behavior: 'smooth',
                        });
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

        <!-- Email Item Vue Component -->
        <script type="module">
            app.component('v-email-item', {
                template: '#v-email-item-template',

                props: ['index', 'email', 'action'],

                data() {
                    return {
                        hovering: '',
                    };
                },
    
                methods: {
                    emailAction(type) {
                        if (type != 'delete') {
                            this.$emit('onEmailAction', {type, email: this.email});
                        } else {
                            if (! confirm('{{ __('admin::app.common.delete-confirm') }}')) {
                            
                                return;
                            }

                            this.$axios.post(`{{ route('admin.mail.delete', ':id') }}`.replace(':id', this.email.id), {
                                _method: 'DELETE',
                                type: 'trash'
                            })
                            .then ((response) => {
                                this.$emitter.emit('add-flash', { type: 'success', message: response.data.message });

                                this.$emit('onDiscard');
                            })
                        }
                    },
                },
            });
        </script>

        <!-- Email Form Vue Component -->
        <script type="module">
            app.component('v-email-form', {
                template: '#v-email-form-template',

                props: ['action', 'email'],

                data() {
                    return {
                        showCC: false,

                        showBCC: false,

                        isStoring: false,
                    };
                },

                computed: {
                    reply_to() {
                        if (this.getActionType == 'forward') {
                            return [];
                        }

                        if (this.getActionType == 'reply-all') {
                            console.log(this.action.email);
                            
                            console.log([
                                this.action.email.from,
                                ...(this.action.email?.cc || []),
                                ...(this.action.email?.bcc || []),
                            ]);
                            
                            return [
                                this.action.email.from,
                                ...(this.action.email?.cc || []),
                                ...(this.action.email?.bcc || []),
                            ];
                        }

                        return [this.action.email.from];
                    },

                    cc() {
                        if (this.getActionType != 'reply-all') {
                            return [];
                        }

                        return this.action.email.cc;
                    },

                    bcc() {
                        if (this.getActionType != 'reply-all') {
                            return [];
                        }

                        return this.action.email.bcc;
                    },

                    reply() {
                        if (this.getActionType == 'forward') {
                            return this.action.email.reply;
                        }

                        return '';
                    },

                    getActionType() {
                        return this.action[this.email.id].type;
                    },
                },

                methods: {
                    save(params, { resetForm, setErrors  }) {
                        let formData = new FormData(this.$refs.mailActionForm);

                        this.isStoring = true;

                        this.$axios.post("{{ route('admin.mail.store') }}", formData, {
                                headers: {
                                    'Content-Type': 'multipart/form-data'
                                }
                            })
                            .then ((response) => {
                                this.isStoring = false;

                                this.$emitter.emit('on-email-save', response.data.data);
                                
                                this.$emitter.emit('add-flash', { type: 'success', message: response.data.message });
                            })
                            .catch ((error) => {
                                this.isStoring = false;

                                if (error.response.status == 422) {
                                    setErrors(error.response.data.errors);
                                } else {
                                    this.$emitter.emit('add-flash', { type: 'error', message: error.response.data.message });
                                }
                            });
                    },
                },
            });
        </script>

        <!-- Contact Lookup Component -->
        <script type="module">
            app.component('v-contact-lookup', {
                template: '#v-contact-lookup-template',

                props: ['email'],

                emits: ['link-contact', 'unlink-contact'],

                data() {
                    return {
                        showPopup: false,

                        searchTerm: '',

                        selectedItem: {},

                        searchedResults: [],

                        isSearching: false,

                        cancelToken: null,
                    };
                },

                mounted() {
                    if (this.value) {
                        this.selectedItem = this.value;
                    }
                },

                created() {
                    window.addEventListener('click', this.handleFocusOut);
                },

                beforeDestroy() {
                    window.removeEventListener('click', this.handleFocusOut);
                },

                watch: {
                    searchTerm(newVal, oldVal) {
                        this.search();
                    },
                },

                computed: {
                    /**
                     * Filter the searchedResults based on the search query.
                     * 
                     * @return {Array}
                     */
                    persons() {
                        return this.searchedResults.filter(item => 
                            item.name.toLowerCase().includes(this.searchTerm.toLowerCase())
                        );
                    }
                },
                
                methods: {
                    /**
                     * Toggle the popup.
                     * 
                     * @return {void}
                     */
                    toggle() {
                        this.showPopup = ! this.showPopup;

                        if (this.showPopup) {
                            this.$nextTick(() => this.$refs.searchInput.focus());
                        }
                    },

                    /**
                     * Select an item from the list.
                     * 
                     * @param {Object} item
                     * 
                     * @return {void}
                     */
                    linkContact(type, person) {
                        this.showPopup = false;

                        this.searchTerm = '';

                        this.selectedItem = person;

                        this.$emit('link-contact', {
                            type,
                            person,
                        });
                    },

                    unlinkContact() {
                        this.selectedItem = {};

                        this.$emit('unlink-contact');
                    },

                    /**
                     * Initialize the items.
                     * 
                     * @return {void}
                     */
                    search() {
                        if (this.searchTerm.length <= 2) {
                            this.searchedResults = [];

                            this.isSearching = false;

                            return;
                        }

                        this.isSearching = true;

                        if (this.cancelToken) {
                            this.cancelToken.cancel();
                        }

                        this.cancelToken = this.$axios.CancelToken.source();

                        this.$axios.get('{{ route('admin.contacts.persons.search') }}', {
                                params: { 
                                    ...this.params,
                                    query: this.searchTerm
                                },
                                cancelToken: this.cancelToken.token, 
                            })
                            .then(response => {
                                this.searchedResults = response.data.data;
                            })
                            .catch(error => {
                                if (! this.$axios.isCancel(error)) {
                                    console.error("Search request failed:", error);
                                }

                                this.isSearching = false;
                            })
                            .finally(() => this.isSearching = false);
                    },

                    /**
                     * Handle the focus out event.
                     * 
                     * @param {Event} event
                     * 
                     * @return {void}
                     */
                    handleFocusOut(event) {
                        const lookup = this.$refs.lookup;

                        if (
                            lookup && 
                            ! lookup.contains(event.target)
                        ) {
                            this.showPopup = false;
                        }
                    },

                    toggleContactModal() {
                        this.showPopup = false;

                        this.$emitter.emit('open-contact-modal');
                    },
                },
            });
        </script>

        <!-- Create Contact Modal Component -->
        <script type="module">
            app.component('v-create-contact', {
                template: '#v-create-contact-template',

                data() {
                    return {
                        isStoring: false,
                    };
                },

                mounted() {
                    this.$emitter.on('open-contact-modal', () => this.$refs.contactModal.toggle());
                },

                methods: {
                    create(params, { setErrors }) {
                        this.isStoring = true;

                        const formData = new FormData(this.$refs.modalForm);

                        this.$axios.post('{{ route('admin.contacts.persons.store') }}', formData)
                            .then(response => {
                                this.isStoring = false;

                                this.$emitter.emit('add-flash', { type: 'success', message: response.data.message });

                                this.$refs.contactModal.toggle();
                            })
                            .catch(error => {
                                this.isStoring = false;

                                if (error.response.status == 422) {
                                    setErrors(error.response.data.errors);
                                } else {
                                    this.$emitter.emit('add-flash', { type: 'error', message: error.response.data.message });
                                }
                            })
                    },
                },
            });
        </script>

        <!-- Link to mail Component -->
        <script type="module">
            app.component('v-action-email', {
                template: '#v-action-email-template',

                data() {
                    return {
                        link: 'contact',

                        email: @json($email->getAttributes()),

                        html: `{!! $html !!}`,
                    };
                },

                created() {
                    @if ($email->person)
                        this.email.person = @json($email->person);
                    @endif
                },

                methods: {
                    linkContact(contact) {
                        this.$axios.post('{{ route('admin.mail.update', $email->id) }}', {
                            _method: 'PUT',
                            person_id: contact.person.id,
                        })
                            .then (response => {                                
                                this.email['person'] = contact.person;

                                this.email['person_id'] = contact.person.id;

                                this.$emitter.emit('add-flash', { type: 'success', message: response.data.message });
                            })
                            .catch (error => {});
                    },

                    unlinkContact(contact) {
                        this.$axios.post('{{ route('admin.mail.update', $email->id) }}', {
                            _method: 'PUT',
                            person_id: null,
                        })
                            .then (response => {
                                this.email['person'] = this.email['person_id'] = null;

                                this.$emitter.emit('add-flash', { type: 'success', message: response.data.message });
                            })
                            .catch (error => {})
                    },

                }
            });
        </script>
    @endPushOnce
</x-admin::layouts>
