{!! view_render_event('admin.leads.view.informations.activity_list.before', ['lead' => $lead]) !!}

<activity-list-component :current-time="{{ json_encode(Carbon\Carbon::now())}}"></activity-list-component>

{!! view_render_event('admin.leads.view.informations.activity_list.after', ['lead' => $lead]) !!}

@push('scripts')

    <script type="text/x-template" id="activity-list-component-template">
        <tabs class="activity-list">
            {!! view_render_event('admin.leads.view.informations.activity_list.general.before', ['lead' => $lead]) !!}

            <tab v-for="type in types" :name="typeLabels[type]" :key="type" :selected="type == 'all'">

                <div v-for="subType in ['planned', 'done']" :class="subType + '-activities ' + type">

                    <div class="section-tag" v-if="type != 'note' && type != 'file'">
                        <span v-if="subType == 'planned'">{{ __('admin::app.leads.planned') }}</span>

                        <span v-else>{{ __('admin::app.leads.done') }}</span>

                        <hr/>
                    </div>

                    <div class="activity-item" v-for="activity in getActivities(type, subType)">
                        <div class="title">
                            <h4 v-if="activity.title">@{{ activity.title }}</h4>

                            <span v-if="activity.type == 'note'">
                                {{ __('admin::app.leads.note-added') }}
                            </span>
                            
                            <span v-else-if="activity.type == 'call'">
                                @{{ '{!! __('admin::app.leads.call-scheduled') !!}'.replace(':from', formatDate(activity.schedule_from)).replace(':to', formatDate(activity.schedule_to)) }}
                            </span>

                            <span v-else-if="activity.type == 'meeting'">
                                @{{ '{!! __('admin::app.leads.meeting-scheduled') !!}'.replace(':from', formatDate(activity.schedule_from)).replace(':to', formatDate(activity.schedule_to)) }}
                            </span>

                            <span v-else-if="activity.type == 'lunch'">
                                @{{ '{!! __('admin::app.leads.lunch-scheduled') !!}'.replace(':from', formatDate(activity.schedule_from)).replace(':to', formatDate(activity.schedule_to)) }}
                            </span>

                            <span v-else-if="activity.type == 'email'">
                                @{{ '{!! __('admin::app.leads.email-scheduled') !!}'.replace(':from', formatDate(activity.schedule_from)).replace(':to', formatDate(activity.schedule_to)) }}
                            </span>
                            
                            <span v-else-if="activity.type == 'file'">
                                {{ __('admin::app.leads.file-added') }}
                            </span>

                            <span class="icon ellipsis-icon dropdown-toggle"></span>

                            <div class="dropdown-list">
                                <div class="dropdown-container">
                                    <ul>
                                        <li v-if="! activity.is_done" @click="markAsDone(activity)">
                                            {{ __('admin::app.leads.mark-as-done') }}
                                        </li>

                                        @if (bouncer()->hasPermission('activities.edit'))
                                            <li>
                                                <a :href="'{{ route('admin.activities.edit') }}/' + activity.id" target="_blank">
                                                    {{ __('admin::app.leads.edit') }}
                                                </a>
                                            </li>
                                        @endif

                                        @if (bouncer()->hasPermission('activities.delete'))
                                            <li @click="remove(activity)">
                                                {{ __('admin::app.leads.remove') }}
                                            </li>
                                        @endif
                                    </ul>
                                </div>
                            </div>
                        </div>

                        <div class="attachment" v-if="activity.file">
                            <i class="icon attachment-icon"></i>

                            <a :href="'{{ route('admin.activities.file_download') }}/' + activity.file.id" target="_blank">
                                @{{ activity.file.name }}
                            </a>
                        </div>

                        <div class="comment" v-if="activity.comment">
                            @{{ activity.comment }}
                        </div>

                        <div class="info">
                            @{{ activity.created_at | moment("Do MMM YYYY, h:mm A") }}

                            <span class="seperator">·</span>

                            <a :href="'{{ route('admin.settings.users.edit') }}/' + activity.user.id" target="_blank">
                                @{{ activity.user.name }}
                            </a> 
                        </div>
                    </div>

                    <div class="empty-activities" v-if="! getActivities(type, subType).length">
                        <span v-if="subType == 'planned'">{{ __('admin::app.leads.empty-planned-activities') }}</span>

                        <span v-else>{{ __('admin::app.leads.empty-done-activities') }}</span>
                    </div>
                    
                </div>
            </tab>

            {!! view_render_event('admin.leads.view.informations.activity_list.general.after', ['lead' => $lead]) !!}


            {!! view_render_event('admin.leads.view.informations.activity_list.quotes.before', ['lead' => $lead]) !!}

            <tab name="Quotes">
                <div class="table lead-quote-list" style="padding: 5px">

                    <table>
                        <thead>
                            <tr>
                                <th class="quote-subject">{{ __('admin::app.leads.subject') }}</th>

                                <th class="expired-at">{{ __('admin::app.leads.expired-at') }}</th>

                                <th class="sub-total">
                                    {{ __('admin::app.leads.sub-total') }}
                                    <span class="currency-code">({{ core()->currencySymbol(config('app.currency')) }})</span>
                                </th>

                                <th class="discount">
                                    {{ __('admin::app.leads.discount') }}
                                    <span class="currency-code">({{ core()->currencySymbol(config('app.currency')) }})</span>
                                </th>

                                <th class="tax">
                                    {{ __('admin::app.leads.tax') }}
                                    <span class="currency-code">({{ core()->currencySymbol(config('app.currency')) }})</span>
                                </th>

                                <th class="adjustment">
                                    {{ __('admin::app.leads.adjustment') }}
                                    <span class="currency-code">({{ core()->currencySymbol(config('app.currency')) }})</span>
                                </th>

                                <th class="grand-total">
                                    {{ __('admin::app.leads.grand-total') }}
                                    <span class="currency-code">({{ core()->currencySymbol(config('app.currency')) }})</span>
                                </th>

                                <th class="actions" style="width: 40px;"></th>
                            </tr>
                        </thead>
                        
                        <tbody>
                            <tr v-for="quote in quotes">
                                <td class="quote-subject">@{{ quote.subject }}</td>

                                <td class="expired-at">@{{ quote.expired_at }}</td>
                                
                                <td class="sub-total">@{{ quote.sub_total }}</td>

                                <td class="discount">@{{ quote.discount_amount }}</td>

                                <td class="tax">@{{ quote.tax_amount }}</td>

                                <td class="adjustment">@{{ quote.adjustment_amount }}</td>

                                <td class="grand-total">@{{ quote.grand_total }}</td>

                                <td class="actions">
                                    <span class="icon ellipsis-icon dropdown-toggle"></span>

                                    <div class="dropdown-list">
                                        <div class="dropdown-container">
                                            <ul>
                                                <li>
                                                    <a :href="'{{ route('admin.quotes.edit') }}/' + quote.id">
                                                        {{ __('admin::app.leads.edit') }}
                                                    </a>
                                                </li>
                                                
                                                <li>
                                                    <a :href="'{{ route('admin.quotes.print') }}/' + quote.id" target="_blank">
                                                        {{ __('admin::app.leads.export-to-pdf') }}
                                                    </a>
                                                </li>

                                                <li @click="removeQuote(quote)">
                                                    {{ __('admin::app.leads.remove') }}
                                                </li>
                                            </ul>
                                        </div>
                                    </div>
                                </td>
                            </tr>

                            <tr v-if="! quotes.length">
                                <td colspan="10">
                                    <p style="text-align: center;">{{ __('admin::app.common.no-records-found') }}</p>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </tab>

            {!! view_render_event('admin.leads.view.informations.activity_list.quotes.after', ['lead' => $lead]) !!}
        </tabs>
    </script>

    <script>
        Vue.component('activity-list-component', {

            template: '#activity-list-component-template',
    
            inject: ['$validator'],

            props: {
            currentTime: {
                type: [Array, String, Object],
                required: false,
                default: (function() {
                    return [];
                })
            },
        },

            data: function () {
                return {
                    activities: @json($lead->activities),

                    types: ['all', 'note', 'call', 'meeting', 'lunch', 'email', 'file'],

                    typeLabels: {
                        'all': "{{ __('admin::app.leads.all') }}",

                        'note': "{{ __('admin::app.leads.notes') }}",

                        'call': "{{ __('admin::app.leads.calls') }}",
                        
                        'meeting': "{{ __('admin::app.leads.meetings') }}",

                        'lunch': "{{ __('admin::app.leads.lunches') }}",

                        'email': "{{ __('admin::app.leads.emails') }}",

                        'file': "{{ __('admin::app.leads.files') }}",
                    },

                    quotes: @json($lead->quotes()->with(['person', 'user'])->get())
                }
            },

            mounted() {

                var currentTime = this.currentTime;
                
            },

            computed: {
                all: function() {
                    return this.activities;
                },

                note: function() {
                    return this.activities.filter(activity => activity.type == 'note');
                },

                call: function() {
                    return this.activities.filter(activity => activity.type == 'call');
                },

                meeting: function() {
                    return this.activities.filter(activity => activity.type == 'meeting');
                },

                lunch: function() {
                    return this.activities.filter(activity => activity.type == 'lunch');
                },

                email: function() {
                    return this.activities.filter(activity => activity.type == 'email');
                },

                file: function() {
                    return this.activities.filter(activity => activity.type == 'file');
                }
            },

            methods: {
                getActivities: function(type, subType) {
                    if (subType == 'planned') {
                        return this[type].filter(activity => ! activity.is_done);
                    } else {
                        return this[type].filter(activity => activity.is_done);
                    }
                },

                markAsDone: function(activity) {
                    var self = this;

                    console.log();

                    if (activity.schedule_to >= this.currentTime) {
                        if(window.confirm("Trying to mark activity as done before scheduled time.")){
                            this.$http.put("{{ route('admin.activities.update') }}/" + activity['id'], {'is_done': 1})
                            .then (function(response) {
                                activity.is_done = 1;

                                window.flashMessages = [{'type': 'success', 'message': response.data.message}];

                                self.$root.addFlashMessages();
                            })
                            .catch (function (error) {
                            })
                        }
                    }
                },

                remove: function(activity) {
                    if (! confirm('Do you really want to perform this action?')) {
                        return;
                    }

                    var self = this;

                    this.$http.delete("{{ route('admin.activities.delete') }}/" + activity['id'])
                        .then (function(response) {
                            const index = self.activities.indexOf(activity);

                            Vue.delete(self.activities, index);
                            
                            window.flashMessages = [{'type': 'success', 'message': response.data.message}];

                            self.$root.addFlashMessages();
                        })
                        .catch (function (error) {
                        })
                },

                removeQuote: function(quote) {
                    if (! confirm('Do you really want to perform this action?')) {
                        return;
                    }
                    
                    var self = this;

                    this.$http.delete("{{ route('admin.leads.quotes.delete', $lead->id) }}/" + quote['id'])
                        .then (function(response) {
                            const index = self.quotes.indexOf(quote);

                            Vue.delete(self.quotes, index);
                            
                            window.flashMessages = [{'type': 'success', 'message': response.data.message}];

                            self.$root.addFlashMessages();
                        })
                        .catch (function (error) {
                        })
                },

                formatDate: function(date) {
                    return moment(String(date)).format('Do MMM YYYY, h:mm A')
                },
            }
        });
    </script>
@endpush