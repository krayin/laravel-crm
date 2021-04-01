<template>
    <div class="table" v-if="resultLoaded">
        <filter-component
            :tabs="tabs"
            :results="encodedResult"
            :pagination-data="encodedResult.pagination_data"
        ></filter-component>

        <table>
            <thead-component :columns="encodedResult.columns"></thead-component>
            <tbody-component :data-collection="encodedResult.records.data"></tbody-component>
        </table>

        <!-- <pagination-component :pagination-data="encodedResult.pagination_data"></pagination-component> -->
    </div>
</template>

<script>
    export default {
        props: [
            'filterIndex',
            'gridCurrentData',
            'massActions',
            'columns',
            'extraFilters',
            'tableClass',
        ],

        data: function () {
            return {
                resultLoaded: false,
                tabs: {
                    type: [{
                        'label'     : 'All',
                        'is_active' : true,
                        'key'       : 'all',
                    }, {
                        'label'     : 'Call',
                        'is_active' : false,
                        'key'       : 'call',
                    }, {
                        'label'     : 'Mail',
                        'is_active' : false,
                        'key'       : 'mail',
                    }, {
                        'label'     : 'Meeting',
                        'is_active' : false,
                        'key'       : 'meeting',
                    }],

                    duration: [{
                        'label'     : 'Yesterday',
                        'is_active' : true,
                        'key'       : 'yesterday',
                    }, {
                        'label'     : 'Today',
                        'is_active' : false,
                        'key'       : 'today',
                    }, {
                        'label'     : 'Tomorrow',
                        'is_active' : false,
                        'key'       : 'tomorrow',
                    }, {
                        'label'     : 'This week',
                        'is_active' : false,
                        'key'       : 'this_week',
                    }, {
                        'label'     : 'This month',
                        'is_active' : false,
                        'key'       : 'this_month',
                    }, {
                        'label'     : 'Custom',
                        'is_active' : false,
                        'key'       : 'custom',
                    }],
                }
            }
        },

        mounted: function () {
            let search = window.location.search;
            const initialIndex = search.indexOf('?');

            if (initialIndex > -1) {
                search = search.replace('?', '')
            }

            this.getData({self: this, newParams: search});

            EventBus.$on('change_tab_data', data => {
                this.tabs[data.type].map(value => {
                    value.is_active = false;
                });

                this.tabs[data.type].map(value => {
                    if (value.key == data.selectedTab) {
                        value.is_active = true;
                    }
                });

                EventBus.$emit('update_filter', {
                    'key'   : data.type,
                    'cond'  : 'eq',
                    'value' : data.selectedTab
                });
            });

            EventBus.$on('refresh_table_data', data => {
                data['self'] = this;

                this.getData(data);
            });
        },

        methods: {
            getData: ({newParams, clean_uri, self, url}) => {
                self.resultLoaded = false;

                url = url ? url : `${window.location.origin}/admin/api/datagrid?table=${self.tableClass}&${newParams}`;

                self.$http.get(url)
                    .then(response => {
                        self.resultLoaded = true;
                        self.encodedResult = response.data;

                        if (newParams) {
                            self.updatedURI(newParams);
                        }
                    })
                    .catch(error => {
                        // @TODO
                        // remove filter from filters array
                    })
            },

            updatedURI: function (params) {
                var newURL = window.location.origin + window.location.pathname + `?${params}`;
                window.history.pushState({path: newURL}, '', newURL);
            }
        }
    };
</script>