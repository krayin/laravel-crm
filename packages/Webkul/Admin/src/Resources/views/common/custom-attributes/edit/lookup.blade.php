@if (isset($attribute))
    @php
        $lookUpEntityData = app('Webkul\Attribute\Repositories\AttributeRepository')
            ->getLookUpEntity($attribute->lookup_type, old($attribute->code) ?: $value);
    @endphp

    <lookup-component :attribute='@json($attribute)' :validations="'{{$validations}}'" :data='@json($lookUpEntityData)'></lookup-component>
@endif

@once
    @push('scripts')

        <script type="text/x-template" id="lookup-component-template">
            <div class="lookup-control">
                <div class="form-group" style="margin-bottom: 0">
                    <input
                        type="text"
                        :name="attribute['code']"
                        class="control"
                        :id="attribute['code']"
                        :for="attribute['code']"
                        placeholder="{{ __('admin::app.common.start-typing') }}"
                        autocomplete="off"
                        v-model="search_term"
                        v-validate="validations"
                        :data-vv-as="attribute['name']"
                        v-on:keyup="search"
                    >

                    <input
                        type="hidden"
                        :name="attribute['code']"
                        v-model="entity_id"
                        v-validate="validations"
                        :data-vv-as="attribute['name']"
                    />

                    <div class="lookup-results" v-if="state == ''">
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
            </div>
        </script>

        <script>
            Vue.component('lookup-component', {

                template: '#lookup-component-template',

                props: ['validations', 'attribute', 'searchRoute', 'data'],

                inject: ['$validator'],

                data: function () {
                    return {
                        search_term: this.data ? this.data['name'] : '',

                        entity_id: this.data ? this.data['id'] : '',

                        is_searching: false,

                        state: this.data ? 'old' : '',

                        results: [],

                        search_route: this.searchRoute ?? `{{ route('admin.settings.attributes.lookup') }}/${this.attribute.lookup_type}`,
                    }
                },

                watch: { 
                    data: function(newVal, oldVal) {
                        if (JSON.stringify(newVal) !== JSON.stringify(oldVal)) {
                            this.search_term = newVal ? newVal['name'] : '';

                            this.entity_id = newVal ? newVal['id'] : '';

                            this.state = newVal ? 'old' : '';
                        }
                    }
                },

                methods: {
                    search: debounce(function () {
                        this.state = '';

                        this.entity_id = null;

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
                        this.state = 'old';

                        this.entity_id = result['id'];

                        this.search_term = result['name'];

                        eventBus.$emit('lookup-added', result);
                    },

                    createNew: function() {
                        this.state = 'new';

                        this.entity_id = null;
                    }
                }
            });
        </script>
        
    @endpush
@endonce