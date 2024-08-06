<x-admin::layouts>
    <!-- Page Title -->
    <x-slot:title>
        @lang('admin::app.activities.index.edit.title')
    </x-slot>

    {!! view_render_event('krayin.admin.activities.edit.form.before') !!}

    <x-admin::form
        :action="route('admin.activities.update', $activity->id)"
        method="PUT"
    >
        <div class="flex items-center justify-between">
            <p class="text-xl font-bold text-gray-800 dark:text-white">
                @lang('admin::app.activities.index.edit.title')
            </p>

            <div class="flex items-center gap-x-2.5">
                {!! view_render_event('krayin.admin.activities.edit.back_button.before') !!}

                <!-- Back Button -->
                <a
                    href="{{ route('admin.activities.index') }}"
                    class="transparent-button hover:bg-gray-200 dark:text-white dark:hover:bg-gray-800"
                >
                    @lang('admin::app.activities.index.edit.back-btn')
                </a>

                {!! view_render_event('krayin.admin.activities.edit.back_button.after') !!}

                {!! view_render_event('krayin.admin.activities.edit.back_button.before') !!}

                <!-- Save Button -->
                <button
                    type="submit"
                    class="primary-button"
                >
                    @lang('admin::app.activities.index.edit.save-btn')
                </button>
 
                {!! view_render_event('krayin.admin.activities.edit.back_button.after') !!}
            </div>
        </div>

        <!-- body content -->
        <div class="mt-3.5 flex gap-2.5 max-xl:flex-wrap">
            <!-- Left sub-component -->
            <div class="flex flex-1 flex-col gap-2 max-xl:flex-auto">
                <!-- Access Control Input Fields -->
                <div class="box-shadow rounded bg-white p-4 dark:bg-gray-900">
                    <x-admin::form.control-group>
                        <div class="flex gap-2"> 
                            <div class="w-full">
                                <x-admin::form.control-group.label class="required">
                                    @lang('admin::app.activities.index.edit.schedule_from')
                                </x-admin::form.control-group.label>

                                <x-admin::flat-picker.datetime class="!w-full" ::allow-input="false">
                                    <input
                                        value="{{ old('schedule_from') ?? $activity->schedule_from }}"
                                        class="flex w-full rounded-md border px-3 py-2 text-sm text-gray-600 transition-all hover:border-gray-400 dark:border-gray-800 dark:bg-gray-900 dark:text-gray-300 dark:hover:border-gray-400"
                                        placeholder="@lang('admin::app.activities.index.edit.schedule_from')"
                                    />
                                </x-admin::flat-picker.datetime>
                            </div>

                            <div class="w-full">
                                <x-admin::form.control-group.label class="required">
                                    @lang('admin::app.activities.index.edit.schedule_to')
                                </x-admin::form.control-group.label>

                                <x-admin::flat-picker.datetime class="!w-full" ::allow-input="false">
                                    <input
                                        value="{{ old('schedule_to') ?? $activity->schedule_to }}"
                                        class="flex w-full rounded-md border px-3 py-2 text-sm text-gray-600 transition-all hover:border-gray-400 dark:border-gray-800 dark:bg-gray-900 dark:text-gray-300 dark:hover:border-gray-400"
                                        placeholder="@lang('admin::app.activities.index.edit.schedule_to')"
                                    />
                                </x-admin::flat-picker.datetime>
                            </div>
                        </div>
                    </x-admin::form.control-group>

                    <x-admin::form.control-group>
                        <x-admin::form.control-group.label>
                            @lang('admin::app.activities.index.edit.comment')
                        </x-admin::form.control-group.label>

                        <x-admin::form.control-group.control
                            type="textarea"
                            name="comment"
                            id="comment"
                            :value="old('comment') ?? $activity->comment"
                            :label="trans('admin::app.activities.index.edit.comment')"
                            :placeholder="trans('admin::app.activities.index.edit.comment')"
                        />
                        <x-admin::form.control-group.error control-name="comment" />
                    </x-admin::form.control-group>

                    <v-multi-lookup-component>
                        <x-admin::form.control-group.label>
                            @lang('admin::app.activities.index.edit.participants')
                        </x-admin::form.control-group.label>

                        <input 
                            type="text"
                            class="block w-full rounded-lg border bg-white py-2 leading-6 text-gray-600 transition-all hover:border-gray-400 dark:border-gray-800 dark:bg-gray-900 dark:text-gray-300 ltr:pl-3 ltr:pr-10 rtl:pl-10 rtl:pr-3"
                            placeholder="@lang('admin::app.activities.index.edit.participants')"
                        >
                    </v-multi-lookup-component>

                    <x-admin::form.control-group class="!mt-4">
                        <x-admin::form.control-group.label>
                            @lang('admin::app.activities.index.edit.lead')
                        </x-admin::form.control-group.label>

                        <x-admin::attributes.edit.lookup/>

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
                </div>
            </div>

            <!-- Right sub-component -->
            <div class="flex w-[360px] max-w-full flex-col gap-2 max-sm:w-full">
                {!! view_render_event('krayin.admin.activities.edit.accordion.general.before') !!}

                <x-admin::accordion>
                    <x-slot:header>
                        <div class="flex items-center justify-between">
                            <p class="p-2.5 text-base font-semibold text-gray-800 dark:text-white">
                                @lang('admin::app.activities.index.edit.general')
                            </p>
                        </div>
                    </x-slot>

                    <x-slot:content>
                        <x-admin::form.control-group>
                            <x-admin::form.control-group.label class="required">
                                @lang('admin::app.activities.index.edit.title')
                            </x-admin::form.control-group.label>
    
                            <x-admin::form.control-group.control
                                type="text"
                                name="title"
                                id="title"
                                rules="required"
                                :value="old('title') ?? $activity->title"
                                :label="trans('admin::app.activities.index.edit.title')"
                                :placeholder="trans('admin::app.activities.index.edit.title')"
                            />
                            <x-admin::form.control-group.error control-name="title" />
                        </x-admin::form.control-group>
    
                        <x-admin::form.control-group>
                            <x-admin::form.control-group.label class="required">
                                @lang('admin::app.activities.index.edit.type')
                            </x-admin::form.control-group.label>
    
                            <x-admin::form.control-group.control
                                type="select"
                                name="type"
                                id="type"
                                value="{{ old('type') ?? $activity->type }}"
                                rules="required"
                                :label="trans('admin::app.activities.index.edit.type')"
                                :placeholder="trans('admin::app.activities.index.edit.type')"
                            >
                                <option value="call">
                                    @lang('admin::app.activities.index.edit.call')
                                </option>
    
                                <option value="meeting">
                                    @lang('admin::app.activities.index.edit.meeting')
                                </option>
    
                                <option value="lunch">
                                    @lang('admin::app.activities.index.edit.lunch')
                                </option>
                            </x-admin::form.control-group.control>
    
                            <x-admin::form.control-group.error control-name="type" />
                        </x-admin::form.control-group>

                        <x-admin::form.control-group>
                            <x-admin::form.control-group.label>
                                @lang('admin::app.activities.index.edit.location')
                            </x-admin::form.control-group.label>

                            <x-admin::form.control-group.control
                                type="text"
                                name="location"
                                id="location"
                                value="{{ old('location') ?? $activity->location }}"
                                rules="required"
                                :label="trans('admin::app.activities.index.edit.location')"
                                :placeholder="trans('admin::app.activities.index.edit.location')"
                            />
                            <x-admin::form.control-group.error control-name="location" />
                        </x-admin::form.control-group>
                    </x-slot>
                </x-admin::accordion>

                {!! view_render_event('krayin.admin.activities.edit.accordion.general.after') !!}
            </div>
        </div>
    </x-admin::form>

    {!! view_render_event('krayin.admin.activities.edit.form.after') !!}

    @pushOnce('scripts')
        <script 
            type="text/x-template"
            id="v-multi-lookup-component-template"
        >
            <x-admin::form.control-group class="!mb-0">
                <x-admin::form.control-group.label>
                    @lang('admin::app.activities.index.edit.participants')
                </x-admin::form.control-group.label>

                <span class="relative inline-block w-full">
                    <input 
                        type="text"
                        class="block w-full rounded-lg border bg-white py-2 leading-6 text-gray-600 transition-all hover:border-gray-400 dark:border-gray-800 dark:bg-gray-900 dark:text-gray-300 ltr:pl-3 ltr:pr-10 rtl:pl-10 rtl:pr-3"
                        placeholder="@lang('admin::app.activities.index.edit.participants')"
                        v-model.lazy="searchTerm"
                        v-debounce="500"
                    >
                
                    <svg
                        class="absolute right-3 top-3 h-5 w-5 animate-spin dark:text-gray-300"
                        xmlns="http://www.w3.org/2000/svg"
                        fill="none" 
                        aria-hidden="true"
                        viewBox="0 0 24 24"
                        v-if="isSearching"
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
                </span>
            </x-admin::form.control-group>

            <div class="flex flex-wrap gap-2 pt-1.5">
                <p
                    v-for="(participant, index) in participants.users"
                    class="flex items-center rounded bg-gray-600 px-2 py-1 font-semibold text-white"
                >
                    <input
                        type="hidden"
                        name="participants[users][]"
                        :value="participant.id"
                    />

                    <span>@{{ participant.name }}</span>
            
                    <span 
                        @click="removeParticipant('users', participant)"
                        class="icon-cross-large cursor-pointer text-lg text-white ltr:ml-1.5 rtl:mr-1.5"
                    ></span>
                </p>

                <p
                    v-for="(participant, index) in participants.persons"
                    class="flex items-center rounded bg-gray-600 px-2 py-1 font-semibold text-white"
                >
                    <input
                        type="hidden"
                        name="participants[persons][]"
                        :value="participant.id"
                    />

                    <span>@{{ participant.name }}</span>
            
                    <span 
                        @click="removeParticipant('persons', participant)"
                        class="icon-cross-large cursor-pointer text-lg text-white ltr:ml-1.5 rtl:mr-1.5"
                    ></span>
                </p>
            </div>

            <div class="relative">
                <div
                    class="absolute top-1 z-10 w-full border bg-white shadow-[0px_0px_0px_0px_rgba(0,0,0,0.10),0px_1px_3px_0px_rgba(0,0,0,0.10),0px_5px_5px_0px_rgba(0,0,0,0.09),0px_12px_7px_0px_rgba(0,0,0,0.05),0px_22px_9px_0px_rgba(0,0,0,0.01),0px_34px_9px_0px_rgba(0,0,0,0.00)] dark:border-gray-800 dark:bg-gray-900"
                    v-if="isDropdownOpen"
                >
                    <div class="flex flex-col overflow-y-auto">
                        <div>
                            <x-admin::form.control-group.label class="p-2 !pb-0 font-semibold">
                                @lang('admin::app.activities.index.edit.persons')
                            </x-admin::form.control-group.label>
    
                            <div
                                class="cursor-pointer border-b p-2 text-sm font-semibold text-gray-600 last:border-b-0 hover:bg-gray-100 dark:border-gray-800 dark:text-gray-300 dark:hover:bg-gray-950"
                                v-for="participant in searchedParticipants.persons"
                                @click="addParticipant('persons', participant)"
                            >
                                <span>@{{ participant.name }}</span>
                            </div>
    
                            <span 
                                v-if='
                                ! searchedParticipants.persons.length 
                                && searchTerm.length
                                && ! isSearching'
                                class="px-4 text-sm"
                            >
                                @lang('admin::app.activities.index.edit.no-result-found')
                            </span>
                        </div>
                        
                        <div>
                            <x-admin::form.control-group.label class="p-2 !pb-0 font-semibold">
                                @lang('admin::app.activities.index.edit.users')
                            </x-admin::form.control-group.label>
    
                            <div
                                class="cursor-pointer border-b p-2 text-sm font-semibold text-gray-600 last:border-b-0 hover:bg-gray-100 dark:border-gray-800 dark:text-gray-300 dark:hover:bg-gray-950"
                                v-for="participant in searchedParticipants.users"
                                @click="addParticipant('users', participant)"
                            >
                                <span>@{{ participant.name }}</span>
                            </div>
    
                            <span 
                                v-if='
                                ! searchedParticipants.users.length 
                                && searchTerm.length
                                && ! isSearching'
                                class="px-4 text-sm"
                            >
                                @lang('admin::app.activities.index.edit.no-result-found')
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </script>

        <script type="module">
            app.component('v-multi-lookup-component', {
                template: '#v-multi-lookup-component-template',

                data() {
                    return {
                        searchTerm: '',
                        
                        isSearching: false,

                        isDropdownOpen: false,
                        
                        data: @json($activity->participants),

                        searchedParticipants: {
                            users: [],
                            persons: [],
                        },

                        participants: {
                            users: [],
                            persons: [],
                        },
                    };
                },

                watch: {
                    /**
                     * Watch the search term and search for the participants.
                     * 
                     * @param {String} newVal
                     * @param {String} oldVal
                     * @return {Void}
                     */
                    searchTerm(newVal, oldVal) {
                        this.search();
                    },
                },

                mounted() {
                    this.data.forEach((participant) => {
                        const { id, name } = participant.user ? participant.user : participant.person;

                        const targetArray = participant.user ? this.participants.users : this.participants.persons;

                        targetArray.push({ id, name });
                    });
                },

                methods: {
                    /**
                     * Search for the participants.
                     * 
                     * @return {Void}
                     */
                    search() {
                        if (this.searchTerm.length <= 1) {
                            this.searchedResults = [];

                            this.isSearching = false;

                            this.isDropdownOpen = false;

                            return;
                        }

                        this.isSearching = true;

                        this.isDropdownOpen = true;

                        this.$axios.get('{{ route('admin.activities.search_participants') }}', {
                                params: {
                                    query: this.searchTerm,
                                }
                            })
                            .then (response => {
                                ['users', 'persons'].forEach((userType) => {
                                    if (this.participants[userType].length) {
                                        this.participants[userType].forEach((addedUser) => {
                                            response.data[userType].forEach((user, index) => {
                                                if (user.id == addedUser.id) {
                                                    response.data[userType].splice(index, 1);
                                                }
                                            });

                                        })
                                    }
                                })

                                this.searchedParticipants = response.data;

                                this.isSearching = false;
                            })
                            .catch (error => this.isSearching = false);
                    },

                    /**
                     * Add participant to the list.
                     * 
                     * @param {String} type
                     * @param {Object} participant
                     * @return {Void}
                     */
                    addParticipant(type, participant) {
                        this.searchTerm = '';

                        this.isDropdownOpen = false;

                        this.searchedParticipants = {
                            users: [],
                            persons: [],
                        };

                        this.participants[type].push(participant);
                    },

                    /**
                     * Remove participant from the list.
                     * 
                     * @param {String} type
                     * @param {Object} participant
                     * @return {Void}
                     */
                    removeParticipant(type, participant) {
                        this.participants[type] = this.participants[type].filter(p => p.id !== participant.id);
                    },
                },
            });
        </script>
    @endPushOnce
</x-admin::layouts>
