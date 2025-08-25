@if (isset($attribute))
    @php
        $lookUpEntityData = app('Webkul\Attribute\Repositories\AttributeRepository')->getLookUpEntity($attribute->lookup_type, old($attribute->code) ?: $value);
    @endphp

    <v-lookup-component
        :attribute="{{ json_encode($attribute) }}"
        :validations="'{{ $validations }}'"
        :value="{{ json_encode($lookUpEntityData)}}"
        can-add-new="{{ $canAddNew ?? false }}"
        @lookup-added="handleLookupAdded"
        @lookup-removed="handleLookupRemoved"
    >
        <div class="relative inline-block w-full">
            <!-- Input Container -->
            <div class="relative flex items-center justify-between rounded border border-gray-200 p-2 hover:border-gray-400 focus:border-gray-400 dark:border-gray-800 dark:text-gray-300">
                @lang('admin::app.components.attributes.lookup.click-to-add')

                <!-- Icons Container -->
                <div class="flex items-center gap-2">
                    <!-- Arrow Icon -->
                    <i class="icon-down-arrow text-2xl text-gray-600"></i>
                </div>
            </div>
        </div>
    </v-lookup-component>
@endif

@pushOnce('scripts')
    <script
        type="text/x-template"
        id="v-lookup-component-template"
    >
        <div
            class="relative"
            ref="lookup"
        >
            <div
                class="relative inline-block w-full"
                @click="toggle"
            >
                <!-- Input Container -->
                <div
                    class="relative flex items-center justify-between rounded border border-gray-200 p-2 hover:border-gray-400 focus:border-gray-400 dark:border-gray-800 dark:text-gray-300"
                    :class="{
                        'bg-gray-50': isDisabled,
                    }"
                >
                    <!-- Selected Item or Placeholder Text -->
                    <span
                        class="overflow-hidden text-ellipsis"
                        :title="selectedItem?.name"
                    >
                        @{{ selectedItem?.name !== "" ? selectedItem?.name : "@lang('admin::app.components.attributes.lookup.click-to-add')" }}
                    </span>

                    <!-- Icons Container -->
                    <div class="flex items-center gap-2">
                        <!-- Close Icon -->
                        <i
                            v-if="
                                ! isDisabled
                                && (
                                    selectedItem?.name
                                    && ! isSearching
                                )"
                            class="icon-cross-large cursor-pointer text-2xl text-gray-600"
                            @click="remove"
                        ></i>

                        <!-- Arrow Icon -->
                        <i
                            class="text-2xl text-gray-600"
                            :class="showPopup ? 'icon-up-arrow' : 'icon-down-arrow'"
                        ></i>
                    </div>
                </div>
            </div>

            <!-- Hidden Input Entity Value -->
            <x-admin::form.control-group.control
                type="hidden"
                ::name="attribute['code']"
                v-model="selectedItem.id"
                ::rules="validations"
                ::label="attribute['name']"
            />

            <!-- Popup Box -->
            <div
                v-if="showPopup"
                class="absolute top-full z-10 mt-1 flex w-full origin-top transform flex-col gap-2 rounded-lg border border-gray-200 bg-white p-2 shadow-lg transition-transform dark:border-gray-900 dark:bg-gray-800"
            >
                <!-- Search Bar -->
                <div class="relative flex items-center">
                    <!-- Input Box -->
                    <input
                        type="text"
                        v-model.lazy="searchTerm"
                        v-debounce="500"
                        class="w-full rounded border border-gray-200 px-2.5 py-2 text-sm font-normal text-gray-800 transition-all hover:border-gray-400 focus:border-gray-400 dark:border-gray-800 dark:bg-gray-900 dark:text-gray-300 dark:hover:border-gray-400 dark:focus:border-gray-400"
                        placeholder="@lang('admin::app.components.attributes.lookup.search')"
                        ref="searchInput"
                        @keyup="search"
                    />

                    <!-- Search Icon (absolute positioned) -->
                    <span class="absolute flex items-center ltr:right-2 rtl:left-2">
                        <!-- Loader (optional, based on condition) -->
                        <div
                            class="relative"
                            v-if="isSearching"
                        >
                            <x-admin::spinner />
                        </div>
                    </span>
                </div>

                <!-- Results List -->
                <ul class="max-h-40 divide-y divide-gray-100 overflow-y-auto">
                    <li
                        v-for="item in filteredResults"
                        :key="item.id"
                        class="flex cursor-pointer gap-2 p-2 transition-colors hover:bg-blue-100 dark:text-gray-300 dark:hover:bg-gray-900"
                        @click="handleResult(item)"
                    >
                        <!-- Entity Name -->
                        <span>@{{ item.name }}</span>
                    </li>

                    <template v-if="filteredResults.length === 0">
                        <li class="px-4 py-2 text-center text-gray-500">
                            @lang('admin::app.components.attributes.lookup.no-result-found')
                        </li>

                        <li
                            v-if="searchTerm.length > 2 && canAddNew"
                            @click="handleResult({ id: '', name: searchTerm })"
                            class="cursor-pointer border-t border-gray-800 px-4 py-2 text-gray-500 hover:bg-brandColor hover:text-white dark:border-gray-300 dark:text-gray-400 dark:hover:bg-gray-900 dark:hover:text-white"
                        >
                            <i class="icon-add text-md"></i>

                            @lang('admin::app.components.lookup.add-as-new')
                        </li>
                    </template>
                </ul>
            </div>
        </div>
    </script>

    <script type="module">
        app.component('v-lookup-component', {
            template: '#v-lookup-component-template',

            props: ['validations', 'isDisabled', 'attribute', 'value', 'canAddNew'],

            emits: ['lookup-added', 'lookup-removed'],

            data() {
                return {
                    showPopup: false,

                    searchTerm: '',

                    searchedResults: [],

                    selectedItem: {
                        id: '',
                        name: ''
                    },

                    searchRoute: `{{ route('admin.settings.attributes.lookup') }}/${this.attribute.lookup_type}`,

                    lookupEntityRoute: `{{ route('admin.settings.attributes.lookup_entity') }}/${this.attribute.lookup_type}`,

                    isSearching: false,
                };
            },

            mounted() {
                if (this.value) {
                    this.getLookUpEntity();
                }

                window.addEventListener('click', this.handleFocusOut);
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
                filteredResults() {
                    return this.searchedResults.filter(item =>
                        item.name.toLowerCase().includes(this.searchTerm.toLowerCase())
                    );
                }
            },

            methods: {
                toggle() {
                    if (this.isDisabled) {
                        this.showPopup = false;

                        return;
                    }

                    this.showPopup = ! this.showPopup;

                    if (this.showPopup) {
                        this.$nextTick(() => this.$refs.searchInput.focus());
                    }
                },

                search() {
                    if (this.searchTerm.length <= 2) {
                        this.searchedResults = [];

                        this.isSearching = false;

                        return;
                    }

                    this.isSearching = true;

                    this.$axios.get(this.searchRoute, {
                            params: { query: this.searchTerm }
                        })
                        .then (response => {
                            this.searchedResults = response.data;
                        })
                        .catch (error => {})
                        .finally(() => this.isSearching = false);
                },

                getLookUpEntity() {
                    this.$axios.get(this.lookupEntityRoute, {
                            params: { query: this.value?.id ?? ""}
                        })
                        .then (response => {
                            this.selectedItem = Object.keys(response.data).length
                                ? response.data
                                : {
                                    id: '',
                                    name: ''
                                };
                        })
                        .catch (error => {});
                },

                handleResult(result) {
                    this.showPopup = false;

                    this.selectedItem = result;

                    this.searchTerm = '';

                    this.$emit('lookup-added', this.selectedItem);
                },

                handleFocusOut(e) {
                    const lookup = this.$refs.lookup;

                    if (
                        lookup &&
                        ! lookup.contains(event.target)
                    ) {
                        this.showPopup = false;
                    }
                },

                remove() {
                    this.selectedItem = {
                        id: '',
                        name: ''
                    };

                    this.$emit('lookup-removed', this.selectedItem);
                },
            },
        });
    </script>
@endPushOnce
