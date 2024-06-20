@push('scripts')
    <script type="text/x-template" id="webhook-component-template">
        <div>
            <label>{{ __('admin::app.settings.workflows.webhook_heading') }}</label>

            <hr>

            <div class="form-group">
                <label>{{ __('admin::app.settings.workflows.webhook_request_method') }}</label>

                <select :name="['actions[' + index + '][hook][method]']" class="control" v-model="action.hook.method">  
                    <option v-for='(text, method) in matchedAction.request_methods' :value="method">
                        @{{ text }}
                    </option>
                </select>
            </div>

            <div class="form-group">
                <label>{{ __('admin::app.settings.workflows.webhook_url') }}</label>

                <textarea id="description" type="text" :name="['actions[' + index + '][hook][url]']" class="control" v-model="action.hook.url"></textarea>
            </div>

            <div class="form-group">
                <table>
                    <tbody>
                        <tr v-for="(header, idx) in headers">
                            <td>
                                <input :name="['actions[' + index + '][hook][headers]['+idx+'][key]']" v-model="header.key" class="control" placeholder="{{ __('admin::app.settings.workflows.header_key') }}" />
                            </td>

                            <td>
                                <input :name="['actions[' + index + '][hook][headers]['+idx+'][value]']" v-model="header.value" class="control" placeholder="{{ __('admin::app.settings.workflows.header_value') }}" />
                            </td>

                            <td class="actions">
                                <i class="icon trash-icon" @click="$delete(headers, idx)"></i>
                            </td>
                        </tr>
                    </tbody>
                </table>

                <br />

                <div class="pull-right">
                    <i class="icon plus-white-icon"></i>

                    <a @click="addHeader" class="btn btn-primary">{{ __('admin::app.settings.workflows.add_header') }}</a>
                </div>
            </div>

            <div class="form-group" id="webhook-encoding">
                <label>{{ __('admin::app.settings.workflows.webhook_encoding') }}</label>

                <label v-for='(text, encoding) in matchedAction.encodings' class="radio-inline">
                    <input type="radio" :name="['actions[' + index + '][hook][encoding]']" :value="encoding" class="control inline-radio" v-model="action.hook.encoding" />
                    @{{ text }}
                </label>
            </div>

            <div class="form-group" v-if="action.hook.method != 'get' && action.hook.method != 'delete'">
                <label>{{ __('admin::app.settings.workflows.request_body') }}</label>

                <tabs>
                    <tab name="{{ __('admin::app.settings.workflows.simple_body') }}" :selected="true">
                        <div>
                            <div v-if="entityType == 'leads'">
                                <label>{{ __('admin::app.settings.workflows.lead') }}</label>

                                <div v-for="lead in leads">
                                    <input type="checkbox" :name="['actions[' + index + '][hook][simple][]']" :value="'lead_'+lead.id" v-model="action.hook.simple" />

                                    @{{ lead.name }}
                                </div>
                            </div>
                        </div>

                        <div v-if="entityType == 'leads' || entityType == 'persons'" :value="entityType">
                            <label>{{ __('admin::app.settings.workflows.person') }}</label>

                            <div v-for="person in persons">
                                <input type="checkbox" :name="['actions[' + index + '][hook][simple][]']" :value="'person_'+person.id" v-model="action.hook.simple" />
                                @{{ person.name }}
                            </div>
                        </div>

                        <div v-if="entityType == 'leads' || entityType == 'quotes'">
                            <label>{{ __('admin::app.settings.workflows.quote') }}</label>

                            <div v-for="quote in quotes">
                                <input type="checkbox" :name="['actions[' + index + '][hook][simple][]']" :value="'quote_'+quote.id" v-model="action.hook.simple" />
                                @{{ quote.name }}
                            </div>
                        </div>

                        <div v-if="entityType == 'leads' || entityType == 'activities'">
                            <label>{{ __('admin::app.settings.workflows.activity') }}</label>

                            <div v-for="activity in activities">
                                <input type="checkbox" :name="['actions[' + index + '][hook][simple][]']" :value="'activity_'+activity.id" v-model="action.hook.simple" />

                                @{{ activity.name }}
                            </div>
                        </div>

                        </div>
                    </tab>

                    <tab name="{{ __('admin::app.settings.workflows.custom_body') }}">
                        <span class="help">Add key value pair in new line, i.e., key=value </span>

                        <textarea id="description" :name="['actions[' + index + '][hook][custom]']" v-model="action.hook.custom" class="control"></textarea>
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
                    headers : @json(collect($workflow->actions)->first()['hook']['headers'] ?? []),
                    'leads': @json(app('\Webkul\Workflow\Helpers\Entity\Lead')->getAttributes('leads', [])),
                    'persons': @json(app('\Webkul\Workflow\Helpers\Entity\Person')->getAttributes('persons', [])),
                    'quotes': @json(app('\Webkul\Workflow\Helpers\Entity\Quote')->getAttributes('quotes', [])),
                    'activities': @json(app('\Webkul\Workflow\Helpers\Entity\Activity')->getAttributes('activities', [])),
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