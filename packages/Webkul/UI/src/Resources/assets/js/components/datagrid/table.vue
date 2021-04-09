<template>
    <div class="table-body" v-if="resultLoaded && Object.keys(tableData).length > 0">
        <filter-component
            :tabs="tabs"
            :results="tableData"
            :pagination-data="tableData.pagination_data"
        ></filter-component>

        <table>
            <thead-component
                :columns="tableData.columns"
                :actions="tableData.actions"
                :mass-actions="tableData.columns"
            ></thead-component>

            <tbody-component
                :actions="tableData.actions"
                :mass-actions="tableData.columns"
                :data-collection="tableData.records.data"
            ></tbody-component>
        </table>

        <!-- <pagination-component :pagination-data="encodedResult.pagination_data"></pagination-component> -->
    </div>
</template>

<script>
    import { mapState, mapActions } from 'vuex';

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
                resultLoaded: true,
                previousURL: null,
            }
        },
        
        computed: {
            ...mapState({
                tabs : state => state.tabs,
                tableData : state => state.tableData,
            }),
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
            ...mapActions([
                'updateTableData',
            ]),

            getData: ({newParams, clean_uri, self, url, usePrevious}) => {
                if (self.resultLoaded) {
                    self.resultLoaded = false;
    
                    if (usePrevious) {
                        url = self.previousURL;
                    } else {
                        url = url ? url : `${window.location.origin}/admin/api/datagrid?table=${self.tableClass}&${newParams}`;
                        self.previousURL = url;
                    }
    
                    self.$http.get(url)
                        .then(response => {
                            self.resultLoaded = true;
    
                            // update store data
                            self.updateTableData(response.data);
    
                            if (newParams) {
                                self.updatedURI(newParams);
                            }
                        })
                        .catch(error => {
                            // @TODO
                            // remove filter from filters array
                        })
                }
            },

            updatedURI: function (params) {
                var newURL = window.location.origin + window.location.pathname + `?${params}`;
                window.history.pushState({path: newURL}, '', newURL);
            }
        }
    };
</script>