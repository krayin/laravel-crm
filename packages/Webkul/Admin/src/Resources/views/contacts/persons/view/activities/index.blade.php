<!-- Lead Activities Vue Component -->
{!! view_render_event('admin.contacts.persons.view.activities.before', ['person' => $person]) !!}

<!-- Lead Activities Vue Component -->    
<v-lead-activities>
    <!-- Shimmer -->
    <x-admin::shimmer.leads.view.activities />
</v-lead-activities>

{!! view_render_event('admin.contacts.persons.view.activities.after', ['person' => $person]) !!}

@pushOnce('scripts')
    <script type="text/x-template" id="v-lead-activities-template">
        <template v-if="isLoading">
            <!-- Shimmer -->
            <x-admin::shimmer.leads.view.activities />
        </template>

        <template v-else>
            <div class="w-full bg-white">
                <div class="flex gap-4 border-b border-gray-200">
                    <div
                        v-for="type in types"
                        class="cursor-pointer px-4 py-2.5 text-sm font-medium"
                        :class="{'border-brandColor border-b-2 !text-brandColor transition': selectedType == type.name }"
                        @click="selectedType = type.name"
                    >
                        @{{ type.label }}
                    </div>
                </div>

                <div class="animate-[on-fade_0.5s_ease-in-out] p-4">
                    <!-- Activity List -->
                    <div class="flex flex-col gap-4">
                        <!-- Activity Item -->
                        <div
                            class="flex gap-2"
                            v-for="(activity, index) in filteredActivities"
                        >
                            <!-- Activity Icon -->
                            <div
                                class="mt-2 flex h-9 min-h-9 w-9 min-w-9 items-center justify-center rounded-full text-xl"
                                :class="typeClasses[activity.type] ?? typeClasses['activity']"
                            >
                            </div>
                            
                            <!-- Activity Details -->
                            <div
                                class="flex w-full justify-between gap-4 rounded-md p-4"
                                :class="{'bg-gray-100': index % 2 != 0 }"
                            >
                                <div class="flex flex-col gap-2">
                                    <!-- Activity Title -->
                                    <div class="flex flex-col gap-1">
                                        <p class="font-medium">
                                            @{{ activity.title }}
                                        </p>

                                        <template v-if="activity.type == 'mail'">
                                            <p></p>
                                        </template>

                                        <template v-else>
                                            <!-- Activity Schedule -->
                                            <p v-if="activity.schedule_from && activity.schedule_from">
                                                @lang('admin::app.contacts.persons.view.activities.index.scheduled-on'):
                                                
                                                @{{ $admin.formatDate(activity.schedule_from, 'd MMM yyyy, h:mm A') + ' - ' + $admin.formatDate(activity.schedule_from, 'd MMM yyyy, h:mm A') }}
                                            </p>

                                            <!-- Activity Participants -->
                                            <p v-if="activity.participants?.length">
                                                @lang('admin::app.contacts.persons.view.activities.index.participants'):

                                                <span class="after:content-[',_'] last:after:content-['']" v-for="(participant, index) in activity.participants">
                                                    @{{ participant.user?.name ?? participant.person.name }}
                                                </span>
                                            </p>

                                            <!-- Activity Location -->
                                            <p v-if="activity.location">
                                                @lang('admin::app.contacts.persons.view.activities.index.location'):

                                                @{{ activity.location }}
                                            </p>
                                        </template>
                                    </div>

                                    <!-- Activity Description -->
                                    <p v-if="activity.comment"
                                    >
                                        @{{ activity.comment }}
                                    </p>

                                    <!-- Attachments -->
                                    <div
                                        class="flex gap-2"
                                        v-if="activity.files.length"
                                    >
                                        <a
                                            :href="`{{ route('admin.activities.file_download', 'replaceID') }}`.replace('replaceID', file.id)"
                                            class="flex cursor-pointer items-center gap-1 rounded-md p-1.5"
                                            target="_blank"
                                            v-for="(file, index) in activity.files"
                                        >
                                            <span class="icon-attachmetent text-xl"></span>

                                            <span class="font-medium text-brandColor">
                                                @{{ file.name }}
                                            </span>
                                        </a>
                                    </div>

                                    <!-- Activity Time and User -->
                                    <div class="text-gray-500">
                                        @{{ $admin.formatDate(activity.created_at, 'd MMM yyyy, h:mm A') }},

                                        @{{ "@lang('admin::app.contacts.persons.view.activities.index.by-user', ['user' => 'replace'])".replace('replace', activity.user.name) }}
                                    </div>
                                </div>

                                <!-- Activity More Options -->
                                <x-admin::dropdown position="bottom-right">
                                    <x-slot:toggle>
                                        <template v-if="! isUpdating[activity.id]">
                                            <button
                                                class="icon-more flex h-7 w-7 cursor-pointer items-center justify-center rounded-md text-2xl transition-all hover:bg-gray-200"
                                            ></button>
                                        </template>

                                        <template v-else>
                                            <x-admin::spinner />
                                        </template>
                                    </x-slot>

                                    <x-slot:menu>
                                        <x-admin::dropdown.menu.item 
                                            v-if="! activity.is_done"
                                            @click="markAsDone(activity)"
                                        >
                                            <div class="flex items-center gap-2">
                                                <span class="icon-tick text-2xl"></span>
                                                
                                                @lang('admin::app.contacts.persons.view.activities.index.mark-as-done')
                                            </div>
                                        </x-admin::dropdown.menu.item>

                                        @if (bouncer()->hasPermission('activities.edit'))
                                            <x-admin::dropdown.menu.item>
                                                <a
                                                    class="flex items-center gap-2"
                                                    :href="'{{ route('admin.activities.edit', 'replaceId') }}'.replace('replaceId', activity.id)"
                                                    target="_blank"
                                                >
                                                    <span class="icon-edit text-2xl"></span>
                                                    
                                                    @lang('admin::app.contacts.persons.view.activities.index.edit')
                                                </a>
                                            </x-admin::dropdown.menu.item>
                                        @endif

                                        @if (bouncer()->hasPermission('activities.delete'))
                                            <x-admin::dropdown.menu.item @click="remove(activity)">
                                                <div class="flex items-center gap-2">
                                                    <span class="icon-delete text-2xl"></span>

                                                    @lang('admin::app.contacts.persons.view.activities.index.delete')
                                                </div>
                                            </x-admin::dropdown.menu.item>
                                        @endif
                                    </x-slot>
                                </x-admin::dropdown>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </template>
    </script>

    <script type="module">
        app.component('v-lead-activities', {
            template: '#v-lead-activities-template',

            data() {
                return {
                    isLoading: false,

                    isUpdating: {},
                    
                    activities: [],

                    selectedType: 'all',

                    typeClasses: {
                        mail: 'icon-mail bg-green-200 text-green-900',
                        note: 'icon-note bg-orange-200 text-orange-800',
                        call: 'icon-call bg-cyan-200 text-cyan-800',
                        meeting: 'icon-activity bg-blue-200 text-blue-800',
                        lunch: 'icon-activity bg-blue-200 text-blue-800',
                        file: 'icon-file bg-green-200 text-green-900',
                    },

                    types: [
                        {
                            name: 'all',
                            label: "{{ trans('admin::app.contacts.persons.view.activities.index.all') }}",
                        }, {
                            name: 'note',
                            label: "{{ trans('admin::app.contacts.persons.view.activities.index.notes') }}",
                        }, {
                            name: 'call',
                            label: "{{ trans('admin::app.contacts.persons.view.activities.index.calls') }}",
                        }, {
                            name: 'meeting',
                            label: "{{ trans('admin::app.contacts.persons.view.activities.index.meetings') }}",
                        }, {
                            name: 'lunch',
                            label: "{{ trans('admin::app.contacts.persons.view.activities.index.lunches') }}",
                        }, {
                            name: 'file',
                            label: "{{ trans('admin::app.contacts.persons.view.activities.index.files') }}",
                        }, {
                            name: 'email',
                            label: "{{ trans('admin::app.contacts.persons.view.activities.index.emails') }}",
                        },
                    ],
                };
            },

            computed: {
                filteredActivities() {
                    if (this.selectedType == 'all') {
                        return this.activities;
                    }

                    return this.activities.filter(activity => activity.type == this.selectedType);
                },
            },

            mounted() {
                this.get();
            },

            methods: {
                get() {
                    this.isLoading = true;

                    this.$axios.get("{{ route('admin.persons.activities.index', $person->id) }}")
                        .then(response => {
                            this.activities = response.data.data;

                            this.isLoading = false;
                        })
                        .catch(error => {
                            console.error(error);
                        });
                },

                markAsDone: function(activity) {
                    let self = this;

                    this.$emitter.emit('open-confirm-modal', {
                        agree: () => {
                            self.isUpdating[activity.id] = true;

                            this.$axios.put("{{ route('admin.activities.update', 'replaceId') }}".replace('replaceId', activity.id), {
                                    'is_done': 1
                                })
                                .then (function(response) {
                                    self.isUpdating[activity.id] = false;

                                    activity.is_done = 1;
                                    
                                    self.$emitter.emit('add-flash', { type: 'success', message: response.data.message });
                                })
                                .catch (function (error) {
                                    self.isUpdating[activity.id] = false;

                                    self.$emitter.emit('add-flash', { type: 'error', message: error.response.data.message });
                                });
                        },
                    });
                },

                remove: function(activity) {
                    let self = this;
                    
                    this.$emitter.emit('open-confirm-modal', {
                        agree: () => {
                            self.isUpdating[activity.id] = true;

                            this.$axios.delete("{{ route('admin.activities.delete', 'replaceId') }}".replace('replaceId', activity.id))
                                .then (function(response) {
                                    self.isUpdating[activity.id] = false;

                                    self.activities.splice(self.activities.indexOf(activity), 1);
                                    
                                    self.$emitter.emit('add-flash', { type: 'success', message: response.data.message });
                                })
                                .catch (function (error) {
                                    self.isUpdating[activity.id] = false;
                                    
                                    self.$emitter.emit('add-flash', { type: 'error', message: error.response.data.message });
                                });
                        },
                    });
                },
            }
        });
    </script>
@endPushOnce