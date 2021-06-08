@extends('admin::layouts.master')

@section('page_title')
    {{ __('admin::app.dashboard.title') }}
@stop

@section('content-wrapper')
    <div class="content full-page dashboard">
        <h1>{{ __('admin::app.dashboard.title') }}</h1>

        <selected-cards-filter></selected-cards-filter>

        {{-- <div class="form-group date">
            <date>
                <input type="text" class="control" id="start-date" value="{{ $startDate }}" />
            </date>

            <date>
                <input type="text" class="control" id="end-date" value="{{ $endDate }}" />
            </date>
        </div> --}}

        <cards-collection></cards-collection>
    </div>
@stop

@push('scripts')
    <script type="text/x-template" id="selected-cards-template">
        {{-- <div class="cards-collection form-group">
            <div class="toggle-btn dropdown-toggle">
                <span>{{ __('admin::app.dashboard.cards') }}</span>
                <span class="icon plus-black-icon"></span>
            </div>

            <div class="cards-options dropdown-list">
                <div>
                    <header>
                        <span class="btn btn-sm btn-secondary-outline">
                            {{ __('admin::app.dashboard.column') }}
                        </span>

                        <span class="btn btn-sm btn-primary" @click="updateCards">
                            {{ __('admin::app.dashboard.done') }}
                        </span>
                    </header>

                    <template v-for="(column, index) in columns">
                        <div class="cards-column" :key="index" v-if="column.card_type != 'custom_card'">
                            <span class="checkbox">
                                <input
                                    type="checkbox"
                                    :id="column.card_id"
                                    :name="column.card_id"
                                    v-model="columns[index].selected"
                                />
                                <label for="checkbox2" class="checkbox-view"></label>
                                @{{ column.label }}
                            </span>
                        </div>
                    </template>
                </div>
            </div>
        </div> --}}
        
        <date-range
            :update="updateCardData"
            end-date="{{ $endDate }}"
            start-date="{{ $startDate }}"
            class="card-filter-container"
        ></date-range>
    </script>

    <script type="text/x-template" id="cards-collection-template">
        <div class="row-grid-3">
            <template v-for="(card, index) in filteredCards">
                {{-- <draggable
                    tag="div"
                    :key="index"
                    handle=".drag-icon"
                    :list="filteredCards"
                    :class="`card ${card.card_border || ''}`"
                > --}}
                <div :class="`card ${card.card_border || ''}`">
                    <template v-if="card.label">
                        <label>
                            @{{ card.label }}

                            {{-- <card-filter
                                :card-id="card.card_id || ''"
                                :filter-type="card.filter_type"
                            ></card-filter> --}}

                            <i class="icon drag-icon"></i>
                        </label>
                    </template>

                    <card-component
                        :index="index"
                        :card-type="card.card_type"
                        :card-id="card.card_id || ''"
                    ></card-component>
                {{-- </draggable> --}}
                </div>
            </template>
        </div>
    </script>

    <script type="text/x-template" id="card-template">
        <div v-if="dataLoaded" class="card-data">
            <bar-chart
                id="lead-chart"
                :data="dataCollection.data"
                v-if="
                    cardType == 'bar_chart'
                    && dataCollection
                "
            ></bar-chart>

            <line-chart
                :id="`line-chart-${index}`"
                :data="dataCollection.data"
                v-if="
                    cardType == 'line_chart'
                    && dataCollection
                "
            ></line-chart>

            <template v-else-if="['activity', 'stages_bar'].indexOf(cardType) > -1">
                <h3 v-if="dataCollection.header_data">
                    <template v-for="(header_data, index) in dataCollection.header_data">
                        @{{ header_data }}
                    </template>
                </h3>

                <div class="activity bar-data" v-for="(data, index) in dataCollection.data">
                    <span>@{{ data.label }}</span>

                    <div class="bar">
                        <div
                            class="primary"
                            :style="`width: ${data.count ? (data.count * 100) / (dataCollection.total || 10) : 0}%;`"
                        ></div>
                    </div>

                    <span>@{{ `${data.count || 0}/${(dataCollection.total || 10)}` }}</span>
                </div>
            </template>

            <div class="lead" v-else-if="cardType == 'top_card'" v-for="(data, index) in dataCollection.data">
                <label>@{{ data.title }}</label>

                <div class="details">
                    <span>@{{ data.amount | toFixed }}</span>
                    <span>@{{ data.created_at | formatDate }}</span>
                    <span>
                        <span class="badge badge-round badge-primary" v-if="data.status == 1"></span>
                        <span class="badge badge-round badge-warning" v-else-if="data.status == 2"></span>
                        <span class="badge badge-round badge-success" v-else-if="data.status == 3"></span>

                        @{{ data.statusLabel }}
                    </span>
                </div>
            </div>

            <div class="email-data" v-else-if="cardType == 'emails'" v-for="(data, index) in dataCollection.data">
                <span>@{{ data.count }}</span>
                <span>@{{ data.label }}</span>
            </div>

            <template v-else-if="cardType == 'custom_card'">
                <div class="custom-card">+</div>
                <div class="custom-card">{{ __('admin::app.dashboard.custom_card') }}</div>
            </template>

            <template v-if="! dataCollection || dataCollection.length == 0 || (dataCollection.data && dataCollection.data.length == 0)">
                <div class="custom-card">
                    <i
                        class="icon empty-bar-icon"
                        v-if="cardType == 'bar_chart' || cardType == 'line_chart'"
                    ></i>

                    <img
                        v-else
                        src="{{ asset('vendor/webkul/admin/assets/images/empty-state-icon.svg') }}"
                    />

                    <span>{{ __('admin::app.dashboard.no_record_found') }}</span>
                </div>
            </template>
        </div>
    </script>

    <script type="text/x-template" id="card-filter-template">
        <div class="card-filter-container">
            <div class="toggle-btn dropdown-toggle">
                <span>{{ __('admin::app.dashboard.cards') }}</span>
                <i class="icon arrow-down-icon"></i>
            </div>

            <div class="dropdown-list">
                <div class="dropdown-container">
                    <ul>
                        <template v-if="filterType == 'monthly'">
                            <li>This month</li>
                            <li>Last month</li>
                        </template>

                        <template v-else-if="filterType == 'daily'">
                            <li>Today</li>
                            <li>Yesterday</li>
                        </template>
                    </ul>
                </div>
            </div>
        </div>

        {{-- <select v-if="filterType == 'monthly'" @change="changeCardFilter">
            <option value="this_month">This month</option>
            <option value="last_month">Last month</option>
        </select>

        <select v-else-if="filterType == 'daily'" @change="changeCardFilter">
            <option value="today">Today</option>
            <option value="yesterday">Yesterday</option>
        </select> --}}
    </script>

    <script>
        Vue.component('selected-cards-filter', {
            template: '#selected-cards-template',

            data: function () {
                return {
                    columns: [],
                    showCardOptions: false,
                }
            },

            created: function () {
                EventBus.$on('cardsLoaded', cards => {
                    this.columns = cards;
                });
            },

            methods: {
                updateCards: function (dateRange) {
                    this.$http.post(`{{ route('admin.api.dashboard.cards.update') }}`, {
                        cards: this.columns
                    }).then(response => {
                        this.cards = response.data;

                        this.showCardOptions = false;
                    })
                    .catch(error => {});
                },

                updateCardData: function (dateRange) {
                    EventBus.$emit('updateDateRange', dateRange);
                }
            }
        });

        Vue.component('cards-collection', {
            template: "#cards-collection-template",

            data: function () {
                return {
                    cards: [],
                    filteredCards: [],
                }
            },

            created: function () {
                this.getDashboardCards();
            },

            mounted: function () {
                $('#start-date').change(({target}) => {
                    EventBus.$emit('updateDateRange', {
                        datesRange : `${$('#start-date').val()},${$('#end-date').val()}`,
                    });
                });

                $('#end-date').change(() => {
                    EventBus.$emit('updateDateRange', {
                        datesRange : `${$('#start-date').val()},${$('#end-date').val()}`,
                    });
                });
            },

            methods: {
                getDashboardCards: function () {
                    this.$http.get(`{{ route('admin.api.dashboard.cards.index') }}`)
                        .then(response => {
                            this.cards = response.data;
                            this.filteredCards = this.cards.filter(card => card.selected);

                            EventBus.$emit('cardsLoaded', this.cards);
                        })
                        .catch(error => {
                        });
                },
            }
        });

        Vue.component('card-component', {
            template: "#card-template",

            props: ['cardId', 'cardType', 'index'],

            data: function () {
                return {
                    dataLoaded: false,
                    dataCollection: {},
                }
            },

            created: function () {
                if (this.cardType != "custom_card") {
                    this.getCardData(this.cardId);
                } else {
                    this.dataLoaded = true;
                }

                EventBus.$on('applyCardFilter', updatedData => {
                    if (this.cardId == updatedData.cardId) {
                        this.getCardData(updatedData.cardId, updatedData.filterValue);
                    }
                });

                EventBus.$on('updateDateRange', dates => {
                    this.getCardData(this.cardId, dates.datesRange, "date-range");
                });
            },

            methods: {
                getCardData: function (cardId, filter, filterKey) {
                    this.dataLoaded = false;

                    filterKey = filterKey || "filter";

                    this.$http.get(`{{ route('admin.api.dashboard.card.index') }}?card-id=${cardId}${filter ? `&${filterKey}=${filter}` : ''}`)
                        .then(response => {
                            this.dataCollection = response.data;

                            this.dataLoaded = true;
                        })
                        .catch(error => {
                        });
                },
            }
        });

        Vue.component('card-filter', {
            template: "#card-filter-template",

            props: ['filterType', 'cardId'],

            methods: {
                changeCardFilter: function ({target}) {
                    EventBus.$emit('applyCardFilter', {
                        cardId      : this.cardId,
                        filterValue : target.value,
                    });
                }
            }
        });
    </script>
@endpush