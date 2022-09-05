<div class="content full-page">
    <div class="table">
        <div class="table-header">
            <h1>
                {!! view_render_event('admin.activities.index.header.before') !!}

                {{ Breadcrumbs::render('activities') }}

                {{ __('admin::app.activities.title') }}

                {!! view_render_event('admin.activities.index.header.after') !!}
            </h1>
        </div>

        <div class="calendar-body">

            <calendar-filters></calendar-filters>

            <calendar-component></calendar-component>
        </div>
    </div>
</div>

@push('scripts')
    <script type="text/x-template" id="calendar-filters-tempalte">
        <div class="form-group datagrid-filters">
            <div></div>
            
            <div class="filter-right">

                @include('admin::activities.index.view-swither')

            </div>

        </div>
    </script>

    <script type="text/x-template" id="calendar-component-tempalte">
        <div class="calendar-container">

            <vue-cal
                hide-view-selector
                :watchRealTime="true"
                :twelveHour="true"
                :disable-views="['years', 'year', 'month', 'day']"
                style="height: calc(100vh - 240px);"
                :events="events"
                @ready="getActivities"
                @view-change="getActivities"
                :on-event-click="onEventClick"
                locale="{{ app()->getLocale() }}"
            />

        </div>
    </script>

    <script>
        Vue.component('calendar-filters', {
            template: '#calendar-filters-tempalte',
        });


        Vue.component('calendar-component', {
            template: '#calendar-component-tempalte',
            
            data: function () {
                return {
                    events: []
                }
            },

            methods: {
                getActivities: function ({startDate, endDate}) {
                    this.$root.pageLoaded = false;

                    this.$http.get("{{ route('admin.activities.get', ['view_type' => 'calendar']) }}" + `&startDate=${new Date(startDate).toLocaleDateString("en-US")}&endDate=${new Date(endDate).toLocaleDateString("en-US")}`)
                        .then(response => {
                            this.$root.pageLoaded = true;

                            this.events = response.data.activities;
                        })
                        .catch(error => {
                            this.$root.pageLoaded = true;
                        });
                },

                onEventClick : function (event) {
                    window.location.href = "{{ route('admin.activities.edit') }}/" + event.id
                }
            }
        });
    </script>
@endpush