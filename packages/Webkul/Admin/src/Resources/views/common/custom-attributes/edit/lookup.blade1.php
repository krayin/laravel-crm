<lookup-component></lookup-component>

@push('scripts')

    <script type="text/x-template" id="lookup-component-template">
        <div class="lookup-control">
            <div class="form-group">
                <input type="text" v-validate="'{{$validations}}'" class="control" name="{{ $attribute->code }}" :data-vv-as="&quot;{{ $attribute->name }}&quot;" v-model.lazy="search_term" v-debounce="500" autocomplete="off" v-on:keyup="search">

                <input type="hidden" name="{{ $attribute->code }}" v-model="entity_id" v-if="entity_id"/>

                <div class="lookup-results" v-if="state == ''">
                    <ul>
                        <li v-for='(result, index) in results' @click="addLookUp(result)">
                            <span>@{{ result.label }}</span>
                        </li>

                        <li v-if='! results.length && search_term.length && ! is_searching'>
                            <span>{{ __('admin::app.common.no-result-found', ['attribute' => $attribute->name]) }}</span>
                        </li>

                        <li class="action" v-if='search_term.length && ! is_searching' @click="createNew()">
                            <span>
                                + {{ __('admin::app.common.add-as', ['attribute' => $attribute->name]) }}
                            </span> 
                        </li>
                    </ul>
                </div>

                <span class="control-error" v-if="errors.has('{{ $attribute->code }}')">
                    @{{ errors.first('{!! $attribute->code !!}') }}
                </span>

                <i class="icon loader-active-icon" v-if="is_searching"></i>
            </div>
        </div>
    </script>

    <script>
        Vue.component('lookup-component', {

            template: '#lookup-component-template',

            inject: ['$validator'],

            data: function () {
                return {
                    search_term: '',

                    entity_id: null,

                    is_searching: false,

                    state: '',

                    results: [],
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

                    var self = this;
                    
                    this.$http.get("{{ route('admin.settings.attributes.lookup', $attribute->lookup_type) }}", {params: {query: this.search_term}})
                        .then (function(response) {
                            self.results = response.data;

                            self.is_searching = false;
                        })
                        .catch (function (error) {
                            self.is_searching = false;
                        })
                }, 500),

                addLookUp: function(result) {
                    this.state = 'old';

                    this.entity_id = result['value'];

                    this.search_term = result['label'];

                    this.searched_result = [];
                },

                createNew: function() {
                    this.state = 'new';

                    this.entity_id = null;
                }
            }
        });
    </script>
@endpush