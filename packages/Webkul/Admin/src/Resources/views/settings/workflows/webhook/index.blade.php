@push('scripts')
    <script type="text/x-template" id="webhook-component-template">
        <div>
            <label>{{ __('admin::app.settings.workflows.webhook_heading') }}</label>

            <hr>

            <div class="form-group">
                <label>{{ __('admin::app.settings.workflows.webhook_request_method') }}</label>

                <select
                    :name="['actions[' + index + '][hook][method]']"
                    class="control"
                    v-model="action.hook.method"
                >  
                    <option v-for='(text, method) in matchedAction.request_methods' :value="method">
                        @{{ text }}
                    </option>
                </select>
            </div>

            <div class="form-group">
                <label>{{ __('admin::app.settings.workflows.webhook_url') }}</label>

                <input id="description" type="url" :name="['actions[' + index + '][hook][url]']" class="control" v-model="action.hook.url">
            </div>

            <div class="form-group">
                <label>{{ __('admin::app.settings.workflows.request-headers') }}</label>
                
                <div
                    v-for="(header, idx) in headers"
                    class="webhook-headers"
                >
                    <div class="webhook-header-items">
                        <div class="form-group">
                            <input
                                type="text"
                                :name="['actions[' + index + '][hook][headers]['+idx+'][key]']"
                                :id="['actions[' + index + '][hook][headers]['+idx+'][key]']"
                                class="control"
                                placeholder="{{ __('admin::app.settings.workflows.header_key') }}"
                                v-model="header.key" 
                            >
                        </div>
        
                        <div class="form-group">
                            <input
                                type="text"
                                :name="['actions[' + index + '][hook][headers]['+idx+'][value]']"
                                :id="['actions[' + index + '][hook][headers]['+idx+'][value]']"
                                class="control"
                                placeholder="{{ __('admin::app.settings.workflows.header_value') }}"
                                v-model="header.value"
                            >
                        </div>
                    </div>

                    <div>
                        <i class="icon trash-icon remove-header" @click="$delete(headers, idx)"></i>
                    </div>
                </div>

                <button
                    type="button"
                    class="btn btn-primary"
                    @click="addHeader" 
                >
                    {{ __('admin::app.settings.workflows.add_header') }}
                </button>
            </div>

            <div class="form-group">
                <label>{{ __('admin::app.settings.workflows.webhook_encoding') }}</label>

                <select
                    :name="['actions[' + index + '][hook][encoding]']"
                    :id="['actions[' + index + '][hook][encoding]']"
                    class="control"
                    v-model="action.hook.encoding"
                >
                    <option
                        v-for='(text, encoding) in matchedAction.encodings'
                        :value="encoding"
                    >
                        @{{ text }}
                    </option>
                </select>
            </div>

            <div
                class="form-group"
                v-if="action.hook.method != 'get' && action.hook.method != 'delete'"
            >
                <label>{{ __('admin::app.settings.workflows.request_body') }}</label>

                <tabs>
                    <tab
                        name="{{ __('admin::app.settings.workflows.simple_body') }}"
                        :selected="true"
                    >
                        <div>
                            <div v-if="entityType == 'leads'">
                                <label>{{ __('admin::app.settings.workflows.lead') }}</label>
                        
                                <div
                                    v-for="(lead, leadIndex) in leads"
                                    :key="lead.id"
                                    class="webhook-request-body"
                                >
                                    <input
                                        type="checkbox"
                                        :name="'actions[' + index + '][hook][simple][' + leadIndex + ']'"
                                        :id="'lead_' + lead.id"
                                        :value="'lead_' + lead.id"
                                        v-model="action.hook.simple"
                                    >

                                    <label :for="'lead_' + lead.id">@{{ lead.name }}</label>
                                </div>
                            </div>
                        </div>
                        
                        <div v-if="entityType == 'leads' || entityType == 'persons'" :value="entityType">
                            <label>{{ __('admin::app.settings.workflows.person') }}</label>
                        
                            <div
                                v-for="(person, personIndex) in persons"
                                :key="person.id"
                                class="webhook-request-body"
                            >
                                <input
                                    type="checkbox"
                                    :name="'actions[' + index + '][hook][simple][' + personIndex + ']'"
                                    :id="'person_' + person.id"
                                    :value="'person_' + person.id"
                                    v-model="action.hook.simple"
                                >
                                <label :for="'person_' + person.id">@{{ person.name }}</label>
                            </div>
                        </div>

                        <div v-if="entityType == 'leads' || entityType == 'quotes'">
                            <label>{{ __('admin::app.settings.workflows.quote') }}</label>
                        
                            <div
                                v-for="(quote, quoteIndex) in quotes"
                                :key="quote.id"
                                class="webhook-request-body"
                            >
                                <input
                                    type="checkbox"
                                    :name="'actions[' + index + '][hook][simple][' + quoteIndex + ']'"
                                    :id="'quote_' + quote.id"
                                    :value="'quote_' + quote.id"
                                    v-model="action.hook.simple"
                                >

                                <label :for="'quote_' + quote.id">@{{ quote.name }}</label>
                            </div>
                        </div>
                        
                        <div v-if="entityType == 'leads' || entityType == 'activities'">
                            <label>{{ __('admin::app.settings.workflows.activity') }}</label>
                        
                            <div
                                v-for="(activity, activityIndex) in activities"
                                :key="activity.id"
                                class="webhook-request-body"
                            >
                                <input
                                    type="checkbox"
                                    :name="'actions[' + index + '][hook][simple][' + activityIndex + ']'"
                                    :id="'activity_' + activity.id"
                                    :value="'activity_' + activity.id"
                                    v-model="action.hook.simple"
                                >

                                <label :for="'activity_' + activity.id">@{{ activity.name }}</label>
                            </div>
                        </div>
                    </tab>

                    <tab name="{{ __('admin::app.settings.workflows.raw') }}">
                        <span class="help">{{ __('admin::app.settings.workflows.request_body_info') }}</span>

                        <textarea
                            :name="['actions[' + index + '][hook][custom]']"
                            id="description"
                            class="control"
                            v-model="action.hook.custom"
                        >
                        </textarea>
                    </tab>
                </tabs>
            </div> 
        </div>
    </script>

    <script>
        Vue.component('webhook-component', {

            template: '#webhook-component-template',

            inject: ['$validator'],

            props: ["entityType", "index", "matchedAction"],

            data: function () {
                return {
                    action: {
                        hook: {
                            method: [],
                            url: '',
                            encoding: '',
                            simple: [],
                            custom: ''
                        }
                    },
                    headers: @json(collect($workflow->actions ?? [])->first()['hook']['headers'] ?? []),
                    leads: @json(app('\Webkul\Workflow\Helpers\Entity\Lead')->getAttributes('leads', [])),
                    persons: @json(app('\Webkul\Workflow\Helpers\Entity\Person')->getAttributes('persons', [])),
                    quotes: @json(app('\Webkul\Workflow\Helpers\Entity\Quote')->getAttributes('quotes', [])),
                    activities: @json(app('\Webkul\Workflow\Helpers\Entity\Activity')->getAttributes('activities', [])),
                }
            },

            mounted() {
                this.setAction();
            },

            methods: {
                setAction() {
                    if(this.$parent.action.hook) {
                        this.action.hook = this.$parent.action.hook;
                    }
                },
                addHeader: function() {
                    this.headers.push({
                        'key': '',
                        'value': ''
                    });
                }
            }
        });
    </script>
@endpush