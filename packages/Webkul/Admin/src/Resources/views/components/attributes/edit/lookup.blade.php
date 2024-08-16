@if (isset($attribute))
    @php
        $lookUpEntityData = app('Webkul\Attribute\Repositories\AttributeRepository')->getLookUpEntity($attribute->lookup_type, old($attribute->code) ?: $value);
    @endphp

    <v-lookup-component
        :attribute="{{ json_encode($attribute) }}"
        :validations="'{{ $validations }}'"
        :value="{{ json_encode($lookUpEntityData)}}"
    >
        <x-admin::form.control-group.control
            type="text"
            placeholder="@lang('admin::app.components.attributes.lookup.click-to-add')"
        />
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
                <div class="relative h-10 rounded border p-2 hover:border-gray-400 focus:border-gray-400 dark:border-gray-800 dark:bg-gray-900 dark:text-gray-300 dark:hover:border-gray-400 dark:focus:border-gray-400">
                    @{{ selectedItem ? selectedItem : "@lang('Start Typing...')" }}
                </div>
                
                <!-- Arrow down icon -->
                <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-3">
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

            <!-- Hidden Input Entity Value -->
            <input
                type="hidden"
                :name="attribute['code']"
                v-model="entityId"
            />
            
            <div 
                v-if="showPopup" 
                class="absolute top-full z-10 mt-1 w-full origin-top transform rounded-lg border border-gray-200 bg-white p-2 shadow-lg transition-transform dark:border-gray-900 dark:bg-gray-800"
            >
                <!-- Search Bar -->
                <div class="relative">
                    <!-- Input Box -->
                    <input
                        type="text"
                        v-model.lazy="searchTerm"
                        v-debounce="500"
                        class="!mb-2 w-full rounded border border-gray-200 px-2.5 py-2 text-sm font-normal text-gray-800 transition-all hover:border-gray-400 focus:border-gray-400 dark:border-gray-800 dark:bg-gray-900 dark:text-gray-300 dark:hover:border-gray-400 dark:focus:border-gray-400" 
                        placeholder="@lang('Search...')"
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


            watch: { 
                value(newValue, oldValue) {
                    if (
                        JSON.stringify(newValue) 
                        !== JSON.stringify(oldValue)
                    ) {
                        this.searchTerm = newValue ? newValue['name'] : '';

                        this.entityId = newValue ? newValue['id'] : '';
                    }
                }
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

                search(event) {
                    const searchTerm = event.target.value;

                    if (searchTerm.length <= 2) {
                        this.searchedResults = [];

                        return;
                    }

                    this.isSearching = true;

                    this.$axios.get(this.searchRoute, {
                            params: { query: searchTerm }
                        })
                        .then (response => {
                            this.searchedResults = response.data;
                        })
                        .catch (error => {})
                        .finally(() => this.isSearching = false);
                },

                getLookUpEntity() {
                    this.$axios.get(this.searchRoute, {
                            params: { query: this.value.name }
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
            }
        });
    </script>
@endPushOnce
