{!! view_render_event('admin.leads.view.tags.before', ['lead' => $lead]) !!}

<v-lead-tags></v-lead-tags>

{!! view_render_event('admin.leads.view.tags.after', ['lead' => $lead]) !!}

@pushOnce('scripts')
    <script type="text/x-template" id="v-lead-tags-template">
        <div class="flex items-center gap-1">
            <!-- Tags -->
            <span
                class="rounded-md bg-rose-100 px-3 py-1.5 text-xs font-medium"
                :style="'background-color: ' + (tag.color ? tag.color : '#546E7A')"
                v-for="(tag, index) in tags"
            >
                @{{ tag.name }}
            </span>

            <!-- Add Button -->
            <x-admin::dropdown ::close-on-click="false">
                <x-slot:toggle>
                    <button class="icon-add rounded-md p-1 text-xl transition-all hover:bg-gray-200"></button>
                </x-slot>

                <x-slot:content class="!p-0">
                    <!-- Dropdown Container !-->
                    <div class="flex flex-col gap-2">
                        <!-- Search Input -->
                        <div class="flex flex-col gap-1 px-4 py-2">
                            <label class="font-semibold text-gray-600">
                                @lang('admin::app.leads.view.tags.title')
                            </label>

                            <!-- Search Button -->
                            <div class="relative">
                                <div class="relative rounded border border-gray-200 p-2 hover:border-gray-400 focus:border-gray-400" role="button">
                                    <input
                                        type="text"
                                        class="w-full cursor-pointer pr-6"
                                        placeholder="@lang('admin::app.leads.view.tags.placeholder')"
                                        v-model.lazy="searchTerm"
                                        v-debounce="500"
                                    />

                                    <template v-if="! isSearching">
                                        <span
                                            class="absolute right-1.5 top-1.5 text-2xl"
                                            :class="[searchTerm.length >= 2 ? 'icon-up-arrow' : 'icon-down-arrow']"
                                        ></span>
                                    </template>

                                    <template v-else>
                                        <x-admin::spinner class="absolute right-2 top-2" />
                                    </template>
                                </div>

                                <!-- Search Tags Dropdown -->
                                <div
                                    class="absolute z-10 w-full rounded bg-white shadow-[0px_10px_20px_0px_#0000001F] dark:bg-gray-900"
                                    v-if="searchTerm.length >= 2"
                                >
                                    <ul class="p-2">
                                        <li
                                            class="cursor-pointer rounded-sm px-5 py-2 text-sm text-gray-800 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-950"
                                            v-for="tag in searchedTags"
                                            @click="add(tag)"
                                        >
                                            @{{ tag.name }}
                                        </li>

                                        <template v-if="! searchedTags.length && ! isSearching">
                                            <li
                                                class="cursor-pointer rounded-sm bg-gray-100 px-5 py-2 text-sm text-gray-800 dark:bg-gray-950"
                                                @click="create"
                                            >
                                                @{{ "@lang('admin::app.leads.view.tags.add-tag', ['term' => 'replaceTerm'])".replace('replaceTerm', searchTerm) }}
                                            </li>
                                        </template>
                                    </ul>
                                </div>
                            </div>
                        </div>

                        <!-- Tags -->
                        <div class="flex flex-col gap-2 px-4 py-1.5">
                            <label class="text-gray-600">
                                @lang('admin::app.leads.view.tags.added-tags')
                            </label>
                            
                            <!-- Added Tags List -->
                            <ul class="flex flex-col">
                                <li
                                    class="group flex items-center justify-between rounded-sm p-2 text-sm text-gray-800"
                                    v-for="tag in tags"
                                >
                                    <!-- Name -->
                                    <span
                                        class="rounded-md bg-rose-100 px-3 py-1.5 text-xs font-medium"
                                        :style="'background-color: ' + (tag.color ? tag.color : '#546E7A')"
                                    >
                                        @{{ tag.name }}
                                    </span>

                                    <!-- Action -->
                                    <div class="relative flex gap-1">
                                        <x-admin::dropdown>
                                            <x-slot:toggle>
                                                <div class="relative hidden cursor-pointer items-center gap-1 rounded border border-gray-200 px-2 py-0.5 hover:border-gray-400 focus:border-gray-400 group-hover:flex">
                                                    <span
                                                        class="h-4 w-4 rounded-full"
                                                        :style="'background-color: ' + (tag.color ? tag.color : '#546E7A')"
                                                    >
                                                    </span>

                                                    <span class="icon-down-arrow text-xl"></span>
                                                </div>
                                            </x-slot>

                                            <x-slot:menu class="top-5">
                                                <x-admin::dropdown.menu.item
                                                    class="top-5"
                                                    v-for="color in backgroundColors"
                                                >
                                                    <span
                                                        class="flex h-4 w-4 rounded-full"
                                                        :style="'background-color: ' + color"
                                                    >
                                                    </span>
                                                </x-admin::dropdown.menu.item>
                                            </x-slot>
                                        </x-admin::dropdown>
                                        
                                        <span
                                            class="icon-cross-large ml-2 hidden cursor-pointer rounded-md p-1 text-xl text-gray-600 transition-all hover:bg-gray-200 group-hover:flex"
                                            @click="remove(tag)"
                                        ></span>
                                    </div>
                                </li>
                            </ul>
                        </div>
                    </div>
                </x-slot>
            </x-admin::dropdown>
        </div>
    </script>

    <script type="module">
        app.component('v-lead-tags', {
            template: '#v-lead-tags-template',

            data: function () {
                return {
                    searchTerm: '',

                    isStoring: false,

                    isSearching: false,

                    tags: @json($lead->tags),

                    searchedTags: [],

                    backgroundColors: [
                        '#FEE2E2',
                        '#FFEDD5',
                        '#FEF3C7',
                        '#FEF9C3',
                        '#ECFCCB',
                        '#F0FDF4',
                        '#DCFCE7',
                        '#fbcfe8',
                    ],
                }
            },

            watch: {
                searchTerm(newVal, oldVal) {
                    this.search();
                },
            },

            methods: {
                openModal(type) {
                    this.$refs.mailActivityModal.open();
                },

                search() {
                    if (this.searchTerm.length <= 1) {
                        this.searchedTags = [];

                        this.isSearching = false;

                        return;
                    }

                    this.isSearching = true;
                    
                    let self = this;
                    
                    this.$axios.get("{{ route('admin.settings.tags.search') }}", {
                            params: {
                                query: this.searchTerm
                            }
                        })
                        .then (function(response) {
                            self.tags.forEach(function(addedTag) {
                                response.data = response.data.filter(function(tag) {
                                    return tag.id !== addedTag.id;
                                });
                            });

                            self.searchedTags = response.data;

                            self.isSearching = false;
                        })
                        .catch (function (error) {
                            self.isSearching = false;
                        });
                },

                create() {
                    this.isStoring = true;

                    var self = this;
                    console.log(1111)

                    this.$axios.post("{{ route('admin.settings.tags.store') }}", {
                        name: this.searchTerm,
                        color: this.backgroundColors[Math.floor(Math.random() * this.backgroundColors.length)]
                    })
                        .then(response => {
                            self.add(response.data.data);
                        })
                        .catch(error => {
                            self.$emitter.emit('add-flash', { type: 'error', message: error.response.data.message });

                            self.isStoring = false;
                        });
                },

                add(params) {
                    this.isStoring = true;

                    var self = this;

                    this.$axios.post("{{ route('admin.leads.tags.store', $lead->id) }}", params)
                        .then(response => {
                            self.searchedTags = [];

                            self.searchTerm = '';

                            self.isStoring = false;

                            self.tags.push(params);

                            self.$emitter.emit('add-flash', { type: 'success', message: response.data.message });
                        })
                        .catch(error => {
                            self.$emitter.emit('add-flash', { type: 'error', message: error.response.data.message });

                            self.isStoring = false;
                        });
                },

                remove(tag) {
                },
            },
        });
    </script>
@endPushOnce