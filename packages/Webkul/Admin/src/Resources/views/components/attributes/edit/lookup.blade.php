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
            placeholder="@lang('admin::app.common.start-typing')"
        />
    </v-lookup-component>
@endif

@pushOnce('scripts')
    <script
        type="text/x-template"
        id="v-lookup-component-template"
    >
        <div class="relative">
            <x-admin::form.control-group.control
                type="text"
                ::id="attribute['code']"
                ::for="attribute['code']"
                ::name="attribute['code']"
                ::label="attribute['name']"
                placeholder="@lang('admin::app.common.start-typing')"
                v-model="searchTerm"
                @click="searchTerm.length >= 2 ? isDropdownOpen = true : {}"
                @keyup="search"
            />

            <input
                type="hidden"
                :name="attribute['code']"
                v-model="entityId"
            />

            <div
                class="absolute top-10 z-10 w-full rounded-lg border bg-white shadow-[0px_0px_0px_0px_rgba(0,0,0,0.10),0px_1px_3px_0px_rgba(0,0,0,0.10),0px_5px_5px_0px_rgba(0,0,0,0.09),0px_12px_7px_0px_rgba(0,0,0,0.05),0px_22px_9px_0px_rgba(0,0,0,0.01),0px_34px_9px_0px_rgba(0,0,0,0.00)] dark:border-gray-800 dark:bg-gray-900"
                v-if="isDropdownOpen"
            >
                <div class="grid max-h-[400px] overflow-y-auto">
                    <template v-for="result in searchedResults">
                        <span
                            class="cursor-pointer border-b p-4 text-sm font-semibold text-gray-600 last:border-b-0 hover:bg-gray-100 dark:border-gray-800 dark:text-gray-300 dark:hover:bg-gray-950"
                            @click="handleResult(result)"
                        >
                            @{{ result.name }}
                        </span>
                    </template>

                    <div
                        class="p-4 text-sm font-semibold text-gray-600 dark:text-gray-300"
                        v-if="searchedResults.length === 0"
                    >
                        @lang('No result found')
                    </div>
                </div>
            </div>
        </div>
    </script>

    <script type="module">
        app.component('v-lookup-component', {
            template: '#v-lookup-component-template',

            props: ['validations', 'attribute', 'value'],

            data() {
                return {
                    isDropdownOpen: false,

                    searchTerm: '',

                    searchedResults: [],

                    entityId: null,

                    searchRoute: `{{ route('admin.settings.attributes.lookup') }}/${this.attribute.lookup_type}`,
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
                search(event) {
                    const searchTerm = event.target.value;

                    if (searchTerm.length <= 2) {
                        this.searchedResults = [];

                        this.isDropdownOpen = false;

                        return;
                    }

                    this.isDropdownOpen = true;

                    this.$axios.get(this.searchRoute, {
                            params: { query: searchTerm }
                        })
                        .then (response => {
                            this.searchedResults = response.data;
                        })
                        .catch (error => this.isDropdownOpen = false);
                },

                getLookUpEntity() {
                    this.$axios.get(this.searchRoute, {
                            params: { query: this.value.name }
                        })
                        .then (response => {
                            const [result] = response.data;

                            this.entityId = result.id;

                            this.searchTerm = result.name;
                        })
                        .catch (error => {});
                },

                handleResult(result) {
                    this.isDropdownOpen = false;
                    
                    this.entityId = result.id;

                    this.searchTerm = result.name;

                    this.$emit('lookup-added', result);
                },

                handleFocusOut(e) {
                    if (! this.$el.contains(e.target)) {
                        this.isDropdownOpen = false;
                    }
                },
            }
        });
    </script>
@endPushOnce