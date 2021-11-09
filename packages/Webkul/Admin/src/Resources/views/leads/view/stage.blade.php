{!! view_render_event('admin.leads.view.informations.stages.before', ['lead' => $lead]) !!}

<stage-component></stage-component>

{!! view_render_event('admin.leads.view.informations.stages.after', ['lead' => $lead]) !!}

@push('scripts')
    <script type="text/x-template" id="stage-component-template">
        <div>
            <div class="pipeline-stage-container">
                <ul class="pipeline-stages" :class="currentStage.code">
                    <li
                        class="stage"
                        v-for="(stage, index) in customStages"
                        :class="{ active: currentStage.id >= stage.id }"
                        @click="changeStage(stage)"
                        v-if="stage.code != 'won' && stage.code != 'lost'"
                    >
                        <span>@{{ stage.name }}</span>
                    </li>

                    <li class="stage">
                        <span class="dropdown-toggle">
                            {{ __('admin::app.leads.won-lost') }}
                            <i class="icon arrow-down-s-icon"></i>
                        </span>

                        <div class="dropdown-list">
                            <div class="dropdown-container">
                                <ul>
                                    <li @click="nextStageCode = 'won'; $root.openModal('updateLeadStageModal')">
                                        {{ __('admin::app.leads.won') }}
                                    </li>
                                    
                                    <li @click="nextStageCode = 'lost'; $root.openModal('updateLeadStageModal')">
                                        {{ __('admin::app.leads.lost') }}
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </li>
                </ul>

                <div class="date-panel">
                    <span class="pull-left">
                        <i class="icon calendar-icon"></i>
                        <label>{{ __('admin::app.leads.created-date:') }}</label>
                        <span title="{{ core()->formatDate($lead->created_at) }}">{{ $lead->created_at->diffForHumans() }}</span>
                    </span>

                    <span class="pull-right">

                        @if ($lead->closed_at && in_array($lead->stage->code, ['won', 'lost']))
                            
                            <i class="icon calendar-icon"></i>
                            <label>{{ __('admin::app.leads.closed-date:') }}</label>
                            <span title="{{ core()->formatDate($lead->closed_at) }}">{{ $lead->closed_at->diffForHumans() }}</span>

                        @elseif ($lead->expected_close_date)

                             <i class="icon calendar-icon"></i>
                            <label>{{ __('admin::app.leads.expected-close-date:') }}</label>
                            <span title="{{ core()->formatDate($lead->expected_close_date, 'd M Y') }}">
                                {{
                                    $lead->expected_close_date->format('d-M-Y') == \Carbon\Carbon::now()->format('d-M-Y')
                                    ? 'Today'
                                    : $lead->expected_close_date->diffForHumans()
                                }}
                            </span>

                        @endif
                    </span>
                </div>
            </div>

            <form action="{{ route('admin.leads.update', $lead->id) }}" method="post" data-vv-scope="change-stage-form">
                <modal id="updateLeadStageModal" :is-open="$root.modalIds.updateLeadStageModal">
                    <h3 slot="header-title">{{ __('admin::app.leads.change-stage') }}</h3>
                    
                    <div slot="header-actions">
                        <button class="btn btn-sm btn-secondary-outline" @click="$root.closeModal('updateLeadStageModal')">{{ __('admin::app.leads.cancel') }}</button>

                        <button class="btn btn-sm btn-primary">{{ __('admin::app.leads.save-btn-title') }}</button>
                    </div>

                    <div slot="body" class="tabs-content">
                        @csrf()

                        <input name="_method" type="hidden" value="PUT">

                        <input type="hidden" name="lead_pipeline_stage_id" :value="this[nextStageCode] && this[nextStageCode].id">

                        <div class="form-group" v-if="this[nextStageCode] && this[nextStageCode].code == 'lost'">
                            <label>{{ __('admin::app.leads.lost-reason') }}</label>

                            <textarea class="control" name="lost_reason"></textarea>
                        </div>

                        <div class="form-group" v-if="this[nextStageCode] && this[nextStageCode].code == 'won'">
                            <label>{{ __('admin::app.leads.won-value') }}</label>

                            <input type="text" name="lead_value" class="control" value="{{ $lead->lead_value }}" />
                        </div>

                        <div class="form-group date">
                            <label>{{ __('admin::app.leads.closed-date') }}</label>

                            <date>
                                <input type="text" name="closed_at" class="control" />
                            </date>
                        </div>
                    </div>
                </modal>
            </form>
        </div>
    </script>

    <script>
        Vue.component('stage-component', {

            template: '#stage-component-template',
    
            inject: ['$validator'],

            data: function () {
                return {
                    currentStage: @json($lead->stage),

                    nextStageCode: null,

                    customStages: @json($lead->pipeline->stages),
                }
            },

            computed: {
                won: function() {
                    const results = this.customStages.filter(stage => stage.code == 'won');

                    return results[0];
                },

                lost: function() {
                    const results = this.customStages.filter(stage => stage.code == 'lost');

                    return results[0];
                },
            },

            methods: {
                changeStage: function(stage) {
                    var self = this;

                    this.$http.put("{{ route('admin.leads.update', $lead->id) }}", {'lead_pipeline_stage_id': stage.id})
                        .then (function(response) {
                            self.currentStage = stage;

                            window.flashMessages = [{'type': 'success', 'message': response.data.message}];

                            self.$root.addFlashMessages();
                        })
                        .catch (function (error) {
                        })
                }
            }
        });
    </script>
@endpush