@props([
    'attachEndpoint',
    'detachEndpoint',
    'addedTags' => [],
])

<v-tags
    attach-endpoint="{{ $attachEndpoint }}"
    detach-endpoint="{{ $detachEndpoint }}"
    :added-tags='@json($addedTags)'
>
    <x-admin::shimmer.tags count="3" />
</v-tags>

@pushOnce('scripts')
    <script type="text/x-template" id="v-tags-template">
        <div class="flex flex-wrap items-center gap-1">
            <!-- Tags -->
            <span
                class="flex items-center gap-1 break-all rounded-md bg-rose-100 px-3 py-1.5 text-xs font-medium"
                :style="{
                    'background-color': tag.color,
                    'color': backgroundColors.find(color => color.background === tag.color)?.text
                }"
                v-for="(tag, index) in tags"
                v-safe-html="tag.name"
            >
            </span>

            <!-- Add Button -->
            <x-admin::dropdown
                ::close-on-click="false"
                position="bottom-{{ in_array(app()->getLocale(), ['fa', 'ar']) ? 'right' : 'left' }}"
            >
                <x-slot:toggle>
                    <button class="icon-settings-tag rounded-md p-1 text-xl transition-all hover:bg-gray-200 dark:hover:bg-gray-950"></button>
                </x-slot>

                <x-slot:content class="!p-0">
                    <!-- Dropdown Container !-->
                    <div class="flex flex-col gap-2">
                        <!-- Search Input -->
                        <div class="flex flex-col gap-1 px-4 py-2">
                            <label class="font-semibold text-gray-600 dark:text-gray-300">
                                @lang('admin::app.components.tags.index.title')
                            </label>

                            <!-- Search Button -->
                            <div class="relative">
                                <div class="relative rounded border border-gray-200 p-2 hover:border-gray-400 focus:border-gray-400 dark:border-gray-800 dark:hover:border-gray-400 dark:focus:border-gray-400" role="button">
                                    <input
                                        type="text"
                                        class="w-full cursor-pointer pr-6 dark:bg-gray-900 dark:text-gray-300"
                                        placeholder="@lang('admin::app.components.tags.index.placeholder')"
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
                                    class="absolute z-10 w-full rounded bg-white shadow-[0px_10px_20px_0px_#0000001F] dark:bg-gray-800"
                                    v-if="searchTerm.length >= 2"
                                >
                                    <ul class="p-2">
                                        <li
                                            class="cursor-pointer break-all rounded-sm px-5 py-2 text-sm text-gray-800 hover:bg-gray-100 dark:text-white dark:hover:bg-gray-950"
                                            v-for="tag in searchedTags"
                                            @click="attachToEntity(tag)"
                                        >
                                            @{{ tag.name }}
                                        </li>

                                        @if (bouncer()->hasPermission('settings.other_settings.tags.create'))
                                            <template v-if="! searchedTags.length && ! isSearching">
                                                <li
                                                    class="cursor-pointer rounded-sm bg-gray-100 px-5 py-2 text-sm text-gray-800 dark:bg-gray-950 dark:text-white"
                                                    @click="create"
                                                >
                                                    @{{ `@lang('admin::app.components.tags.index.add-tag', ['term' => 'replaceTerm'])`.replace('replaceTerm', searchTerm) }}
                                                </li>
                                            </template>
                                        @endif
                                    </ul>
                                </div>
                            </div>
                        </div>

                        <!-- Tags -->
                        <div
                            class="flex flex-col gap-2 px-4 py-1.5"
                            v-if="tags.length"
                        >
                            <label class="text-gray-600 dark:text-gray-300">
                                @lang('admin::app.components.tags.index.added-tags')
                            </label>

                            <!-- Added Tags List -->
                            <ul class="flex flex-col">
                                <template v-for="tag in tags">
                                    <li
                                        class="flex items-center justify-between gap-2.5 rounded-sm p-2 text-sm text-gray-800 dark:text-white"
                                        v-if="tag.id"
                                    >
                                        <!-- Name -->
                                        <span
                                            class="break-all rounded-md bg-rose-100 px-3 py-1.5 text-xs font-medium"
                                            :style="{
                                                'background-color': tag.color,
                                                'color': backgroundColors.find(color => color.background === tag.color)?.text
                                            }"
                                        >
                                            @{{ tag.name }}
                                        </span>

                                        <!-- Action -->
                                        <div class="flex items-center gap-1">
                                            @if (bouncer()->hasPermission('settings.other_settings.tags.edit'))
                                                <x-admin::dropdown position="bottom-right">
                                                    <x-slot:toggle>
                                                        <button class="flex cursor-pointer items-center gap-1 rounded border border-gray-200 px-2 py-0.5 transition-all hover:border-gray-400 focus:border-gray-400 dark:border-gray-800 dark:hover:border-gray-400 dark:focus:border-gray-400">
                                                            <span
                                                                class="h-4 w-4 break-all rounded-full"
                                                                :style="'background-color: ' + (tag.color ? tag.color : '#546E7A')"
                                                            >
                                                            </span>

                                                            <span class="icon-down-arrow text-xl"></span>
                                                        </button>
                                                    </x-slot>

                                                    <x-slot:menu class="!top-7 !p-0">
                                                        <x-admin::dropdown.menu.item
                                                            class="top-5 flex gap-2"
                                                            ::class="{ 'bg-gray-100 dark:bg-gray-950': tag.color === color.background }"
                                                            v-for="color in backgroundColors"
                                                            @click="update(tag, color)"
                                                        >
                                                            <span
                                                                class="flex h-4 w-4 break-all rounded-full"
                                                                :style="'background-color: ' + color.background"
                                                            >
                                                            </span>

                                                            @{{ color.label }}
                                                        </x-admin::dropdown.menu.item>
                                                    </x-slot>
                                                </x-admin::dropdown>
                                            @endif

                                            @if (bouncer()->hasPermission('settings.other_settings.tags.delete'))
                                                <div class="flex items-center">
                                                    <span
                                                        class="icon-cross-large flex cursor-pointer rounded-md p-1 text-xl text-gray-600 transition-all hover:bg-gray-200 dark:text-gray-300 dark:hover:bg-gray-800"
                                                        v-show="! isRemoving[tag.id]"
                                                        @click="detachFromEntity(tag)"
                                                    ></span>

                                                    <span
                                                        class="p-1"
                                                        v-show="isRemoving[tag.id]"
                                                    >
                                                        <x-admin::spinner />
                                                    </span>
                                                </div>
                                            @endif
                                        </div>
                                    </li>
                                </template>
                            </ul>
                        </div>
                    </div>
                </x-slot>
            </x-admin::dropdown>
        </div>
    </script>

    <script type="module">
        app.component('v-tags', {
            template: '#v-tags-template',

            props: {
                attachEndpoint: {
                    type: String,
                    default: '',
                },

                detachEndpoint: {
                    type: String,
                    default: '',
                },

                addedTags: {
                    type: Array,
                    default: () => [],
                },
            },

            data: function () {
                return {
                    searchTerm: '',

                    isStoring: false,

                    isSearching: false,

                    isRemoving: {},

                    tags: [],

                    searchedTags: [],

                    backgroundColors: [
                        {
                            label: "@lang('admin::app.components.tags.index.aquarelle-red')",
                            text: '#DC2626',
                            background: '#FEE2E2',
                        }, {
                            label: "@lang('admin::app.components.tags.index.crushed-cashew')",
                            text: '#EA580C',
                            background: '#FFEDD5',
                        }, {
                            label: "@lang('admin::app.components.tags.index.beeswax')",
                            text: '#D97706',
                            background: '#FEF3C7',
                        }, {
                            label: "@lang('admin::app.components.tags.index.lemon-chiffon')",
                            text: '#CA8A04',
                            background: '#FEF9C3',
                        }, {
                            label: "@lang('admin::app.components.tags.index.snow-flurry')",
                            text: '#65A30D',
                            background: '#ECFCCB',
                        }, {
                            label: "@lang('admin::app.components.tags.index.honeydew')",
                            text: '#16A34A',
                            background: '#DCFCE7',
                        },
                    ],
                }
            },

            watch: {
                searchTerm(newVal, oldVal) {
                    this.search();
                },
            },

            mounted() {
                this.tags = this.addedTags;
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
                                search: 'name:' + this.searchTerm,
                                searchFields: 'name:like',
                            }
                        })
                        .then (function(response) {
                            self.tags.forEach(function(addedTag) {
                                response.data.data = response.data.data.filter(function(tag) {
                                    return tag.id !== addedTag.id;
                                });
                            });

                            self.searchedTags = response.data.data;

                            self.isSearching = false;
                        })
                        .catch (function (error) {
                            self.isSearching = false;
                        });
                },

                create() {
                    this.isStoring = true;

                    var self = this;

                    this.$axios.post("{{ route('admin.settings.tags.store') }}", {
                        name: this.searchTerm,
                        color: this.backgroundColors[Math.floor(Math.random() * this.backgroundColors.length)].background,
                    })
                        .then(response => {
                            self.attachToEntity(response.data.data);
                        })
                        .catch(error => {
                            self.$emitter.emit('add-flash', { type: 'error', message: error.response.data.message });

                            self.isStoring = false;
                        });
                },

                update(tag, color) {
                    var self = this;

                    this.$axios.put("{{ route('admin.settings.tags.update', 'replaceTagId') }}".replace('replaceTagId', tag.id), {
                        name: tag.name,
                        color: color.background,
                    })
                        .then(response => {
                            tag.color = color.background;

                            self.$emitter.emit('add-flash', { type: 'success', message: response.data.message });
                        })
                        .catch(error => {
                            self.$emitter.emit('add-flash', { type: 'error', message: error.response.data.message });
                        });
                },

                attachToEntity(params) {
                    this.isStoring = true;

                    var self = this;

                    this.$axios.post(this.attachEndpoint, {
                        tag_id: params.id,
                    })
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

                detachFromEntity(tag) {
                    var self = this;

                    this.$emitter.emit('open-confirm-modal', {
                        agree: () => {
                            this.isRemoving[tag.id] = true;

                            this.$axios.delete(this.detachEndpoint, {
                                    data: {
                                        tag_id: tag.id,
                                    }
                                })
                                .then(response => {
                                    self.isRemoving[tag.id] = false;

                                    const index = self.tags.indexOf(tag);

                                    self.tags.splice(index, 1);

                                    self.$emitter.emit('add-flash', { type: 'success', message: response.data.message });
                                })
                                .catch(error => {
                                    self.$emitter.emit('add-flash', { type: 'error', message: error.response.data.message });

                                    self.isRemoving[tag.id] = false;
                                });
                        },
                    });
                },
            },
        });
    </script>
@endPushOnce