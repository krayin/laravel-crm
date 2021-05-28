@extends('admin::layouts.master')

@section('page_title')
    {{ __('admin::app.dashboard.title') }}
@stop

@section('content-wrapper')
    <div class="content full-page dashboard">
        <h1>{{ __('admin::app.dashboard.title') }}</h1>

        <selected-cards-filter></selected-cards-filter>

        <cards-collection></cards-collection>
    </div>
@stop

@push('scripts')
    <script type="text/x-template" id="selected-cards-template">
        <div class="cards-collection form-group">
            <div class="toggle-btn" @click="showCardOptions = !showCardOptions">
                <span>{{ __('admin::app.dashboard.cards') }}</span>
                <span class="icon plus-black-icon"></span>
            </div>

            <div class="cards-options" v-if="showCardOptions">
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
        </div>
    </script>

    <script type="text/x-template" id="cards-collection-template">
        <div class="row-grid-3">
            <template v-for="(card, index) in cards">
                <div :class="`card ${card.card_border || ''}`" :key="index" v-if="card.selected">
                    <template v-if="card.label">
                        <label>
                            @{{ card.label }}

                            <card-filter
                                :card-id="card.card_id || ''"
                                :filter-type="card.filter_type"
                            ></card-filter>
                        </label>
                    </template>

                    <card-component
                        :index="index"
                        :card-id="card.card_id || ''"
                        :card-type="card.card_type"
                    ></card-component>
                </div>
            </template>
        </div>
    </script>

    <script type="text/x-template" id="card-template">
        <div v-if="dataLoaded" class="card-data">
            <bar-chart id="lead-chart" :data="dataCollection.data" v-if="cardType == 'bar_chart'"></bar-chart>

            <line-chart :id="`line-chart-${index}`" :data="dataCollection.data" v-if="cardType == 'line_chart'"></line-chart>

            <template v-else-if="['activity', 'stages_bar'].indexOf(cardType) > -1">
                <h3 v-if="cardType != 'stages_bar'">
                    <template v-for="(header_data, index) in dataCollection.header_data">
                        @{{ header_data }}
                    </template>
                </h3>

                <div class="activity bar-data" v-for="(data, index) in dataCollection.data">
                    <span>@{{ data.label }}</span>
                    <div class="bar">
                        <div class="primary" :style="`width: ${(data.count * 100) / (dataCollection.total || 10)}%;`"></div>
                    </div>
                    <span>@{{ `${data.count}/${(dataCollection.total || 10)}` }}</span>
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

            <template v-if="! dataCollection || dataCollection.length == 0 || dataCollection.data.length == 0">
                <div class="custom-card">
                    {{ __('admin::app.dashboard.no_record_found') }}
                </div>
            </template>
        </div>
    </script>

    <script type="text/x-template" id="card-filter-template">
        <select v-if="filterType == 'monthly'" @change="changeCardFilter">
            <option value="this_month">This month</option>
            <option value="last_month">Last month</option>
        </select>

        <select v-else-if="filterType == 'daily'" @change="changeCardFilter">
            <option value="today">Today</option>
            <option value="yesterday">Yesterday</option>
        </select>
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
                updateCards: function () {
                    this.$http.post(`{{ route('admin.api.dashboard.cards.update') }}`, {
                        cards: this.columns
                    })
                        .then(response => {
                            this.cards = response.data;

                            this.showCardOptions = false;
                        })
                        .catch(error => {
                        });
                }
            }
        });

        Vue.component('cards-collection', {
            template: "#cards-collection-template",

            data: function () {
                return {
                    cards: [],
                }
            },

            created: function () {
                this.getDashboardCards();
            },

            methods: {
                getDashboardCards: function () {
                    this.$http.get(`{{ route('admin.api.dashboard.cards.index') }}`)
                        .then(response => {
                            this.cards = response.data;

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
            },

            methods: {
                getCardData: function (cardId, filter) {
                    this.$http.get(`{{ route('admin.api.dashboard.card.index') }}?card-id=${cardId}${filter ? '&filter=' + filter : ''}`)
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