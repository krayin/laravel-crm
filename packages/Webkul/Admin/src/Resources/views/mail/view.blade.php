<x-admin::layouts>
    <x-slot:title>
        @lang('admin::app.mail.view.subject', ['subject' => $email->subject])
    </x-slot>

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
    
                <div class="flex items-center gap-2">
                    <div class="text-xl font-bold dark:text-gray-300">
                        @lang('admin::app.mail.view.title') 
                    </div>
                    
                    <span class="label-active">{{ request('route') }}</span>
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
                            @lang('admin::app.mail.view.link-mail')
                        </button>
                    </v-action-email>
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
                                        <span>@lang('admin::app.mail.view.to') @{{ email.reply_to.join(', ') }}</span>

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
                                            @lang('admin::app.mail.view.cc'):
                                            
                                            @{{ email.cc.join(', ') }}
                                        </span>

                                        <span v-if="email.bcc">
                                            @lang('admin::app.mail.view.bcc'):

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

                                                @lang('admin::app.mail.view.reply')
                                            </label>

                                            <label
                                                class="flex gap-2 cursor-pointer px-2 py-2 text-base text-gray-800 hover:bg-gray-100 dark:text-white dark:hover:bg-gray-950"
                                                @click="emailAction('reply-all')"
                                            >
                                                <i class="icon-reply-all text-2xl"></i>

                                                @lang('admin::app.mail.view.reply-all')
                                            </label>

                                            <label
                                                class="flex gap-2 cursor-pointer px-2 py-2 text-base text-gray-800 hover:bg-gray-100 dark:text-white dark:hover:bg-gray-950"
                                                @click="emailAction('forward')"
                                            >
                                                <i class="icon-forward text-2xl"></i>

                                                @lang('admin::app.mail.view.forward')
                                            </label>

                                            <label
                                                class="flex gap-2 cursor-pointer px-2 py-2 text-base text-gray-800 hover:bg-gray-100 dark:text-white dark:hover:bg-gray-950"
                                                @click="emailAction('delete')"
                                            >
                                                <i class="icon-delete text-2xl"></i>

                                                @lang('admin::app.mail.view.delete')
                                            </label>
                                        </div>
                                    </x-slot>
                                </x-admin::dropdown>
                            </div>
                        </div>
                    </div>

                    <!-- Mail Body -->
                    <div v-html="email.reply"></div>

                    <div class="flex gap-2 flex-wrap">
                        <a
                            :href="'{{ route('admin.mail.attachment_download') }}/' + attachment.id"
                            class="flex cursor-pointer items-center gap-1 rounded-md"
                            target="_blank"
                            v-for="attachment in email.attachments"
                        >
                            <span class="icon-attached-file text-xl"></span>

                            <span class="font-medium text-brandColor">@{{ attachment.name }}</span>
                        </a>
                    </div>

                    <!-- Reply, Reply All and Forward email -->
                    <template v-if="!action[email.id]">
                        <div class="flex gap-6 font-medium py-4 border-t-2">
                            <label
                                class="flex gap-2 items-center text-brandColor cursor-pointer"
                                @click="emailAction('reply')"
                            >
                                @lang('admin::app.mail.view.reply')

                                <i class="icon-reply text-2xl"></i>
                            </label>

                            <label
                                class="flex gap-2 items-center text-brandColor cursor-pointer"
                                @click="emailAction('reply-all')"
                            >
                                @lang('admin::app.mail.view.reply-all')

                                <i class="icon-reply-all text-2xl"></i>
                            </label>

                            <label
                                class="flex gap-2 items-center text-brandColor cursor-pointer"
                                @click="emailAction('forward')"
                            >
                                @lang('admin::app.mail.view.forward')

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
                                            @lang('admin::app.mail.view.to')
                                        </x-admin::form.control-group.label>

                                        <div class="relative">
                                            <x-admin::form.control-group.controls.tags
                                                name="reply_to"
                                                rules="required"
                                                input-rules="email"
                                                ::data="reply_to"
                                                :label="trans('admin::app.mail.view.to')"
                                                :placeholder="trans('admin::app.mail.view.enter-mails')"
                                            />

                                            <div class="absolute right-2 top-[9px] flex items-center gap-2">
                                                <span
                                                    class="cursor-pointer font-medium hover:underline"
                                                    @click="showCC = ! showCC"
                                                >
                                                    @lang('admin::app.mail.view.cc')
                                                </span>

                                                <span
                                                    class="cursor-pointer font-medium hover:underline"
                                                    @click="showBCC = ! showBCC"
                                                >
                                                    @lang('admin::app.mail.view.bcc')
                                                </span>
                                            </div>
                                        </div>

                                        <x-admin::form.control-group.error control-name="reply_to" />
                                    </x-admin::form.control-group>

                                    <template v-if="showCC">
                                        <!-- Cc -->
                                        <x-admin::form.control-group>
                                            <x-admin::form.control-group.label>
                                                @lang('admin::app.mail.view.cc')
                                            </x-admin::form.control-group.label>

                                            <x-admin::form.control-group.control
                                                type="tags"
                                                name="cc"
                                                input-rules="email"
                                                :label="trans('admin::app.mail.view.cc')"
                                                :placeholder="trans('admin::app.mail.view.enter-mails')"
                                            />

                                            <x-admin::form.control-group.error control-name="cc" />
                                        </x-admin::form.control-group>
                                    </template>

                                    <template v-if="showBCC">
                                        <!-- Cc -->
                                        <x-admin::form.control-group>
                                            <x-admin::form.control-group.label>
                                                @lang('admin::app.mail.view.bcc')
                                            </x-admin::form.control-group.label>

                                            <x-admin::form.control-group.control
                                                type="tags"
                                                name="bcc"
                                                input-rules="email"
                                                :label="trans('admin::app.mail.view.bcc')"
                                                :placeholder="trans('admin::app.mail.view.enter-mails')"
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
                                            :tinymce="true"
                                            :label="trans('admin::app.mail.view.message')"
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
                                        <i class="icon-attachment text-xl font-medium"></i>
                        
                                        @lang('admin::app.mail.view.add-attachments')
                                    </label>
                                                            
                                    <div class="flex gap-2 items-center justify-center">
                                        <label
                                            class="flex cursor-pointer items-center gap-1"
                                            @click="$emit('onDiscard')"
                                        >
                                            @lang('admin::app.mail.view.discard')
                                        </label>

                                        <x-admin::button
                                            class="primary-button"
                                            :title="trans('admin::app.mail.view.send')"
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
                    <div class="flex justify-between">
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
                        </div>
                        
                        <div class="flex gap-2">
                            <template v-if="! unlinking.contact">
                                <button
                                    type="button"
                                    class="icon-delete flex h-7 w-7 cursor-pointer items-center justify-center rounded-md text-2xl transition-all hover:bg-gray-200"
                                    @click="unlinkContact"
                                ></button>
                            </template>

                            <template v-else>
                                <x-admin::spinner />
                            </template>

                            <a
                                :href="'{{ route('admin.contacts.persons.edit', ':id') }}'.replace(':id', email.person_id)"
                                target="_blank"
                                class="icon-right-arrow flex h-7 w-7 cursor-pointer items-center justify-center rounded-md text-2xl transition-all hover:bg-gray-200"
                            ></a>
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
                                            <x-admin::spinner />
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
                                    @click="linkContact(person)"
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
                                    @lang('admin::app.mail.view.no-result-found')
                                </li>
                            </ul>

                            <!-- Add New Contact Button -->
                            <div
                                class="flex items-center gap-2 p-2 border-t border-gray-200 cursor-pointer text-blue-600 transition-colors"
                                @click="toggleContactModal"
                            >
                                <span>+ @lang('admin::app.mail.view.add-new-contact')</span>
                            </div>
                        </div>
                    </div>
                </template>
            </div>
        </script>

        <script
            type="text/x-template"
            id="v-lead-lookup-template"
        >
            <div>
                <template v-if="email?.lead_id">
                    <div class="flex justify-between">
                        <div class="flex gap-2 w-full">
                            <div class="flex h-9 w-9 items-center justify-center rounded-full bg-green-200 text-xs font-medium">
                                @{{email.lead.title.split(' ').map(word => word[0]).join('') }}
                            </div>
                    
                            <div class="flex flex-col gap-1">
                                <!-- Lead Title -->
                                <span>@{{email.lead.title }}</span>

                                <!-- Lead Description and Lead Value -->
                                <div class="flex flex-col gap-2">
                                    <div class="flex flex-col">
                                        <div class="text-sm">
                                            <span class="font-semibold">@lang('admin::app.mail.view.description'):</span>
                                            
                                            @{{ email.lead.description }}
                                        </div>

                                        <div class="text-sm">
                                            <span class="font-semibold">@lang('Lead Value'):</span>
                                            
                                            @{{ $admin.formatPrice(email.lead.lead_value) }}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="flex gap-2">
                            <template v-if="! unlinking.lead">
                                <button
                                    type="button"
                                    class="icon-delete flex h-7 w-7 cursor-pointer items-center justify-center rounded-md text-2xl transition-all hover:bg-gray-200"
                                    @click="unlinkLead"
                                ></button>
                            </template>

                            <template v-else>
                                <x-admin::spinner />
                            </template>

                            <a
                                :href="'{{ route('admin.leads.view', ':id') }}'.replace(':id', email.lead_id)"
                                target="_blank"
                                class="icon-right-arrow flex h-7 w-7 cursor-pointer items-center justify-center rounded-md text-2xl transition-all hover:bg-gray-200"
                            ></a>
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
                                @{{ selectedItem.name ?? '@lang('Search an existing lead')'}}
                            </div>
                            
                            <!-- Arrow down icon -->
                            <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                                <i class="fas fa-chevron-down text-gray-400"></i>
                            </div>
                        </div>

                        <!-- toggle popup -->
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
                                    placeholder="@lang('admin::app.mail.view.search')"
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
                                            <x-admin::spinner />
                                        </div>
                            
                                        <!-- Search Icon -->
                                        <i class="fas fa-search text-gray-500"></i>
                                    </div>
                                </span>
                            </div>

                            <!-- Results List -->
                            <ul class="max-h-40 divide-y divide-gray-100 overflow-y-auto">
                                <li 
                                    v-for="lead in leads" 
                                    :key="lead.id"
                                    class="flex items-center gap-2 p-2 cursor-pointer text-gray-800 transition-colors hover:bg-blue-100"
                                    @click="linkLead(lead)"
                                >
                                    <div class="flex h-9 w-9 items-center justify-center rounded-full bg-green-200 text-xs font-medium">
                                        @{{ lead.title.split(' ').map(word => word[0]).join('') }}
                                    </div>
                            
                                    <!-- Lead Title -->
                                    <div class="flex flex-col gap-1">
                                        <span>@{{ lead.title }}</span>
                                    </div>
                                </li>                       
                            
                                <li v-if="leads.length === 0" class="px-4 py-2 text-center text-gray-500">
                                    @lang('admin::app.mail.view.no-result-found')
                                </li>
                            </ul>

                            <!-- Add New Lead Button -->
                            <div
                                class="flex items-center gap-2 p-2 border-t border-gray-200 cursor-pointer text-blue-600 transition-colors"
                                @click="toggleLeadModal"
                            >
                                <span>+ @lang('admin::app.mail.view.add-new-lead')</span>
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
                    ref="contactForm"
                >
                    <!-- Add Contact Modal -->
                    <x-admin::modal 
                        ref="contactModal"
                        @toggle="toggleModal"
                    >
                        <x-slot:header>
                            <div class="flex items-center justify-between">
                                <p class="text-xl font-semibold text-gray-800 dark:text-white">
                                    @lang('admin::app.mail.view.create-new-contact')
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
                                :title="trans('admin::app.mail.view.save-contact')"
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
            id="v-create-lead-template"
        >
            <x-admin::form
                v-slot="{ meta, errors, handleSubmit }"
                as="div"
            >
                <form
                    @submit="handleSubmit($event, create)"
                    ref="leadForm"
                >
                    <!-- Add Contact Modal -->
                    <x-admin::modal
                        ref="leadModal"
                        @toggle="toggleModal"
                        size="large"
                    >
                        <x-slot:header>
                            <div class="flex items-center justify-between">
                                <p class="text-xl font-semibold text-gray-800 dark:text-white">
                                    @lang('admin::app.mail.view.create-lead')
                                </p>
                            </div>
                        </x-slot>

                        <x-slot:content>
                            <div class="flex flex-col gap-2">
                                <!-- Tabs -->
                                <div class="flex gap-4 border-b border-gray-200">
                                    <div
                                        v-for="type in types"
                                        class="cursor-pointer px-3 py-2.5 text-sm font-medium"
                                        :class="{'border-brandColor border-b-2 !text-brandColor transition': selectedType == type.name }"
                                        @click="selectedType = type.name"
                                    >
                                        @{{ type.label }}
                                    </div>
                                </div>

                                <!-- Container -->
                                <div>
                                    <div v-show="selectedType == 'lead'">
                                        <div class="w-full">
                                            <!-- Lead Details Title and Description -->
                                            <x-admin::attributes
                                                :custom-attributes="app('Webkul\Attribute\Repositories\AttributeRepository')->findWhere([
                                                    ['code', 'NOTIN', ['lead_value', 'lead_type_id', 'lead_source_id', 'expected_close_date', 'user_id', 'lead_value']],
                                                    'entity_type' => 'leads',
                                                    'quick_add'   => 1
                                                ])"
                                            />

                                            <div class="flex gap-4 max-sm:flex-wrap">
                                                <div class="w-1/2 mb-4">
                                                    <x-admin::attributes
                                                        :custom-attributes="app('Webkul\Attribute\Repositories\AttributeRepository')->findWhere([
                                                            ['code', 'IN', ['lead_value']],
                                                            'entity_type' => 'leads',
                                                            'quick_add'   => 1
                                                        ])"
                                                    />
                                                </div>

                                                <div class="w-1/2 mb-4">
                                                    <x-admin::attributes
                                                        :custom-attributes="app('Webkul\Attribute\Repositories\AttributeRepository')->findWhere([
                                                            ['code', 'IN', ['lead_source_id']],
                                                            'entity_type' => 'leads',
                                                            'quick_add'   => 1
                                                        ])"
                                                    />
                                                </div>
                                            </div>

                                            <div class="flex gap-4 max-sm:flex-wrap">
                                                <div class="w-1/2 mb-4">
                                                    <x-admin::attributes
                                                        :custom-attributes="app('Webkul\Attribute\Repositories\AttributeRepository')->findWhere([
                                                            ['code', 'IN', ['lead_type_id']],
                                                            'entity_type' => 'leads',
                                                            'quick_add'   => 1
                                                        ])"
                                                    />
                                                </div>
                                                
                                                <div class="w-1/2 mb-4">
                                                    <x-admin::attributes
                                                        :custom-attributes="app('Webkul\Attribute\Repositories\AttributeRepository')->findWhere([
                                                            ['code', 'IN', ['user_id']],
                                                            'entity_type' => 'leads',
                                                            'quick_add'   => 1
                                                        ])"
                                                    />
                                                </div>
                                            </div>

                                            <div class="flex gap-4 max-sm:flex-wrap">
                                                <div class="w-1/2 mb-4">
                                                    <x-admin::attributes
                                                        :custom-attributes="app('Webkul\Attribute\Repositories\AttributeRepository')->findWhere([
                                                            ['code', 'IN', ['expected_close_date']],
                                                            'entity_type' => 'leads',
                                                            'quick_add'   => 1
                                                        ])"
                                                        :custom-validations="[
                                                            'expected_close_date' => [
                                                                'date_format:yyyy-MM-dd',
                                                                'after:' .  \Carbon\Carbon::yesterday()->format('Y-m-d')
                                                            ],
                                                        ]"
                                                    />
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                        
                                    <div v-show="selectedType == 'person'">
                                        @include('admin::leads.common.contact')
                                    </div>

                                    <div 
                                        class="overflow-y-auto"
                                        v-show="selectedType == 'product'"
                                    >
                                        @include('admin::leads.common.products')
                                    </div>
                                </div>
                            </div>
                        </x-slot>

                        <x-slot:footer>
                            <x-admin::button
                                class="primary-button"
                                :title="trans('Save Lead')"
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
                ref="emailLinkDrawer"
            >
                <x-slot:toggle>
                    <button
                        type="button"
                        class="primary-button"
                    >
                        @lang('admin::app.mail.view.link-mail')
                    </button>
                </x-slot>

                <x-slot:header class="p-3.5">
                    <!-- Apply Filter Title -->
                    <div class="flex items-center justify-between">
                        <p class="text-xl font-semibold text-gray-800 dark:text-white">
                            @lang('admin::app.mail.view.link-mail')
                        </p>
                    </div>
                </x-slot>
                
                <x-slot:content class="p-3.5">
                    <div class="flex flex-col gap-4">
                        <!-- Link to contact -->
                        <label class="font-semibold text-gray-700 cursor-pointer">
                            @{{ email?.person ? "@lang('admin::app.mail.view.linked-contact')" : "@lang('admin::app.mail.view.link-to-contact')" }}
                        </label>
                
                        <!-- Contact Lookup -->
                        <v-contact-lookup
                            @link-contact="linkContact"
                            @unlink-contact="unlinkContact"
                            @open-contact-modal="openContactModal"
                            :unlinking="unlinking"
                            :email="email"
                        ></v-contact-lookup>

                        <!-- Link to Lead -->
                        <label class="font-semibold text-gray-700 cursor-pointer">
                            @{{ email?.lead ? "@lang('admin::app.mail.view.linked-lead')" : "@lang('admin::app.mail.view.link-to-lead')" }}
                        </label>
                    
                        <!-- Lead Lookup -->
                        <v-lead-lookup
                            @link-lead="linkLead"
                            @unlink-lead="unlinkLead"
                            @open-lead-modal="openLeadModal"
                            :unlinking="unlinking"
                            :email="email"
                        ></v-lead-lookup>
                    </div>
                </x-slot>
            </x-admin::drawer>

            <!-- Create Contact Modal -->
            <v-create-contact ref="createContact"></v-create-contact>

            <!-- Create Lead Modal -->
            <v-create-lead ref="createLead"></v-create-lead>
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

                emits: ['on-discard'],

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
                            this.$emitter.emit('open-confirm-modal', {
                                agree: () => {
                                    this.$axios.post(`{{ route('admin.mail.delete', ':id') }}`.replace(':id', this.email.id), {
                                        _method: 'DELETE',
                                        type: 'trash'
                                    })
                                    .then ((response) => {
                                        if (response.status == 200) {
                                            this.$emitter.emit('add-flash', { type: 'success', message: response.data.message });
    
                                            this.$emit('on-discard');
                                        }
                                    });
                                }
                            });
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

                props: ['email', 'unlinking'],

                emits: ['link-contact', 'unlink-contact', 'open-contact-modal'],

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
                    linkContact(person) {
                        this.showPopup = false;

                        this.searchTerm = '';

                        this.selectedItem = person;

                        this.$emit('link-contact', person);
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

                        this.$emit('open-contact-modal');
                    },
                },
            });
        </script>

        <!-- Contact Lookup Component -->
        <script type="module">
            app.component('v-lead-lookup', {
                template: '#v-lead-lookup-template',

                props: ['email', 'unlinking'],

                emits: ['link-lead', 'unlink-lead', 'open-lead-modal'],

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
                    leads() {
                        return this.searchedResults.filter(item => 
                            item.title.toLowerCase().includes(this.searchTerm.toLowerCase())
                        );
                    },
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
                    linkLead(lead) {
                        this.showPopup = false;

                        this.searchTerm = '';

                        this.selectedItem = lead;

                        this.$emit('link-lead', lead);
                    },

                    unlinkLead() {
                        this.selectedItem = {};

                        this.$emit('unlink-lead');
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

                        this.$axios.get('{{ route('admin.leads.search') }}', {
                                params: { 
                                    ...this.params,
                                    query: this.searchTerm
                                },
                                cancelToken: this.cancelToken.token, 
                            })
                            .then(response => {
                                this.searchedResults = response.data;
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

                    toggleLeadModal() {
                        this.showPopup = false;

                        this.$emit('open-lead-modal');
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

                methods: {
                    toggleModal({ isActive }) {
                        if (! isActive) {
                            this.$parent.$refs.emailLinkDrawer.toggle();
                        }
                    },

                    create(params, { setErrors }) {
                        this.isStoring = true;

                        const formData = new FormData(this.$refs.contactForm);

                        this.$axios.post('{{ route('admin.contacts.persons.store') }}', formData)
                            .then(response => {
                                this.$emitter.emit('add-flash', { type: 'success', message: response.data.message });

                                this.$refs.contactModal.close();
                            })
                            .catch(error => {
                                if (error.response.status == 422) {
                                    setErrors(error.response.data.errors);
                                } else {
                                    this.$emitter.emit('add-flash', { type: 'error', message: error.response.data.message });
                                }
                            })
                            .finally(() => {
                                this.isStoring = false;

                                this.$parent.$refs.emailLinkDrawer.open();
                            });
                    },
                },
            });
        </script>

        <!-- Create Lead Modal Component -->
        <script type="module">
            app.component('v-create-lead', {
                template: '#v-create-lead-template',

                data() {
                    return {
                        isStoring: false,

                        
                        selectedType: "lead",

                        types: [
                            {
                                name: 'lead',
                                label: "{{ trans('admin::app.mail.view.lead-details') }}",
                            }, {
                                name: 'person',
                                label: "{{ trans('admin::app.mail.view.contact-person') }}",
                            }, {
                                name: 'product',
                                label: "{{ trans('admin::app.mail.view.product') }}",
                            },
                        ],
                    };
                },

                methods: {
                    toggleModal({ isActive }) {
                        if (! isActive) {
                            this.$parent.$refs.emailLinkDrawer.toggle();
                        }
                    },

                    create(params, { setErrors }) {
                        this.isStoring = true;

                        const formData = new FormData(this.$refs.leadForm);

                        formData.append('lead_pipeline_stage_id', 1)

                        this.$axios.post('{{ route('admin.leads.store') }}', formData)
                            .then(response => {
                                this.$emitter.emit('add-flash', { type: 'success', message: response.data.message });

                                this.$refs.leadModal.close();
                            })
                            .catch(error => {
                                if (error.response.status == 422) {
                                    setErrors(error.response.data.errors);
                                } else {
                                    this.$emitter.emit('add-flash', { type: 'error', message: error.response.data.message });
                                }
                            })
                            .finally(() => {
                                this.isStoring = false;

                                this.$parent.$refs.emailLinkDrawer.open();
                            });
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

                        unlinking: {
                            lead: false,
                            contact: false,
                        },
                    };
                },

                created() {
                    @if ($email->person)
                        this.email.person = @json($email->person);
                    @endif

                    @if ($email->lead)
                        this.email.lead = @json($email->lead);
                    @endif
                },

                methods: {
                    linkContact(person) {
                        this.email['person'] = person;

                        this.email['person_id'] = person.id;

                        this.$axios.post('{{ route('admin.mail.update', $email->id) }}', {
                            _method: 'PUT',
                            person_id: person.id,
                        })
                            .then (response => {                            
                                this.$emitter.emit('add-flash', { type: 'success', message: response.data.message });
                            })
                            .catch (error => {});
                    },

                    unlinkContact() {
                        this.unlinking.contact = true;

                        this.$axios.post('{{ route('admin.mail.update', $email->id) }}', {
                            _method: 'PUT',
                            person_id: null,
                        })
                            .then (response => {
                                this.email['person'] = this.email['person_id'] = null;

                                this.$emitter.emit('add-flash', { type: 'success', message: response.data.message });
                            })
                            .catch (error => {})
                            .finally(() => this.unlinking.contact = false);
                    },

                    linkLead(lead) {
                        this.email['lead'] = lead;

                        this.email['lead_id'] = lead.id;

                        this.$axios.post('{{ route('admin.mail.update', $email->id) }}', {
                            _method: 'PUT',
                            lead_id: lead.id,
                        })
                            .then (response => {
                                this.$emitter.emit('add-flash', { type: 'success', message: response.data.message });
                            })
                            .catch (error => {});
                    },

                    unlinkLead() {
                        this.unlinking.lead = true;

                        this.$axios.post('{{ route('admin.mail.update', $email->id) }}', {
                            _method: 'PUT',
                            lead_id: null,
                        })
                            .then (response => {
                                this.email['lead'] = this.email['lead_id'] = null;

                                this.$emitter.emit('add-flash', { type: 'success', message: response.data.message });
                            })
                            .catch (error => {})
                            .finally(() => this.unlinking.lead = false);
                    },

                    openContactModal() {
                        this.$refs.emailLinkDrawer.close();
                        
                        this.$refs.createContact.$refs.contactModal.open();
                    },

                    openLeadModal() {
                        this.$refs.emailLinkDrawer.close();

                        this.$refs.createLead.$refs.leadModal.open();
                    },
                },
            });
        </script>
    @endPushOnce
</x-admin::layouts>