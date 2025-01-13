@if (isset($attribute))
    @php
        $lookUpEntityData = app('Webkul\Attribute\Repositories\AttributeRepository')->getLookUpEntity($attribute->lookup_type, old($attribute->code) ?: $value);
    @endphp

    <v-lookup-component
        :attribute="{{ json_encode($attribute) }}"
        :validations="'{{ $validations }}'"
        :value="{{ json_encode($lookUpEntityData)}}"
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
        <div class="relative">
            <div
                class="relative inline-block w-full"
                @click="toggle"
            >
                <!-- Input Container -->
                <div class="relative flex items-center justify-between rounded border border-gray-200 p-2 hover:border-gray-400 focus:border-gray-400 dark:border-gray-800 dark:text-gray-300">
                    <!-- Selected Item or Placeholder Text -->
                    @{{ selectedItem ? selectedItem : "@lang('admin::app.components.attributes.lookup.click-to-add')" }}
                    
                    <!-- Icons Container -->
                    <div class="flex items-center gap-2">
                        <!-- Close Icon -->
                        <i 
                            v-if="entityId && ! isSearching"
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
            <input
                type="hidden"
                :name="attribute['code']"
                v-model="entityId"
            />
            
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
                    <template v-for="result in searchedResults"> 
                        <li
                            class="flex cursor-pointer gap-2 p-2 transition-colors hover:bg-blue-100 dark:text-gray-300 dark:hover:bg-gray-900"
                            @click="handleResult(result)"
                        >
                            <!-- Entity Name -->
                            <span>@{{ result.name }}</span>
                        </li>                       
                    </template>
                
                    <li 
                        v-if="searchedResults.length === 0"
                        class="px-4 py-2 text-center text-gray-500"
                    >
                        @lang('admin::app.components.attributes.lookup.no-result-found')
                    </li>
                </ul>
            </div>
        </div>
    </script>

    <script type="module">
        app.component('v-lookup-component', {
            template: '#v-lookup-component-template',

            props: ['validations', 'attribute', 'value'],

            data() {
                return {
                    showPopup: false,

                    searchTerm: '',

                    searchedResults: [],

                    selectedItem: null,

                    entityId: null,

                    searchRoute: `{{ route('admin.settings.attributes.lookup') }}/${this.attribute.lookup_type}`,

                    isSearching: false,
                };
            },

            mounted() {
                if (this.value) {
                    this.selectedItem = this.value;
                }
            },

            watch: { 
                searchTerm(newVal, oldVal) {
                    this.search();
                },
            },

            mounted() {
                if (this.value) {
                    this.getLookUpEntity();
                }

                window.addEventListener('click', this.handleFocusOut);
            },

            beforeDestroy() {
                window.removeEventListener('click', this.handleFocusOut);
            },

            methods: {
                toggle() {
                    this.showPopup = ! this.showPopup;

                    if (this.showPopup) {
                        this.$nextTick(() => this.$refs.searchInput.focus());
                    }
                },

                search() {
                    if (this.searchTerm.length <= 2) {
                        this.searchedResults = [];

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
                    this.$axios.get(this.searchRoute, {
                            params: { query: this.value?.name ?? ""}
                        })
                        .then (response => {
                            const [result] = response.data;

                            this.entityId = result.id;

                            this.selectedItem = result.name;
                        })
                        .catch (error => {});
                },

                handleResult(result) {
                    this.showPopup = ! this.showPopup;
                    
                    this.entityId = result.id;

                    this.selectedItem = result.name;

                    this.searchTerm = "";

                    this.searchedResults = [];

                    this.$emit('lookup-added', result);
                },

                handleFocusOut(e) {
                    if (! this.$el.contains(e.target)) {
                        this.showPopup = false;
                    }
                },

                remove() {
                    this.entityId = null;

                    this.selectedItem = null;

                    this.searchTerm = '';

                    this.searchedResults = [];

                    this.$emit('lookup-removed');
                },
            },
        });
    </script>
@endPushOnce
