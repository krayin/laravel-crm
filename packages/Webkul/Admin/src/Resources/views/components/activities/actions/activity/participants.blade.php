{!! view_render_event('admin.components.activities.actions.activity.participants.before') !!}

<!-- Participants Vue Component -->
<v-activity-participants></v-activity-participants>

{!! view_render_event('admin.components.activities.actions.activity.participants.after') !!}

@pushOnce('scripts')
    <script type="text/x-template" id="v-activity-participants-template">
        <!-- Search Button -->
        <div class="relative">
            <div 
                class="relative rounded border border-gray-200 px-2 py-1 hover:border-gray-400 focus:border-gray-400 dark:border-gray-800 dark:hover:border-gray-400" 
                role="button"
            >
                <ul class="flex flex-wrap items-center gap-1">
                    <template v-for="userType in ['users', 'persons']">
                        {!! view_render_event('admin.components.activities.actions.activity.participants.user_type.before') !!}

                        <li
                            class="flex items-center gap-1 rounded-md bg-slate-100 pl-2 dark:bg-gray-950 dark:text-gray-300"
                            v-for="(user, index) in addedParticipants[userType]"
                        >
                            {!! view_render_event('admin.components.activities.actions.activity.participants.user_type.user.before') !!}

                            <!-- User Id -->
                            <x-admin::form.control-group.control
                                type="hidden"
                                ::name="'participants.' + userType + '[' + index + ']'"
                                ::value="user.id"
                            />

                            @{{ user.name }}

                            <span
                                class="icon-cross-large cursor-pointer p-0.5 text-xl"
                                @click="remove(userType, user)"
                            ></span>

                            {!! view_render_event('admin.components.activities.actions.activity.participants.user_type.user.after') !!}
                        </li>

                        {!! view_render_event('admin.components.activities.actions.activity.participants.user_type.after') !!}
                    </template>

                    <li>
                        {!! view_render_event('admin.components.activities.actions.activity.participants.search_term.before') !!}

                        <input
                            type="text"
                            class="w-full px-1 py-1 dark:bg-gray-900 dark:text-gray-300"
                            placeholder="@lang('admin::app.components.activities.actions.activity.participants.placeholder')"
                            v-model.lazy="searchTerm"
                            v-debounce="500"
                        />

                        {!! view_render_event('admin.components.activities.actions.activity.participants.search_term.after') !!}
                    </li>
                </ul>

                <div>
                    <template v-if="! isSearching.users && ! isSearching.persons">
                        <span
                            class="absolute right-1.5 top-1.5 text-2xl"
                            :class="[searchTerm.length >= 2 ? 'icon-up-arrow' : 'icon-down-arrow']"
                        ></span>
                    </template>

                    <template v-else>
                        <x-admin::spinner class="absolute right-2 top-2" />
                    </template>
                </div>
            </div>

            {!! view_render_event('admin.components.activities.actions.activity.participants.dropdown.before') !!}

            <!-- Search Dropdown -->
            <div
                class="absolute z-10 w-full rounded bg-white shadow-[0px_10px_20px_0px_#0000001F] dark:bg-gray-900"
                v-if="searchTerm.length >= 2"
            >
                <ul class="flex flex-col gap-1 p-2">
                    <!-- Users -->
                    <li
                        class="flex flex-col gap-2"
                        v-for="userType in ['users', 'persons']"
                    >
                        {!! view_render_event('admin.components.activities.actions.activity.participants.dropdown.user_type.before') !!}

                        <h3 class="text-sm font-bold text-gray-600 dark:text-gray-300">
                            <template v-if="userType === 'users'">
                                @lang('admin::app.components.activities.actions.activity.participants.users')
                            </template>

                            <template v-else>
                                @lang('admin::app.components.activities.actions.activity.participants.persons')
                            </template>
                        </h3>

                        {!! view_render_event('admin.components.activities.actions.activity.participants.dropdown.user_type.after') !!}

                        {!! view_render_event('admin.components.activities.actions.activity.participants.dropdown.no_results.before') !!}

                        <ul>
                            <li
                                class="rounded-sm px-5 py-2 text-sm text-gray-800 dark:text-white"
                                v-if="! searchedParticipants[userType].length && ! isSearching[userType]"
                            >
                                <p class="text-sm text-gray-500 dark:text-gray-400">
                                    @lang('admin::app.components.activities.actions.activity.participants.no-results')
                                </p>
                            </li>

                            <li
                                class="cursor-pointer rounded-sm px-3 py-2 text-sm text-gray-800 hover:bg-gray-100 dark:text-white dark:hover:bg-gray-950"
                                v-for="user in searchedParticipants[userType]"
                                @click="add(userType, user)"
                            >
                                @{{ user.name }}
                            </li>
                        </ul>

                        {!! view_render_event('admin.components.activities.actions.activity.participants.dropdown.no_results.after') !!}
                    </li>
                </ul>
            </div>

            {!! view_render_event('admin.components.activities.actions.activity.participants.dropdown.after') !!}
        </div>
    </script>

    <script type="module">
        app.component('v-activity-participants', {
            template: '#v-activity-participants-template',

            props: {
                participants: {
                    type: Object,
                    default: () => ({
                        users: [],

                        persons: [],
                    })
                }
            },

            data: function () {
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
                }
            },

            watch: {
                searchTerm(newVal, oldVal) {
                    this.search('users');
                    
                    this.search('persons');
                },
            },

            mounted() {
                this.addedParticipants = this.participants;
            },

            methods: {
                search(userType) {
                    if (this.searchTerm.length <= 1) {
                        this.searchedParticipants[userType] = [];

                        this.isSearching[userType] = false;

                        return;
                    }

                    this.isSearching[userType] = true;

                    let self = this;
                    
                    this.$axios.get(this.searchEnpoints[userType], {
                            params: {
                                search: 'name:' + this.searchTerm,
                                searchFields: 'name:like',
                            }
                        })
                        .then (function(response) {
                            self.addedParticipants[userType].forEach(function(addedParticipant) {
                                response.data.data = response.data.data.filter(function(participant) {
                                    return participant.id !== addedParticipant.id;
                                });
                            });

                            self.searchedParticipants[userType] = response.data.data;

                            self.isSearching[userType] = false;
                        })
                        .catch (function (error) {
                            self.isSearching[userType] = false;
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
                    this.addedParticipants[userType] = this.addedParticipants[userType].filter(function(addedParticipant) {
                        return addedParticipant.id !== participant.id;
                    });
                },
            },
        });
    </script>
@endPushOnce