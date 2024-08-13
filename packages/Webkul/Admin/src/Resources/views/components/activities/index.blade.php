@props([
    'endpoint',
    'emailDetachEndpoint' => null,
    'types'               => null,
    'extraTypes'          => null,
])

<!-- Lead Activities Vue Component -->
<v-activities
    endpoint="{{ $endpoint }}"
    email-detach-endpoint="{{ $emailDetachEndpoint }}"
    @if($types):types='@json($types)'@endif
    @if($extraTypes):extra-types='@json($extraTypes)'@endif
>
    <!-- Shimmer -->
    <x-admin::shimmer.activities />

    @foreach ($extraTypes ?? [] as $type)
        <template v-slot:{{ $type['name'] }}>
            {{ ${$type['name']} ?? '' }}
        </template>
    @endforeach
</v-activities>

@pushOnce('scripts')
    <script type="text/x-template" id="v-activities-template">
        <template v-if="isLoading">
            <!-- Shimmer -->
            <x-admin::shimmer.activities />
        </template>

        <template v-else>
            <div class="w-full bg-white">
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

                <!-- Show Default Activities if selectedType not in extraTypes -->
                <template v-if="! extraTypes.find(type => type.name == selectedType)">
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
                                    :class="typeClasses[activity.type] ?? typeClasses['default']"
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

                                            <template v-if="activity.type == 'email'">
                                                <p>
                                                    @lang('admin::app.components.activities.index.from'):

                                                    @{{ activity.additional.from }}
                                                </p>

                                                <p>
                                                    @lang('admin::app.components.activities.index.to'):

                                                    @{{ activity.additional.to.join(', ') }}
                                                </p>

                                                <p v-if="activity.additional.cc">
                                                    @lang('admin::app.components.activities.index.cc'):

                                                    @{{ activity.additional.cc.join(', ') }}
                                                </p>

                                                <p v-if="activity.additional.bcc">
                                                    @lang('admin::app.components.activities.index.bcc'):

                                                    @{{ activity.additional.bcc.join(', ') }}
                                                </p>
                                            </template>

                                            <template v-else>
                                                <!-- Activity Schedule -->
                                                <p v-if="activity.schedule_from && activity.schedule_from">
                                                    @lang('admin::app.components.activities.index.scheduled-on'):
                                                    
                                                    @{{ $admin.formatDate(activity.schedule_from, 'd MMM yyyy, h:mm A') + ' - ' + $admin.formatDate(activity.schedule_from, 'd MMM yyyy, h:mm A') }}
                                                </p>

                                                <!-- Activity Participants -->
                                                <p v-if="activity.participants?.length">
                                                    @lang('admin::app.components.activities.index.participants'):

                                                    <span class="after:content-[',_'] last:after:content-['']" v-for="(participant, index) in activity.participants">
                                                        @{{ participant.user?.name ?? participant.person.name }}
                                                    </span>
                                                </p>

                                                <!-- Activity Location -->
                                                <p v-if="activity.location">
                                                    @lang('admin::app.components.activities.index.location'):

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
                                                <span class="icon-attached-file text-xl"></span>

                                                <span class="font-medium text-brandColor">
                                                    @{{ file.name }}
                                                </span>
                                            </a>
                                        </div>

                                        <!-- Activity Time and User -->
                                        <div class="text-gray-500">
                                            @{{ $admin.formatDate(activity.created_at, 'd MMM yyyy, h:mm A') }},

                                            @{{ "@lang('admin::app.components.activities.index.by-user', ['user' => 'replace'])".replace('replace', activity.user.name) }}
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

                                        <x-slot:menu class="!min-w-40">
                                            <template v-if="activity.type != 'email'">
                                                @if (bouncer()->hasPermission('activities.edit'))
                                                    <x-admin::dropdown.menu.item 
                                                        v-if="! activity.is_done"
                                                        @click="markAsDone(activity)"
                                                    >
                                                        <div class="flex items-center gap-2">
                                                            <span class="icon-tick text-2xl"></span>
                                                            
                                                            @lang('admin::app.components.activities.index.mark-as-done')
                                                        </div>
                                                    </x-admin::dropdown.menu.item>
                                                    
                                                    <x-admin::dropdown.menu.item>
                                                        <a
                                                            class="flex items-center gap-2"
                                                            :href="'{{ route('admin.activities.edit', 'replaceId') }}'.replace('replaceId', activity.id)"
                                                            target="_blank"
                                                        >
                                                            <span class="icon-edit text-2xl"></span>
                                                            
                                                            @lang('admin::app.components.activities.index.edit')
                                                        </a>
                                                    </x-admin::dropdown.menu.item>
                                                @endif

                                                @if (bouncer()->hasPermission('activities.delete'))
                                                    <x-admin::dropdown.menu.item @click="remove(activity)">
                                                        <div class="flex items-center gap-2">
                                                            <span class="icon-delete text-2xl"></span>

                                                            @lang('admin::app.components.activities.index.delete')
                                                        </div>
                                                    </x-admin::dropdown.menu.item>
                                                @endif
                                            </template>

                                            <template v-else>
                                                @if (bouncer()->hasPermission('mail.view'))
                                                    <x-admin::dropdown.menu.item>
                                                        <a
                                                            :href="'{{ route('admin.mail.view', ['route' => 'replaceFolder', 'id' => 'replaceMailId']) }}'.replace('replaceFolder', activity.additional.folders[0]).replace('replaceMailId', activity.id)"
                                                            class="flex items-center gap-2"
                                                            target="_blank"
                                                        >
                                                            <span class="icon-eye text-2xl"></span>

                                                            @lang('admin::app.components.activities.index.view')
                                                        </a>
                                                    </x-admin::dropdown.menu.item>
                                                @endif
                                                
                                                <x-admin::dropdown.menu.item @click="unlinkEmail(activity)">
                                                    <div class="flex items-center gap-2">
                                                        <span class="icon-attachment text-2xl"></span>

                                                        @lang('admin::app.components.activities.index.unlink')
                                                    </div>
                                                </x-admin::dropdown.menu.item>
                                            </template>
                                        </x-slot>
                                    </x-admin::dropdown>
                                </div>
                            </div>

                            <!-- Empty Placeholder -->
                            <div
                                class="grid justify-center justify-items-center gap-3.5 py-12"
                                v-if="! filteredActivities.length"
                            >
                                <img :src="typeIllustrations[selectedType]?.image ?? typeIllustrations['all'].image">
                                
                                <div class="flex flex-col items-center gap-2">
                                    <p class="text-xl font-semibold">
                                        @{{ typeIllustrations[selectedType]?.title ?? typeIllustrations['all'].title }}
                                    </p>

                                    <p class="text-gray-400">
                                        @{{ typeIllustrations[selectedType]?.description ?? typeIllustrations['all'].description }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </template>

                <template v-else>
                    <template v-for="type in extraTypes">
                        <div v-show="selectedType == type.name">
                            <slot :name="type.name"></slot>
                        </div>
                    </template>
                </template>
            </div>
        </template>
    </script>

    <script type="module">
        app.component('v-activities', {
            template: '#v-activities-template',

            props: {
                endpoint: {
                    type: String,
                    default: '',
                },

                emailDetachEndpoint: {
                    type: String,
                    default: '',
                },

                types: {
                    type: Array,
                    default: [
                        {
                            name: 'all',
                            label: "{{ trans('admin::app.components.activities.index.all') }}",
                        }, {
                            name: 'planned',
                            label: "{{ trans('admin::app.components.activities.index.planned') }}",
                        }, {
                            name: 'note',
                            label: "{{ trans('admin::app.components.activities.index.notes') }}",
                        }, {
                            name: 'call',
                            label: "{{ trans('admin::app.components.activities.index.calls') }}",
                        }, {
                            name: 'meeting',
                            label: "{{ trans('admin::app.components.activities.index.meetings') }}",
                        }, {
                            name: 'lunch',
                            label: "{{ trans('admin::app.components.activities.index.lunches') }}",
                        }, {
                            name: 'file',
                            label: "{{ trans('admin::app.components.activities.index.files') }}",
                        }, {
                            name: 'email',
                            label: "{{ trans('admin::app.components.activities.index.emails') }}",
                        }
                    ],
                },

                extraTypes: {
                    type: Array,
                    default: [],
                },
            },

            data() {
                return {
                    isLoading: false,

                    isUpdating: {},
                    
                    activities: [],

                    selectedType: 'all',

                    typeClasses: {
                        email: 'icon-mail bg-green-200 text-green-900',
                        note: 'icon-note bg-orange-200 text-orange-800',
                        call: 'icon-call bg-cyan-200 text-cyan-800',
                        meeting: 'icon-activity bg-blue-200 text-blue-800',
                        lunch: 'icon-activity bg-blue-200 text-blue-800',
                        file: 'icon-file bg-green-200 text-green-900',
                        default: 'icon-activity bg-blue-200 text-blue-800',
                    },

                    typeIllustrations: {
                        all: {
                            image: "{{ admin_vite()->asset('images/empty-placeholders/activities.svg') }}",
                            title: "{{ trans('admin::app.components.activities.index.empty-placeholders.all.title') }}",
                            description: "{{ trans('admin::app.components.activities.index.empty-placeholders.all.description') }}",
                        },
                        
                        planned: {
                            image: "{{ admin_vite()->asset('images/empty-placeholders/plans.svg') }}",
                            title: "{{ trans('admin::app.components.activities.index.empty-placeholders.planned.title') }}",
                            description: "{{ trans('admin::app.components.activities.index.empty-placeholders.planned.description') }}",
                        },
                        
                        note: {
                            image: "{{ admin_vite()->asset('images/empty-placeholders/notes.svg') }}",
                            title: "{{ trans('admin::app.components.activities.index.empty-placeholders.notes.title') }}",
                            description: "{{ trans('admin::app.components.activities.index.empty-placeholders.notes.description') }}",
                        },
                        
                        call: {
                            image: "{{ admin_vite()->asset('images/empty-placeholders/calls.svg') }}",
                            title: "{{ trans('admin::app.components.activities.index.empty-placeholders.calls.title') }}",
                            description: "{{ trans('admin::app.components.activities.index.empty-placeholders.calls.description') }}",
                        },
                        
                        meeting: {
                            image: "{{ admin_vite()->asset('images/empty-placeholders/meetings.svg') }}",
                            title: "{{ trans('admin::app.components.activities.index.empty-placeholders.meetings.title') }}",
                            description: "{{ trans('admin::app.components.activities.index.empty-placeholders.meetings.description') }}",
                        },
                        
                        lunch: {
                            image: "{{ admin_vite()->asset('images/empty-placeholders/activities.svg') }}",
                            title: "{{ trans('admin::app.components.activities.index.empty-placeholders.lunches.title') }}",
                            description: "{{ trans('admin::app.components.activities.index.empty-placeholders.lunches.description') }}",
                        },
                        
                        file: {
                            image: "{{ admin_vite()->asset('images/empty-placeholders/files.svg') }}",
                            title: "{{ trans('admin::app.components.activities.index.empty-placeholders.files.title') }}",
                            description: "{{ trans('admin::app.components.activities.index.empty-placeholders.files.description') }}",
                        },
                        
                        email: {
                            image: "{{ admin_vite()->asset('images/empty-placeholders/emails.svg') }}",
                            title: "{{ trans('admin::app.components.activities.index.empty-placeholders.emails.title') }}",
                            description: "{{ trans('admin::app.components.activities.index.empty-placeholders.emails.description') }}",
                        },
                    },
                }
            },

            computed: {
                filteredActivities() {
                    if (this.selectedType == 'all') {
                        return this.activities;
                    } else if (this.selectedType == 'planned') {
                        return this.activities.filter(activity => ! activity.is_done);
                    }

                    return this.activities.filter(activity => activity.type == this.selectedType);
                }
            },

            mounted() {
                this.get();

                if (this.extraTypes?.length) {
                    this.extraTypes.forEach(type => {
                        this.types.push(type);
                    });
                }
            },

            methods: {
                get() {
                    this.isLoading = true;

                    this.$axios.get(this.endpoint)
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

                unlinkEmail: function(activity) {
                    let self = this;
                    
                    this.$emitter.emit('open-confirm-modal', {
                        agree: () => {
                            let emailId = activity.parent_id ?? activity.id;

                            this.$axios.delete(this.emailDetachEndpoint, {
                                    data: {
                                        email_id: emailId,
                                    }
                                })
                                .then (response => {
                                    let relatedActivities = self.activities.filter(activity => activity.parent_id == emailId || activity.id == emailId);

                                    relatedActivities.forEach(activity => {
                                        const index = self.activities.findIndex(a => a === activity);
                                        
                                        if (index !== -1) {
                                            self.activities.splice(index, 1);
                                        }
                                    });

                                    self.$emitter.emit('add-flash', { type: 'success', message: response.data.message });
                                })
                                .catch (error => {
                                    self.$emitter.emit('add-flash', { type: 'success', message: response.data.message });
                                });
                        }
                    });
                },
            }
        });
    </script>
@endPushOnce