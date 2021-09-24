@if (isset($attribute))
    @php
        $lookUpEntityData = app('Webkul\Attribute\Repositories\AttributeRepository')
            ->getLookUpEntity($attribute->lookup_type, old($attribute->code) ?: $value);
    @endphp

    <multi-lookup-component :attribute='@json($attribute)' :validations="'{{$validations}}'" :data='@json($lookUpEntityData)'></multi-lookup-component>
@endif

@once
    @push('scripts')

        <script type="text/x-template" id="multi-lookup-component-template">
            <div class="lookup-control">
                <div class="form-group" style="margin-bottom: 0">
                    <input
                        type="hidden"
                        :name="attribute['code']"
                        v-validate="validations"
                        :data-vv-as="attribute['name']"
                        v-if="! selected_results.length"
                    />

                    <input
                        type="text"
                        class="control"
                        placeholder="{{ __('admin::app.common.start-typing') }}"
                        autocomplete="off"
                        v-model="search_term"
                        v-on:keyup="search"
                    >

                    <div class="lookup-results" v-if="search_term.length">
                        <ul>
                            <li v-for='(result, index) in results' @click="addLookUp(result)">
                                <span>@{{ result.name }}</span>
                            </li>

                            <li v-if='! results.length && search_term.length && ! is_searching'>
                                <span>{{ __('admin::app.common.no-result-found') }}</span>
                            </li>
                        </ul>
                    </div>

                    <i class="icon loader-active-icon" v-if="is_searching"></i>
                </div>

                <div class="lookup-selected-options">
                    <span class="badge badge-sm badge-pill badge-secondary-outline" v-for='(result, index) in selected_results'>
                        <input type="hidden" :name="attribute['code']" :value="result.id"/>
                        @{{ result.name }}
                        <i class="icon close-icon"  @click="removeLookUp(result)"></i>
                    </span>
                </div>
            </div>
        </script>

        <script>
            Vue.component('multi-lookup-component', {

                template: '#multi-lookup-component-template',

                props: ['validations', 'attribute', 'searchRoute', 'data'],

                inject: ['$validator'],

                data: function () {
                    return {
                        search_term: '',

                        is_searching: false,

                        selected_results: this.data || [],

                        results: [],

                        search_route: this.searchRoute ?? `{{ route('admin.settings.attributes.lookup') }}/${this.attribute.lookup_type}`,
                    }
                },

                methods: {
                    search: debounce(function () {
                        this.is_searching = true;

                        if (this.search_term.length < 2) {
                            this.results = [];

                            this.is_searching = false;

                            return;
                        }

                        this.$http.get(this.search_route, {params: {query: this.search_term}})
                            .then (response => {
                                this.results = response.data;

                                this.is_searching = false;
                            })
                            .catch (error => {
                                this.is_searching = false;
                            })
                    }, 500),

                    addLookUp: function(result) {
                        this.search_term = '';

                        this.results = [];

                        this.selected_results.push(result);

                        eventBus.$emit('multi-lookup-added', result);
                    },

                    removeLookUp: function(result) {
                        const index = this.selected_results.indexOf(result);

                        Vue.delete(this.selected_results, index);
                    }
                }
            });
        </script>
        
    @endpush
@endonce