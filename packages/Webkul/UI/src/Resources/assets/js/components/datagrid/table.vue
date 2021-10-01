<template>
    <div class="table">
        <div class="table-header">
            <slot name="table-header"></slot>

            <div class="table-action">
                <slot name="table-action"></slot>
            </div>
        </div>

        <sidebar-filter></sidebar-filter>

        <div class="table-body" v-if="Object.keys(tableData).length > 0">
            <spinner-meter :full-page="true" v-if="! pageLoaded"></spinner-meter>

            <filter-component>
                <template v-slot:extra-filters>
                    <slot name="extra-filters"></slot>
                </template>
            </filter-component>

            <table v-if="tableData.records.total">
                <thead-component></thead-component>

                <tbody-component
                    :result-loaded="resultLoaded"
                ></tbody-component>
            </table>

            <div class="empty-table" v-else>
                <div>
                    <img :src="`${baseURL}/vendor/webkul/admin/assets/images/empty-table-icon.svg`"/>

                    <span>{{ __("ui.datagrid.no-records") }}</span>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import { mapState, mapActions } from "vuex";

export default {
    props: ["dataSrc"],

    data: function() {
        return {
            pageLoaded: false,

            previousURL: null,

            resultLoaded: true,

            baseURL: window.baseURL
        };
    },

    computed: {
        ...mapState({
            tableData: state => state.tableData
        })
    },

    mounted: function() {
        let search = window.location.search;
        
        const initialIndex = search.indexOf("?");

        if (initialIndex > -1) {
            search = search.replace("?", "");
        }

        this.getData({ self: this, newParams: search });

        EventBus.$on("refresh_table_data", data => {
            data["self"] = this;

            this.getData(data);
        });
    },

    methods: {
        ...mapActions(["updateTableData", "toggleSidebarFilter"]),

        getData: ({ newParams, self, url, usePrevious }) => {
            if (self.resultLoaded) {
                self.resultLoaded = false;

                if (usePrevious) {
                    url = self.previousURL;
                } else {
                    url = url ? url : `${self.dataSrc}?${newParams}`;
                    self.previousURL = url;
                }

                if (Object.keys(window.params).length > 0) {
                    Object.keys(window.params).forEach(paramKey => {
                        url += `&${paramKey}=${window.params[paramKey]}`;
                    });
                }

                self.$http
                    .get(url)
                    .then(response => {
                        self.pageLoaded = self.resultLoaded = true;

                        self.updateTableData(response.data);

                        if (newParams || newParams == "") {
                            self.updatedURI(newParams);
                        }
                    })
                    .catch(error => {
                        const actualFilters = self.$store.state.filters.filter(
                            filter =>
                                ! (
                                    filter.column == "view_type" &&
                                    filter.val == "table"
                                )
                        );

                        if (
                            error.response.status == 500 &&
                            actualFilters.length > 0
                        ) {
                            self.$store.state.filters = self.$store.state.filters.filter(
                                filter =>
                                    filter.column == "view_type" &&
                                    filter.val == "table"
                            );
                            self.toggleSidebarFilter();

                            self.addFlashMessages({
                                type: "error",
                                message: error?.response?.data?.message
                            });
                        }

                        self.pageLoaded = self.resultLoaded = true;
                    });
            }
        },

        updatedURI: function(params) {
            let newURL =
                window.location.origin +
                window.location.pathname +
                `${params != "" ? "?" + params : ""}`;

            window.history.pushState({ path: newURL }, "", newURL);
        }
    }
};
</script>
