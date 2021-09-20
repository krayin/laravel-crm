@if (isset($attribute))
    <tags-component
        :attribute='@json($attribute)'
        :validations="'{{$validations}}'"
        :data='@json(old($attribute->code) ?: $value)'
    ></tags-component>
@endif

@once
    @push('scripts')

        <script type="text/x-template" id="tags-component-template">
            <div class="tags-control">
                <div
                    class="form-group input-group"
                    v-for="(tag, index) in tags"
                    :class="[errors.has(attribute['code'] + '[' + index + '][value]') ? 'has-error' : '']"
                >
                
                    <input
                        type="text"
                        class="control"
                        :for="attribute['code']"
                        autocomplete="off"
                        v-model="search_term"
                        :data-vv-as="attribute['name']"
                        v-on:keyup="search"
                    />

                    <input
                        type="hidden"
                        :name="attribute['code']"
                        v-model="tag_ids"
                        v-validate="validations"
                        :data-vv-as="attribute['name']"
                    />

                    <div class="lookup-results" v-if="state == ''">
                        <ul>
                            <li v-for='(result, index) in results' @click="addTag(result)">
                                <span>@{{ result.name }}</span>
                            </li>

                            <li v-if='! results.length && search_term.length && ! is_searching'>
                                <span>{{ __('admin::app.common.no-result-found') }}</span>
                            </li>
                        </ul>
                    </div>

                    <span class="control-error" v-if="errors.has(attribute['code'] + '[' + index + '][value]')">
                        @{{ errors.first(attribute['code'] + '[' + index + '][value]') }}
                    </span>
                </div>
            </div>
        </script>

        <script>
            Vue.component('tags-component', {

                template: '#tags-component-template',

                props: ['validations', 'attribute', 'searchRoute', 'data'],

                inject: ['$validator'],

                data: function () {
                    return {
                        tags: this.data ? this.data : [],

                        search_term: '',

                        is_searching: false,

                        search_route: this.searchRoute ? this.searchRoute : "{{ route('admin.settings.attributes.lookup') }}" + this.attribute['lookup_type'],
                    }
                },

                computed: {
                    tag_ids: function() {
                        var tags = [];

                        this.tags.forEach(function(tag) {
                            tags.push(tag.id ? tag.id : tag.name);
                        });

                        return tags.join(',');
                    },
                },

                methods: {
                    search: debounce(function () {
                        this.state = '';

                        this.is_searching = true;

                        if (this.search_term.length < 2) {
                            this.results = [];

                            this.is_searching = false;

                            return;
                        }

                        var self = this;
                        
                        this.$http.get(this.search_route, {params: {query: this.search_term}})
                            .then (function(response) {
                                self.results = response.data;

                                self.is_searching = false;
                            })
                            .catch (function (error) {
                                self.is_searching = false;
                            })
                    }, 500),
                    
                    addTag: function(tag) {
                        this.state = 'old';

                        this.search_term = '';

                        this.tags.push(tag)
                    },

                    removeTag: function() {

                    }
                }
            });
        </script>

    @endpush
@endonce
