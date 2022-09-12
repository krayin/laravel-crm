<template>
    <div class="grid-container">
        <div class="datagrid-filters" id="datagrid-filters">
            <!-- searchbox and mass action -->
            <div>
                <!-- searchbox and filters section -->
                <div
                    class="search-filter form-group"
                    v-if="tableData.enableSearch"
                >
                    <i class="icon search-icon input-search-icon"></i>

                    <input
                        type="search"
                        class="control"
                        id="search-field"
                        v-model="searchValue"
                        :placeholder="__('ui.datagrid.search')"
                        @keyup="searchCollection(searchValue)"
                    />
                </div>

                <!-- mass actions section -->
                <div
                    class="mass-actions form-group"
                    v-if="selectedTableRows.length > 0"
                >
                    <select
                        name="mass_action"
                        class="control"
                        v-model="massActionValue"
                        v-validate="'required'"
                    >
                        <option value="NA" disbaled="disbaled">
                            {{ __("ui.datagrid.massaction.select_action") }}
                        </option>

                        <option
                            :value="massAction"
                            :key="index"
                            v-for="(massAction, index) in tableData.massActions"
                        >
                            {{ massAction.label }}
                        </option>
                    </select>

                    <select
                        class="control"
                        v-model="massActionOptionValue"
                        name="update-options"
                        v-validate="'required'"
                        v-if="massActionValue.type == 'update'"
                    >
                        <option value="NA" disbaled="disbaled">
                            {{ __("ui.datagrid.massaction.select_action") }}
                        </option>

                        <option
                            :key="key"
                            v-for="(option, key) in massActionValue.options"
                            :value="option"
                        >
                            {{ key }}
                        </option>
                    </select>

                    <button
                        type="button"
                        class="btn btn-sm btn-primary"
                        @click="onSubmit"
                    >
                        {{ __("ui.datagrid.submit") }}
                    </button>
                </div>
            </div>

            <!-- items per page, pagination and filter section -->
            <div class="filter-right">
                <div
                    class="dropdown-filters per-page"
                    v-if="tableData.enablePerPage"
                >
                    <div class="form-group">
                        <label class="per-page-label" for="perPage">
                            {{ __("ui.datagrid.items-per-page") }}
                        </label>

                        <select
                            id="perPage"
                            name="perPage"
                            class="control"
                            v-model="perPage"
                            @change="paginate"
                        >
                            <option value="10">10</option>
                            <option value="20">20</option>
                            <option value="30">30</option>
                            <option value="40">40</option>
                            <option value="50">50</option>
                        </select>
                    </div>
                </div>

                <pagination-component
                    tab-view="true"
                    :per-page="perPage"
                    v-if="! tableData.tabFilters.length > 0"
                ></pagination-component>

                <slot name="extra-filters"></slot>

                <div
                    class="filter-btn"
                    v-if="tableData.enableFilters"
                    style="display: inline-block"
                >
                    <div
                        class="grid-dropdown-header"
                        @click="toggleSidebarFilter"
                    >
                        <span class="name">
                            {{ __("ui.datagrid.filter.title") }}
                        </span>

                        <i class="icon add-icon"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- tabs section -->
        <div class="tabs-container" v-if="tableData.tabFilters.length > 0">
            <tabs
                event-value-key="value"
                event-key="updateFilter"
                :tabs-collection="tableData.tabFilters[0].values"
                :class="`${tableData.tabFilters[0].type} tabs-left-container`"
                :event-data="{
                    key: tableData.tabFilters[0].key,
                    cond: tableData.tabFilters[0].condition
                }"
                v-if="tableData.tabFilters && tableData.tabFilters[0]"
            ></tabs>

            <div v-else></div>

            <div class="tabs-right-container">
                <section>
                    <pagination-component
                        tab-view="true"
                        :per-page="perPage"
                    ></pagination-component>
                </section>

                <tabs
                    event-value-key="value"
                    event-key="updateFilter"
                    :class="`${tableData.tabFilters[1].type}`"
                    :tabs-collection="tableData.tabFilters[1].values"
                    v-if="tableData.tabFilters && tableData.tabFilters[1]"
                    :event-data="{
                        key: tableData.tabFilters[1].key,
                        cond: tableData.tabFilters[1].condition
                    }"
                ></tabs>

                <div class="custom-design-container dropdown-list">
                    <label>
                        {{ __("ui.datagrid.filter.date_range") }}
                    </label>

                    <i
                        class="icon close-icon"
                        data-close-container="true"
                        @click="$store.state.customTabFilter = false"
                    ></i>

                    <date-range-basic
                        date-range-key="duration"
                        :start-date="custom_filter[0]"
                        :end-date="custom_filter[1]"
                        @onChange="changeDateRange($event)"
                    ></date-range-basic>

                    <button
                        type="button"
                        data-close-container="true"
                        class="btn btn-sm btn-primary"
                        @click="applyCustomFilter"
                    >
                        {{ __("ui.datagrid.filter.done") }}
                    </button>
                </div>
            </div>
        </div>

        <!-- applied filters section -->
        <div class="filtered-tags" v-if="filters.length > 0">
            <template v-for="(filter, index) in filters">
                <div
                    :key="index"
                    class="filter-tag"
                    v-if="ignoreDisplayFilter.indexOf(filter.column) == -1"
                >
                    <span>{{ __("ui.datagrid.filter." + filter.column) || filter.prettyColumn }}</span>

                    <span class="wrapper">
                        {{
                            filter.prettyValue || decodeURIComponent(filter.val)
                        }}
                        <i
                            class="icon close-icon"
                            @click="removeFilter(filter)"
                        ></i>
                    </span>
                </div>
            </template>
        </div>
    </div>
</template>

<script>
import { mapState, mapActions } from "vuex";

export default {
    props: ["tabs"],

    data: function() {
        return {
            type: null,

            filters: [],

            perPage: 10,

            debounce: {},

            sortAsc: "asc",

            searchValue: "",

            sortDesc: "desc",

            stringValue: null,

            booleanValue: null,

            massActionValue: "NA",

            sidebarFilter: false,

            stringCondition: null,

            numberCondition: null,

            booleanCondition: null,

            datetimeCondition: null,

            massActionOptionValue: "NA",

            custom_filter: [null, null],

            url: new URL(window.location.href),

            ignoreDisplayFilter: ["view_type"]
        };
    },

    computed: {
        ...mapState({
            tableData: state => state.tableData,

            customTabFilter: state => state.customTabFilter,

            selectedTableRows: state => state.selectedTableRows
        }),

        columns: function() {
            return this.tableData.columns;
        },

        customTabFilters: function() {
            return this.tableData.customTabFilters;
        }
    },

    watch: {
        filters: function(newValue, oldValue) {
            this.$store.state.filters = newValue;

            let duration = newValue.find(filter => filter.column == "duration");

            if (duration) {
                duration = duration.val.split(",");

                let timestamp = Date.parse(duration[0]);

                if (isNaN(timestamp) == false) {
                    this.custom_filter = duration;
                }
            }
        },

        "$store.state.filters": function(newValue, oldValue) {
            this.filters = newValue;

            if (this.filters.length == 0) {
                this.custom_filter = [null, null];
            }

            this.makeURL();
        },

        "$store.state.tableData.columns": function(newValue, oldValue) {
            if (newValue.length != oldValue.length) {
                this.updateFilterValue();
            }
        }
    },

    mounted: function() {
        this.setParamsAndUrl();

        if (this.filters.length) {
            for (let i = 0; i < this.filters.length; i++) {
                if (this.filters[i].column === "perPage") {
                    this.perPage = this.filters[i].val;
                }
            }
        }

        $("body").click(event => {
            if (
                ((typeof event.target.className == "string" &&
                    event.target.className?.includes(
                        "custom-design-container"
                    )) ||
                    $(event.target).parents(".flatpickr-calendar").length ||
                    $(event.target).parents(".custom-design-container")
                        .length) &&
                ! $(event.target).attr("data-close-container")
            ) {
                event.stopPropagation();
            }
        });

        EventBus.$on("updateFilter", data => {
            if (
                this.customTabFilters.includes(data.key) &&
                data.value == "custom"
            ) {
                setTimeout(() => {
                    $(".custom-design-container").toggle();
                });
            } else {
                this.updateFilter(data);

                this.$store.state.customTabFilter = false;
            }
        });
    },

    methods: {
        ...mapActions(["toggleSidebarFilter", "selectAllRows"]),

        nullify: function() {
            this.stringCondition = null;
            this.datetimeCondition = null;
            this.booleanCondition = null;
            this.numberCondition = null;
        },

        searchCollection: function(searchValue) {
            clearTimeout(this.debounce["search"]);

            let sanitizedSearchValue = searchValue.trim();

            this.debounce["search"] = setTimeout(() => {
                this.formURL("search", "all", sanitizedSearchValue, "Search");
            }, 1000);
        },

        setParamsAndUrl: function() {
            let params = new URL(window.location.href).search;

            if (params.slice(1, params.length).length > 0) {
                this.arrayFromUrl();
            }

            this.setActiveTabs();
        },

        formURL: function(column, condition, response, label) {
            let obj = {};

            if (
                column === "" ||
                condition === "" ||
                response === "" ||
                column === null ||
                condition === null ||
                response === null
            ) {
                this.updateFilter({
                    key: column,
                    value: ""
                });

                return false;
            } else {
                if (this.filters.length > 0) {
                    if (column !== "sort" && column !== "search") {
                        let filterRepeated = false;

                        for (let j = 0; j < this.filters.length; j++) {
                            if (this.filters[j].column === column) {
                                if (
                                    this.filters[j].cond === condition &&
                                    this.filters[j].val === response
                                ) {
                                    filterRepeated = true;

                                    return false;
                                } else if (
                                    this.filters[j].cond === condition &&
                                    this.filters[j].val !== response
                                ) {
                                    filterRepeated = true;

                                    this.filters[j].val = response;

                                    this.makeURL();
                                }
                            }
                        }

                        if (filterRepeated === false) {
                            obj.column = column;
                            obj.cond = condition;
                            obj.val = response;
                            obj.label = label;

                            this.filters.push(obj);
                            obj = {};

                            this.makeURL();
                        }
                    }

                    if (column === "search") {
                        let search_found = false;

                        for (let j = 0; j < this.filters.length; j++) {
                            if (this.filters[j].column === "search") {
                                this.filters[j].column = column;
                                this.filters[j].cond = condition;
                                this.filters[j].val = encodeURIComponent(
                                    response
                                );
                                this.filters[j].label = label;

                                this.makeURL();
                            }
                        }

                        for (let j = 0; j < this.filters.length; j++) {
                            if (this.filters[j].column === "search") {
                                search_found = true;
                            }
                        }

                        if (search_found === false) {
                            obj.column = column;
                            obj.cond = condition;
                            obj.val = encodeURIComponent(response);
                            obj.label = label;

                            this.filters.push(obj);

                            obj = {};

                            this.makeURL();
                        }
                    }
                } else {
                    obj.column = column;
                    obj.cond = condition;
                    obj.val = encodeURIComponent(response);
                    obj.label = label;

                    this.filters.push(obj);

                    obj = {};

                    this.makeURL();
                }
            }
        },

        makeURL: function() {
            let newParams = "";

            for (let i = 0; i < this.filters.length; i++) {
                if (
                    this.filters[i].column == "status" ||
                    this.filters[i].column == "value_per_locale" ||
                    this.filters[i].column == "value_per_channel" ||
                    this.filters[i].column == "is_unique"
                ) {
                    if (this.filters[i].val.includes("True")) {
                        this.filters[i].val = 1;
                    } else if (this.filters[i].val.includes("False")) {
                        this.filters[i].val = 0;
                    }
                }

                let condition = "";
                if (this.filters[i].cond !== undefined) {
                    condition = "[" + this.filters[i].cond + "]";
                }

                if (i == 0) {
                    newParams =
                        this.filters[i].column +
                        condition +
                        "=" +
                        this.filters[i].val;
                } else {
                    newParams =
                        newParams +
                        "&" +
                        this.filters[i].column +
                        condition +
                        "=" +
                        this.filters[i].val;
                }
            }

            EventBus.$emit("refresh_table_data", {
                newParams
            });
        },

        arrayFromUrl: function() {
            let obj = {};
            const processedUrl = this.url.search.slice(1, this.url.length);
            let splitted = [];
            let moreSplitted = [];

            splitted = processedUrl.split("&");

            for (let i = 0; i < splitted.length; i++) {
                moreSplitted.push(splitted[i].split("="));
            }

            for (let i = 0; i < moreSplitted.length; i++) {
                const key = decodeURI(moreSplitted[i][0]);
                let value = decodeURI(moreSplitted[i][1]);

                if (value.includes("+")) {
                    value = value.replace("+", " ");
                }

                obj.column = key.replace("]", "").split("[")[0];
                obj.cond = key.replace("]", "").split("[")[1];

                obj.val = value;

                if (obj?.column?.replaceAll) {
                    obj.prettyColumn = `${obj.column.replaceAll("_", " ")}`;
                }

                switch (obj.column) {
                    case "search":
                        obj.label = "Search";
                        break;

                    case "sort":
                        obj.prettyValue = `${obj.cond.replaceAll("_", " ")} - ${
                            obj.val
                        }`;
                        break;

                    default:
                        break;
                }

                if (obj.cond == "bw") {
                    let timestamp = Date.parse(obj.val.split(",")[0]);

                    if (isNaN(timestamp) == false) {
                        obj.prettyValue = `${obj.val.replaceAll(",", " - ")}`;
                    }
                }

                if (obj.column !== undefined && obj.val !== undefined) {
                    this.filters.push(obj);
                }

                obj = {};
            }
        },

        paginate: function(e) {
            for (let i = 0; i < this.filters.length; i++) {
                if (this.filters[i].column == "perPage") {
                    this.filters.splice(i, 1);
                }
            }

            this.filters.push({
                column: "perPage",
                cond: "eq",
                val: e.target.value
            });

            this.makeURL();
        },

        updateFilter: function({ key, value, cond }) {
            this.filters = this.filters.filter(filter => filter.column != key);

            if (value && value != "" && value != ",") {
                let data = {
                    column: key,
                    val: value
                };

                if (data?.column?.replaceAll) {
                    data.prettyColumn = `${data.column.replaceAll("_", " ")}`;
                }

                if (cond) {
                    data["cond"] = cond;
                }

                if (key == "sort") {
                    data.prettyValue = `${data.cond.replaceAll("_", " ")} - ${
                        data.val
                    }`;
                } else if (data.cond == "bw") {
                    let timestamp = Date.parse(data.val.split(",")[0]);

                    if (isNaN(timestamp) == false) {
                        data.prettyValue = `${data.val.replaceAll(",", " - ")}`;
                    }
                }

                this.filters.push(data);

                this.updateFilterValue();
            }

            this.makeURL();
        },

        setActiveTabs: function() {
            let defaultSelectedIndex = [];

            for (const index in this.tableData.tabFilters) {
                for (const tabValueIndex in this.tableData.tabFilters[index]
                    .values) {
                    if (
                        this.tableData.tabFilters[index].values[tabValueIndex]
                            .isActive
                    ) {
                        defaultSelectedIndex[index] = tabValueIndex;
                    }

                    this.tableData.tabFilters[index].values[
                        tabValueIndex
                    ].isActive = false;
                }
            }

            for (const index in this.tableData.tabFilters) {
                let applied = false;

                this.filters.forEach(filter => {
                    if (filter.column == this.tableData.tabFilters[index].key) {
                        for (const tabValueIndex in this.tableData.tabFilters[
                            index
                        ].values) {
                            if (
                                this.tableData.tabFilters[index].values[
                                    tabValueIndex
                                ].key == filter.val ||
                                (filter.cond == "bw" &&
                                    this.tableData.tabFilters[index].values[
                                        tabValueIndex
                                    ].key == "custom")
                            ) {
                                applied = true;
                                this.tableData.tabFilters[index].values[
                                    tabValueIndex
                                ].isActive = true;
                            }
                        }
                    }
                });

                if (! applied) {
                    this.tableData.tabFilters[index].values[
                        defaultSelectedIndex[index]
                    ].isActive = true;
                }
            }
        },

        onSubmit: function(event) {
            this.$root.pageLoaded = false;

            this.toggleButtonDisable(true);

            if (! this.massActionValue.action) {
                this.$root.pageLoaded = true;

                this.toggleButtonDisable(false);

                this.addFlashMessages({
                    type: "error",
                    message: this.__("ui.datagrid.mandatory_mass_action")
                });

                return;
            }

            if (this.massActionValue.type !== 'delete' && this.massActionOptionValue === 'NA') {
                this.$root.pageLoaded = true;

                this.toggleButtonDisable(false);

                this.addFlashMessages({
                    type: "error",
                    message: this.__("ui.datagrid.mandatory_mass_action")
                });

                return;
            }

            if (! confirm(this.__("ui.datagrid.massaction.delete"))) {
                this.$root.pageLoaded = true;

                this.toggleButtonDisable(false);

                return;
            }

            this.$validator.validateAll().then(result => {
                if (result) {
                    this.$http[this.massActionValue.method.toLowerCase()](
                        this.massActionValue.action,
                        {
                            rows: this.selectedTableRows,
                            value: this.massActionOptionValue
                        }
                    )
                        .then(response => {
                            this.$root.pageLoaded = true;

                            EventBus.$emit("refresh_table_data", {
                                usePrevious: true
                            });

                            this.selectAllRows(true);

                            this.massActionValue = "NA";
                            this.massActionOptionValue = "NA";

                            this.toggleButtonDisable(false);

                            this.addFlashMessages({
                                type: "success",
                                message: response.data.message
                            });
                        })
                        .catch(error => {
                            this.$root.pageLoaded = true;

                            this.toggleButtonDisable(false);

                            this.addFlashMessages({
                                type: "error",
                                message: error.response.data.message
                            });
                        });
                } else {
                    this.$root.pageLoaded = true;
                    
                    this.toggleButtonDisable(false);

                    EventBus.$emit("onFormError");
                }
            });
        },

        changeDateRange: function(event) {
            this.custom_filter = event;
        },

        applyCustomFilter: function() {
            if (this.custom_filter[0] && this.custom_filter[1]) {
                let data = {
                    cond: "bw",
                    key: "duration",
                    value: `${this.custom_filter[0]},${this.custom_filter[1]}`
                };

                this.updateFilter(data);
            }

            this.$store.state.customTabFilter = false;
        },

        removeFilter: function(filter) {
            for (let index in this.filters) {
                if (
                    this.filters[index].column === filter.column &&
                    this.filters[index].cond === filter.cond &&
                    this.filters[index].val === filter.val
                ) {
                    if (this.filters[index].column == "perPage") {
                        this.perPage = "10";
                    }

                    if (this.filters[index].column == "duration") {
                        let key = "duration";

                        let specificRangeDiv = document.querySelector(`#dateRange${key}`);

                        let datePickers = specificRangeDiv.querySelectorAll('.flatpickr-input');

                        datePickers.forEach((datePicker) => {
                            let fp = datePicker._flatpickr;

                            fp.set('minDate', '');

                            fp.set('maxDate', '');
                        });

                        $(datePickers).val('');
                    }

                    this.filters.splice(index, 1);

                    this.makeURL();
                }
            }
        },

        updateFilterValue: function() {
            let allFilter = this.filters.filter(filter => filter.val === "all");

            if (allFilter.length > 0) {
                let viewType =
                    this.filters.find(
                        filter => filter.column === "view_type"
                    ) ?? false;

                if (viewType) {
                    allFilter.push(viewType);
                }

                this.filters = this.generateNewFilters(allFilter);
            } else {
                let otherFilters = this.filters.filter(
                    filter => filter.val !== "all"
                );

                this.filters = this.generateNewFilters(otherFilters);
            }
        },

        generateNewFilters: function(filters) {
            return filters.map(filter => {
                let column = this.$store.state.tableData.columns.find(
                    column => column.index == filter.column
                );

                if (column?.dropdown_options) {
                    let value = filter.val.split(",");
                    value = value.map(
                        id =>
                            column.dropdown_options.find(
                                option => option.value == id
                            ).label
                    );
                    filter.prettyValue = value.join(",");
                }

                return filter;
            });
        }
    }
};
</script>
