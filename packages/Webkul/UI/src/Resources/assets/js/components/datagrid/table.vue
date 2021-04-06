<template>
    <div class="table-body" v-if="resultLoaded">
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
                        'name'      : 'All',
                        'isActive'  : true,
                        'key'       : 'all',
                    }, {
                        'name'      : 'Call',
                        'isActive'  : false,
                        'key'       : 'call',
                    }, {
                        'name'      : 'Mail',
                        'isActive'  : false,
                        'key'       : 'mail',
                    }, {
                        'name'      : 'Meeting',
                        'isActive'  : false,
                        'key'       : 'meeting',
                    }],

                    duration: [{
                        'name'      : 'Yesterday',
                        'isActive'  : true,
                        'key'       : 'yesterday',
                    }, {
                        'name'      : 'Today',
                        'isActive'  : false,
                        'key'       : 'today',
                    }, {
                        'name'      : 'Tomorrow',
                        'isActive'  : false,
                        'key'       : 'tomorrow',
                    }, {
                        'name'      : 'This week',
                        'isActive'  : false,
                        'key'       : 'this_week',
                    }, {
                        'name'      : 'This month',
                        'isActive'  : false,
                        'key'       : 'this_month',
                    }, {
                        'name'      : 'Custom',
                        'isActive'  : false,
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