{!! view_render_event('admin.leads.view.header.tags.before', ['lead' => $lead]) !!}

<tags-component></tags-component>

{!! view_render_event('admin.leads.view.header.tags.after', ['lead' => $lead]) !!}


@push('scripts')
    <script type="text/x-template" id="tags-component-template">
        <div class="tags-container">
            @if (bouncer()->hasPermission('settings.other_settings.tags.create'))
                <i class="icon tags-icon" @click="is_dropdown_open = ! is_dropdown_open"></i>
            @endif

            <ul class="tag-list">
                <li v-for='(tag, index) in tags' :style="'background-color: ' + (tag.color ? tag.color : '#546E7A')">
                    @{{ tag.name }}
                    
                    @if (bouncer()->hasPermission('settings.other_settings.tags.delete'))
                        <i class="icon close-white-icon" @click="removeTag(tag)"></i>
                    @endif
                </li>
            </ul>

            <div class="tag-dropdown" v-if="is_dropdown_open">
                <div class="lookup-results" v-if="! show_form">
                    <ul>
                        <li class="control-list-item">
                            <div class="form-group">
                                <input
                                    type="text"
                                    class="control"
                                    v-model="term"
                                    placeholder="{{ __('admin::app.leads.search-tag') }}"
                                    autocomplete="off"
                                    v-on:keyup="search"
                                />

                                <i class="icon loader-active-icon" v-if="is_searching"></i>
                            </div>
                        </li>

                        <li v-for='(tag, index) in search_results' @click="addTag(tag)">
                            <span>@{{ tag.name }}</span>
                        </li>

                        <li v-if="! search_results.length && term.length && ! is_searching">
                            <span>{{ __('admin::app.common.no-result-found') }}</span>
                        </li>

                        <li class="action" @click="show_form = true">
                            <span>
                                + {{ __('admin::app.leads.add-tag') }}
                            </span> 
                        </li>
                    </ul>
                </div>

                <div class="form-container" v-else>
                    <form data-vv-scope="tag-form">
                        <div class="form-group" :class="[errors.has('tag-form.name') ? 'has-error' : '']">
                            <label class="required">{{ __('admin::app.leads.name') }}</label>

                            <input
                                type="text"
                                name="name"
                                class="control"
                                v-model="tag.name"
                                v-validate="'required'"
                                data-vv-as="&quot;{{ __('admin::app.leads.name') }}&quot;"
                            />

                            <span class="control-error" v-if="errors.has('tag-form.name')">
                                @{{ errors.first('tag-form.name') }}
                            </span>
                        </div>

                        <div class="form-group">
                            <label>{{ __('admin::app.leads.color') }}</label>
                            
                            <div class="color-list">
                                <span
                                    v-for='color in colors'
                                    :style="'background:' + color"
                                    :class="{active: tag.color == color}"
                                    @click="tag.color = color"
                                >
                                </span>
                            </div>
                        </div>

                        <div class="form-group button-group">
                            <button type="button" class="btn btn-sm btn-secondary-outline" @click="show_form = false">
                                {{ __('admin::app.leads.cancel') }}
                            </button>

                            <button type="button" class="btn btn-sm btn-primary" @click="createTag">
                                {{ __('admin::app.leads.save') }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </script>

    <script>
        Vue.component('tags-component', {

            template: '#tags-component-template',
    
            inject: ['$validator'],

            data: function() {
                return {
                    is_dropdown_open: false,

                    term: '',

                    is_searching: false,

                    tags: @json($lead->tags),

                    search_results: [],

                    tag: {
                        name: '',

                        color: '',

                        lead_id: "{{ $lead->id }}",
                    },

                    colors: [
                        '#337CFF',
                        '#FEBF00',
                        '#E5549F',
                        '#27B6BB',
                        '#FB8A3F',
                        '#43AF52',
                    ],

                    show_form: false,
                }
            },

            methods: {
                search: debounce(function () {
                    this.is_searching = true;

                    if (this.term.length < 2) {
                        this.search_results = [];

                        this.is_searching = false;

                        return;
                    }

                    var self = this;
                    
                    this.$http.get("{{ route('admin.settings.tags.search') }}", {params: {query: this.term}})
                        .then (function(response) {
                            self.tags.forEach(function(addedTag) {
                                
                                response.data.forEach(function(tag, index) {
                                    if (tag.id == addedTag.id) {
                                        response.data.splice(index, 1);
                                    }
                                });

                            });

                            self.search_results = response.data;

                            self.is_searching = false;
                        })
                        .catch (function (error) {
                            self.is_searching = false;
                        })
                }, 500),

                createTag: function() {
                    var self = this;

                    this.$validator.validateAll('tag-form').then(function (result) {
                        if (result) {
                            self.$http.post(`{{ route('admin.settings.tags.store') }}`, self.tag)
                                .then(response => {
                                    self.addTag(response.data.tag);
                                })
                                .catch(error => {
                                    window.flashMessages = [{'type': 'error', 'message': error.response.data.errors['name'][0]}];

                                    self.$root.addFlashMessages()
                                });
                        }
                    });
                },

                addTag: function(tag) {
                    var self = this;

                    self.$http.post(`{{ route('admin.leads.tags.store', $lead->id) }}`, tag)
                        .then(response => {
                            self.is_dropdown_open = self.show_form = false;

                            self.search_results = [];

                            self.term = '';

                            self.tags.push(tag);

                            window.flashMessages = [{'type': 'success', 'message': response.data.message}];

                            self.$root.addFlashMessages();
                        })
                        .catch(error => {
                            window.flashMessages = [{'type': 'error', 'message': error.response.data.message}];

                            self.$root.addFlashMessages()
                        });
                },

                removeTag: function(tag) {
                    var self = this;

                    this.$http.delete("{{ route('admin.leads.tags.delete', $lead->id) }}/" + tag['id'])
                        .then (function(response) {
                            const index = self.tags.indexOf(tag);

                            Vue.delete(self.tags, index);
                            
                            window.flashMessages = [{'type': 'success', 'message': response.data.message}];

                            self.$root.addFlashMessages();
                        })
                        .catch (function (error) {
                            window.flashMessages = [{'type': 'error', 'message': error.response.data.message}];

                            self.$root.addFlashMessages()
                        })
                }
            }
        });
    </script>
@endpush