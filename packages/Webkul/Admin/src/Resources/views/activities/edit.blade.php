<x-admin::layouts>
    <!-- Page Title -->
    <x-slot:title>
        @lang('admin::app.activities.edit.title')
    </x-slot>

    {!! view_render_event('admin.activities.edit.form.before') !!}

    <x-admin::form
        :action="route('admin.activities.update', $activity->id)"
        method="PUT"
    >
        <div class="flex flex-col gap-4">
            <div class="flex items-center justify-between rounded-lg border border-gray-200 bg-white px-4 py-2 text-sm dark:border-gray-800 dark:bg-gray-900 dark:text-gray-300">
                <div class="flex flex-col gap-2">
                    <!-- Breadcrumbs -->
                    <div class="flex cursor-pointer items-center">
                        <x-admin::breadcrumbs
                            name="activities.edit"
                            :entity="$activity"
                        />
                    </div>

                    <!-- Page Title -->
                    <div class="text-xl font-bold dark:text-gray-300">
                        @lang('admin::app.activities.edit.title')
                    </div>
                </div>

                <div class="flex items-center gap-x-2.5">
                    <!-- Create button for person -->
                    <div class="flex items-center gap-x-2.5">
                        {!! view_render_event('admin.activities.edit.save_button.before') !!}

                        <!-- Save Button -->
                        <button
                            type="submit"
                            class="primary-button"
                        >
                            @lang('admin::app.activities.edit.save-btn')
                        </button>
        
                        {!! view_render_event('admin.activities.edit.save_button.after') !!}
                    </div>
                </div>
            </div>

            <!-- Form Content -->
            <div class="flex gap-2.5 max-xl:flex-wrap">
                <!-- Left sub-component -->
                <div class="flex flex-1 flex-col gap-2 max-xl:flex-auto">
                    <div class="box-shadow rounded-lg border border-gray-200 bg-white p-4 dark:bg-gray-900 dark:border-gray-800">
                        {!! view_render_event('admin.activities.edit.form_controls.before') !!}

                        <!-- Schedule Date -->
                        <x-admin::form.control-group>
                            <div class="flex gap-2"> 
                                <div class="w-full">
                                    <x-admin::form.control-group.label class="required">
                                        @lang('admin::app.activities.edit.schedule_from')
                                    </x-admin::form.control-group.label>

                                    <x-admin::flat-picker.datetime class="!w-full" ::allow-input="false">
                                        <input
                                            value="{{ old('schedule_from') ?? $activity->schedule_from }}"
                                            class="flex w-full rounded-md border px-3 py-2 text-sm text-gray-600 transition-all hover:border-gray-400 dark:border-gray-800 dark:bg-gray-900 dark:text-gray-300 dark:hover:border-gray-400"
                                            placeholder="@lang('admin::app.activities.edit.schedule_from')"
                                        />
                                    </x-admin::flat-picker.datetime>
                                </div>

                                <div class="w-full">
                                    <x-admin::form.control-group.label class="required">
                                        @lang('admin::app.activities.edit.schedule_to')
                                    </x-admin::form.control-group.label>

                                    <x-admin::flat-picker.datetime class="!w-full" ::allow-input="false">
                                        <input
                                            value="{{ old('schedule_to') ?? $activity->schedule_to }}"
                                            class="flex w-full rounded-md border px-3 py-2 text-sm text-gray-600 transition-all hover:border-gray-400 dark:border-gray-800 dark:bg-gray-900 dark:text-gray-300 dark:hover:border-gray-400"
                                            placeholder="@lang('admin::app.activities.edit.schedule_to')"
                                        />
                                    </x-admin::flat-picker.datetime>
                                </div>
                            </div>
                        </x-admin::form.control-group>

                        <!-- Comment -->
                        <x-admin::form.control-group>
                            <x-admin::form.control-group.label>
                                @lang('admin::app.activities.edit.comment')
                            </x-admin::form.control-group.label>

                            <x-admin::form.control-group.control
                                type="textarea"
                                name="comment"
                                id="comment"
                                :value="old('comment') ?? $activity->comment"
                                :label="trans('admin::app.activities.edit.comment')"
                                :placeholder="trans('admin::app.activities.edit.comment')"
                            />
                            
                            <x-admin::form.control-group.error control-name="comment" />
                        </x-admin::form.control-group>

                        <!-- Participants -->
                        <x-admin::form.control-group>
                            <x-admin::form.control-group.label>
                                @lang('admin::app.activities.edit.participants')
                            </x-admin::form.control-group.label>

                            <!-- Participants Multilookup Vue Component -->
                            <v-multi-lookup-component>
                                <div 
                                    class="relative rounded border border-gray-200 px-2 py-1 hover:border-gray-400 focus:border-gray-400 dark:border-gray-800 dark:hover:border-gray-400 dark:focus:border-gray-400" 
                                    role="button"
                                >
                                    <ul class="flex flex-wrap items-center gap-1">
                                        <li>
                                            <input
                                                type="text"
                                                class="w-full px-1 py-1 dark:bg-gray-900 dark:text-gray-300"
                                                placeholder="@lang('admin::app.activities.edit.participants')"
                                            />
                                        </li>
                                    </ul>

                                    <span class="icon-down-arrow absolute top-1.5 text-2xl ltr:right-1.5 rtl:left-1.5"></span>
                                </div>
                            </v-multi-lookup-component>
                        </x-admin::form.control-group>

                        <!-- Lead -->
                        <x-admin::form.control-group class="!mb-0">
                            <x-admin::form.control-group.label>
                                @lang('admin::app.activities.edit.lead')
                            </x-admin::form.control-group.label>

                            <x-admin::attributes.edit.lookup/>

                            <!-- Lead Lookup Vue Component -->
                            <v-lookup-component
                                :attribute="{'code': 'lead_id', 'name': 'Lead', 'lookup_type': 'leads'}"
                                :value='@json($lookUpEntityData)'
                            >
                                <x-admin::form.control-group.control
                                    type="text"
                                    placeholder="@lang('admin::app.common.start-typing')"
                                />
                            </v-lookup-component>
                        </x-admin::form.control-group>

                        {!! view_render_event('admin.activities.edit.form_controls.after') !!}
                    </div>
                </div>

                <!-- Right sub-component -->
                <div class="flex w-[360px] max-w-full flex-col gap-2 max-sm:w-full">
                    {!! view_render_event('admin.activities.edit.accordion.general.before') !!}

                    <x-admin::accordion>
                        <x-slot:header>
                            <div class="flex items-center justify-between">
                                <p class="p-2.5 text-base font-semibold text-gray-800 dark:text-white">
                                    @lang('admin::app.activities.edit.general')
                                </p>
                            </div>
                        </x-slot>

                        <x-slot:content>
                            <!-- Title -->
                            <x-admin::form.control-group>
                                <x-admin::form.control-group.label class="required">
                                    @lang('admin::app.activities.edit.title')
                                </x-admin::form.control-group.label>
        
                                <x-admin::form.control-group.control
                                    type="text"
                                    name="title"
                                    id="title"
                                    rules="required"
                                    :value="old('title') ?? $activity->title"
                                    :label="trans('admin::app.activities.edit.title')"
                                    :placeholder="trans('admin::app.activities.edit.title')"
                                />

                                <x-admin::form.control-group.error control-name="title" />
                            </x-admin::form.control-group>
        
                            <!-- Edit Type -->
                            <x-admin::form.control-group>
                                <x-admin::form.control-group.label class="required">
                                    @lang('admin::app.activities.edit.type')
                                </x-admin::form.control-group.label>
        
                                <x-admin::form.control-group.control
                                    type="select"
                                    name="type"
                                    id="type"
                                    :value="old('type') ?? $activity->type"
                                    rules="required"
                                    :label="trans('admin::app.activities.edit.type')"
                                    :placeholder="trans('admin::app.activities.edit.type')"
                                >
                                    <option value="call">
                                        @lang('admin::app.activities.edit.call')
                                    </option>
        
                                    <option value="meeting">
                                        @lang('admin::app.activities.edit.meeting')
                                    </option>
        
                                    <option value="lunch">
                                        @lang('admin::app.activities.edit.lunch')
                                    </option>
                                </x-admin::form.control-group.control>
        
                                <x-admin::form.control-group.error control-name="type" />
                            </x-admin::form.control-group>

                            <!-- Location -->
                            <x-admin::form.control-group class="!mb-0">
                                <x-admin::form.control-group.label>
                                    @lang('admin::app.activities.edit.location')
                                </x-admin::form.control-group.label>

                                <x-admin::form.control-group.control
                                    type="text"
                                    name="location"
                                    id="location"
                                    :value="old('location') ?? $activity->location"
                                    rules="required"
                                    :label="trans('admin::app.activities.edit.location')"
                                    :placeholder="trans('admin::app.activities.edit.location')"
                                />

                                <x-admin::form.control-group.error control-name="location" />
                            </x-admin::form.control-group>
                        </x-slot>
                    </x-admin::accordion>

                    {!! view_render_event('admin.activities.edit.accordion.general.after') !!}
                </div>
            </div>
        </div>
    </x-admin::form>

    {!! view_render_event('admin.activities.edit.form.after') !!}

    @pushOnce('scripts')
        <script 
            type="text/x-template"
            id="v-multi-lookup-component-template"
        >
            <!-- Search Button -->
            <div class="relative">
                <div class="relative rounded border border-gray-200 px-2 py-1 hover:border-gray-400 focus:border-gray-400 dark:border-gray-800" role="button">
                    <ul class="flex flex-wrap items-center gap-1">
                        <!-- Added Participants -->
                        <template v-for="userType in ['users', 'persons']">
                            <li
                                class="flex items-center gap-1 rounded-md bg-slate-100 pl-2 dark:bg-slate-950 dark:text-gray-300"
                                v-for="(user, index) in addedParticipants[userType]"
                            >
                                <!-- Person and User Hidden Input Field -->
                                <input
                                    type="hidden"
                                    :name="`participants[${userType}][]`"
                                    :value="user.id"
                                />

                                @{{ user.name }}

                                <span
                                    class="icon-cross-large cursor-pointer p-0.5 text-xl"
                                    @click="remove(userType, user)"
                                ></span>
                            </li>
                        </template>

                        <!-- Search Input Box -->
                        <li>
                            <input
                                type="text"
                                class="w-full px-1 py-1 dark:bg-gray-900 dark:text-gray-300"
                                placeholder="@lang('admin::app.activities.edit.participants')"
                                v-model.lazy="searchTerm"
                                v-debounce="500"
                            />
                        </li>
                    </ul>

                    <!-- Search and Spinner Icon -->
                    <div>
                        <template v-if="! isSearching.users && ! isSearching.persons">
                            <span
                                class="absolute top-1.5 text-2xl ltr:right-1.5 rtl:left-1.5"
                                :class="[searchTerm.length >= 2 ? 'icon-up-arrow' : 'icon-down-arrow']"
                            ></span>
                        </template>

                        <template v-else>
                            <x-admin::spinner class="absolute top-2 ltr:right-2 rtl:left-2" />
                        </template>
                    </div>
                </div>

                <!-- Search Dropdown -->
                <div
                    class="absolute z-10 w-full rounded bg-white shadow-[0px_10px_20px_0px_#0000001F] dark:bg-gray-900"
                    v-if="searchTerm.length >= 2"
                >
                    <ul class="flex flex-col gap-1 p-2">
                        <!-- Users and Person Searched Participants -->
                        <li
                            class="flex flex-col gap-2"
                            v-for="userType in ['users', 'persons']"
                        >
                            <h3 class="text-sm font-bold text-gray-600 dark:text-gray-400">
                                <template v-if="userType === 'users'">
                                    @lang('admin::app.activities.edit.users')
                                </template>

                                <template v-else>
                                    @lang('admin::app.activities.edit.persons')
                                </template>
                            </h3>

                            <ul>
                                <li
                                    class="rounded-sm px-5 py-2 text-sm text-gray-800 dark:text-gray-300"
                                    v-if="! searchedParticipants[userType].length && ! isSearching[userType]"
                                >
                                    <p class="text-sm text-gray-500 dark:text-gray-400">
                                        @lang('admin::app.activities.edit.no-result-found')
                                    </p>
                                </li>

                                <li
                                    class="cursor-pointer rounded-sm px-3 py-2 text-sm text-gray-800 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-950"
                                    v-for="user in searchedParticipants[userType]"
                                    @click="add(userType, user)"
                                >
                                    @{{ user.name }}
                                </li>
                            </ul>
                        </li>
                    </ul>
                </div>
            </div>
        </script>

        <script type="module">
            app.component('v-multi-lookup-component', {
                template: '#v-multi-lookup-component-template',

                data() {
                    return {
                        isSearching: {
                            users: false,
                            
                            persons: false,
                        },

                        searchTerm: '',

                        addedParticipants: {
                            users: [],
                            
                            persons: [],
                        },

                        searchedParticipants: {
                            users: [],
                            
                            persons: [],
                        },

                        searchEnpoints: {
                            users: "{{ route('admin.settings.users.search') }}",
                            
                            persons: "{{ route('admin.contacts.persons.search') }}",
                        },
                    };
                },

                watch: {
                    searchTerm(newVal, oldVal) {
                        this.search('users');
                        
                        this.search('persons');
                    },
                },

                created() {
                    @json($activity->participants).forEach(participant => {
                        if (participant.user) {
                            this.addedParticipants.users.push(participant.user);
                        } else if (participant.person) {
                            this.addedParticipants.persons.push(participant.person);
                        }
                    });
                },

                methods: {
                    search(userType) {
                        if (this.searchTerm.length <= 1) {
                            this.searchedParticipants[userType] = [];

                            this.isSearching[userType] = false;

                            return;
                        }

                        this.isSearching[userType] = true;

                        this.$axios.get(this.searchEnpoints[userType], {
                                params: {
                                    search: 'name:' + this.searchTerm,
                                    searchFields: 'name:like',
                                }
                            })
                            .then ((response) => {
                                this.addedParticipants[userType].forEach(addedParticipant => 
                                    response.data.data = response.data.data.filter(participant => participant.id !== addedParticipant.id)
                                );

                                this.searchedParticipants[userType] = response.data.data;

                                this.isSearching[userType] = false;
                            })
                            .catch (function (error) {
                                this.isSearching[userType] = false;
                            });
                    },

                    add(userType, participant) {
                        this.addedParticipants[userType].push(participant);

                        this.searchTerm = '';

                        this.searchedParticipants = {
                            users: [],
                            
                            persons: [],
                        };
                    },

                    remove(userType, participant) {
                        this.addedParticipants[userType] = this.addedParticipants[userType].filter(addedParticipant => 
                            addedParticipant.id !== participant.id
                        );
                    },
                },
            });
        </script>
    @endPushOnce
</x-admin::layouts>
