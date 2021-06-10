<template>
    <div class="table-body" v-if="Object.keys(tableData).length > 0">
        <filter-component></filter-component>

        <table v-if="tableData.records.total">
            <thead-component></thead-component>

            <tbody-component :result-loaded="resultLoaded"></tbody-component>
        </table>

        <div class="empty-table" v-else>
            <img :src="`${baseURL}/vendor/webkul/admin/assets/images/empty-table-icon.svg`" />

            <span>{{ __('ui.datagrid.no-records') }}</span>
        </div>        
    </div>
</template>

<script>
    import { mapState, mapActions } from 'vuex';

    export default {
        props: [
            'tableClass',
        ],

        data: function () {
            return {
                resultLoaded: true,
                previousURL: null,
                baseURL: window.baseURL,
            }
        },
        
        computed: {
            ...mapState({
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

            getData: ({newParams, self, url, usePrevious}) => {
                if (self.resultLoaded) {
                    self.resultLoaded = false;
    
                    if (usePrevious) {
                        url = self.previousURL;
                    } else {
                        url = url ? url : `${window.baseURL}/admin/api/datagrid?table=${self.tableClass}&${newParams}`;
                        self.previousURL = url;
                    }

                    if (Object.keys(window.params).length > 0) {
                        Object.keys(window.params).forEach(paramKey => {
                            url += `&${paramKey}=${window.params[paramKey]}`;
                        });
                    }
    
                    self.$http.get(url)
                        .then(response => {
                            self.resultLoaded = true;
    
                            // update store data
                            self.updateTableData(response.data);
    
                            if (newParams || newParams == "") {
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
                var newURL = window.location.origin + window.location.pathname + `${params != '' ? '?' + params : ''}`;
                window.history.pushState({path: newURL}, '', newURL);
            }
        }
    };
</script>