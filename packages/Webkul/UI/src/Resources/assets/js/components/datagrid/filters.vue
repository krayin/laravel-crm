<template>
    <div class="grid-container">
        <!-- searchbox and filters section -->
        <div class="datagrid-filters" id="datagrid-filters">
            <div class="filter-left">
                <div class="search-filter form-group">
                    <input
                        type="text"
                        class="control"
                        id="search-field"
                        v-model="searchValue"
                        :placeholder="__('ui.datagrid.search')"
                        @keyup="searchCollection(searchValue)"
                    />
                </div>
            </div>

            <div class="filter-right">
                <div class="dropdown-filters per-page">
                    <div class="form-group">
                        <label class="per-page-label" for="perPage">
                            {{ __('ui.datagrid.items-per-page') }}
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

                <div class="filter-btn">
                    <div class="grid-dropdown-header" @click="toggleSidebarFilter">
                        <span class="name">
                            {{ __('ui.datagrid.filter.title') }}
                        </span>

                        <i class="icon add-icon"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- applied filters section -->
        <div class="filtered-tags">
            <template v-for="(filter, index) in filters">
                <div
                    :key="index"
                    class="filter-tag"
                    style="text-transform: capitalize;"
                    v-if="ignoreDisplayFilter.indexOf(filter.column) == -1"
                >
                    <span v-text="filter.column"></span>

                    <span class="wrapper">
                        {{ filter.prettyValue ? filter.prettyValue : decodeURIComponent(filter.val) }}
                        <i class="icon close-icon" @click="removeFilter(filter)"></i>
                    </span>
                </div>
            </template>
        </div>

        <!-- filters section -->
        <template>
            <tabs
                event-value-key="value"
                event-key="update_filter"
                class="pill d-inline-block"
                :tabs-collection="tabs.type"
                :event-data="{key: 'type', 'cond' : 'eq'}"
            ></tabs>

            <div class="tabs-right-container">
                <section>
                    <pagination-component :pagination-data="paginationData" tab-view="true" :per-page="perPage"></pagination-component>
                </section>

                <tabs
                    event-value-key="value"
                    event-key="update_filter"
                    class="group d-inline-block"
                    :tabs-collection="tabs.duration"
                    :event-data="{key: 'duration', 'cond' : 'eq'}"
                ></tabs>
            </div>
        </template>

        <!-- mass actions section -->
        <div class="mass-actions form-group" v-if="selectedTableRows.length > 0">
            <select name="mass_action" class="control" v-model="massActionValue" v-validate="'required'">
                <option :value="massAction" :key="index" v-for="(massAction, index) in tableData.massactions">
                    {{ massAction.label }}
                </option>
            </select>

            <select class="control" v-model="massActionOptionValue" name="update-options" v-validate="'required'" v-if="massActionValue.type == 'update'">
                <option :key="key" v-for="(option, key) in massActionValue.options" :value="option">
                    {{ key }}
                </option>
            </select>

            <button type="button" class="badge badge-md badge-primary" @click="onSubmit">
                {{ __('ui.datagrid.submit') }}
            </button>
        </div>
    </div>
</template>

<script>
    import { mapState, mapActions } from 'vuex';

    export default {
        props: [
            'results',
            'tabs',
            'paginationData',
        ],

        data: function () {
            return {
                massActionValue: {},
                massActionOptionValue: null,
                url: new URL(window.location.href),
                currentSort: null,
                sortDesc: 'desc',
                sortAsc: 'asc',
                searchValue: '',
                filters: [],
                type: null,
                columns: this.results['columns'],
                stringCondition: null,
                booleanCondition: null,
                numberCondition: null,
                datetimeCondition: null,
                stringValue: null,
                booleanValue: null,
                perPage: 10,
                extraFilters: this.results['extraFilters'],
                debounce: {},
                ignoreDisplayFilter: ['duration', 'type'],
                sidebarFilter: false,
            }
        },

        computed: {
            ...mapState({
                tableData : state => state.tableData,
                selectedTableRows : state => state.selectedTableRows,
            }),
        },

        mounted: function () {
            this.setParamsAndUrl();

            if (this.filters.length) {
                for (let i = 0; i < this.filters.length; i++) {
                    if (this.filters[i].column === 'perPage') {
                        this.perPage = this.filters[i].val;
                    }
                }
            }

            EventBus.$on('update_filter', data => {
                this.updateFilter(data);
            });
        },

        methods: {
            ...mapActions([
                'toggleSidebarFilter',
                'selectAllRows'
            ]),

            nullify: function () {
                this.stringCondition = null;
                this.datetimeCondition = null;
                this.booleanCondition = null;
                this.numberCondition = null;
            },

            sortCollection: function (alias) {
                let label = '';

                for (let colIndex in this.columns) {
                    if (this.columns[colIndex].index === alias) {
                        matched = 0;
                        label = this.columns[colIndex].label;
                        break;
                    }
                }

                this.formURL("sort", alias, this.sortAsc, label);
            },

            searchCollection: function (searchValue) {
                clearTimeout(this.debounce['search']);

                this.debounce['search'] = setTimeout(() => {
                    this.formURL("search", 'all', searchValue, 'Search');
                }, 1000);
            },

            // function triggered to check whether the query exists or not and then call the make filters from the url
            setParamsAndUrl: function () {
                var params = (new URL(window.location.href)).search;

                if (params.slice(1, params.length).length > 0) {
                    this.arrayFromUrl();
                }

                this.setActiveTabs();
            },

            findCurrentSort: function () {
                for (let i in this.filters) {
                    if (this.filters[i].column === 'sort') {
                        this.currentSort = this.filters[i].val;
                    }
                }
            },

            //make array of filters, sort and search
            formURL: function (column, condition, response, label) {
                var obj = {};

                if (
                    column === ""
                    || condition === ""
                    || response === ""
                    || column === null
                    || condition === null
                    || response === null
                ) {
                    alert(this.__('ui.datagrid.filter-fields-missing'));

                    return false;
                } else {

                    if (this.filters.length > 0) {
                        if (column !== "sort" && column !== "search") {
                            let filterRepeated = false;

                            for (let j = 0; j < this.filters.length; j++) {
                                if (this.filters[j].column === column) {
                                    if (this.filters[j].cond === condition && this.filters[j].val === response) {
                                        filterRepeated = true;

                                        return false;
                                    } else if (this.filters[j].cond === condition && this.filters[j].val !== response) {
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

                        if (column === "sort") {
                            let sort_exists = false;

                            for (let j = 0; j < this.filters.length; j++) {
                                if (this.filters[j].column === "sort") {
                                    if (this.filters[j].column === column && this.filters[j].cond === condition) {
                                        this.findCurrentSort();

                                        if (this.currentSort === "asc") {
                                            this.filters[j].column = column;
                                            this.filters[j].cond = condition;
                                            this.filters[j].val = this.sortDesc;

                                            this.makeURL();
                                        } else {
                                            this.filters[j].column = column;
                                            this.filters[j].cond = condition;
                                            this.filters[j].val = this.sortAsc;

                                            this.makeURL();
                                        }
                                    } else {
                                        this.filters[j].column = column;
                                        this.filters[j].cond = condition;
                                        this.filters[j].val = response;
                                        this.filters[j].label = label;

                                        this.makeURL();
                                    }

                                    sort_exists = true;
                                }
                            }

                            if (sort_exists === false) {
                                if (this.currentSort === null)
                                    this.currentSort = this.sortAsc;

                                obj.column = column;
                                obj.cond = condition;
                                obj.val = this.currentSort;
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
                                    this.filters[j].val = encodeURIComponent(response);
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

            // make the url from the array and redirect
            makeURL: function () {
                var newParams = '';

                for(let i = 0; i < this.filters.length; i++) {
                    if (this.filters[i].column == 'status' || this.filters[i].column == 'value_per_locale' || this.filters[i].column == 'value_per_channel' || this.filters[i].column == 'is_unique') {
                        if (this.filters[i].val.includes("True")) {
                            this.filters[i].val = 1;
                        } else if (this.filters[i].val.includes("False")) {
                            this.filters[i].val = 0;
                        }
                    }

                    let condition = '';
                    if (this.filters[i].cond !== undefined) {
                        condition = '[' + this.filters[i].cond + ']';
                    }

                    if (i == 0) {
                        newParams = this.filters[i].column + condition + '=' + this.filters[i].val;
                    } else {
                        newParams = newParams + '&' + this.filters[i].column + condition + '=' + this.filters[i].val;
                    }
                }

                var uri = window.location.href.toString();

                var clean_uri = uri.substring(0, uri.indexOf("?")).trim();

                EventBus.$emit('refresh_table_data', {
                    newParams,
                    clean_uri
                });
            },

            //make the filter array from url after being redirected
            arrayFromUrl: function () {

                let obj = {};
                const processedUrl = this.url.search.slice(1, this.url.length);
                let splitted = [];
                let moreSplitted = [];

                splitted = processedUrl.split('&');

                for (let i = 0; i < splitted.length; i++) {
                    moreSplitted.push(splitted[i].split('='));
                }

                for (let i = 0; i < moreSplitted.length; i++) {
                    const key = decodeURI(moreSplitted[i][0]);
                    let value = decodeURI(moreSplitted[i][1]);

                    if (value.includes('+')) {
                        value = value.replace('+', ' ');
                    }

                    obj.column = key.replace(']', '').split('[')[0];
                    obj.cond = key.replace(']', '').split('[')[1]
                    obj.val = value;

                    switch (obj.column) {
                        case "search":
                            obj.label = "Search";
                            break;
                        case "channel":
                            obj.label = "Channel";
                            if ('channels' in this.extraFilters) {
                                obj.prettyValue = this.extraFilters['channels'].find(channel => channel.id == obj.val).name
                            }
                            break;
                        case "locale":
                            obj.label = "Locale";
                            if ('locales' in this.extraFilters) {
                                obj.prettyValue = this.extraFilters['locales'].find(locale => locale.code === obj.val).name
                            }
                            break;
                        case "customer_group":
                            obj.label = "Customer Group";
                            if ('customer_groups' in this.extraFilters) {
                                obj.prettyValue = this.extraFilters['customer_groups'].find(customer_group => customer_group.id === parseInt(obj.val, 10)).name
                            }
                            break;
                        case "sort":
                            for (let colIndex in this.columns) {
                                if (this.columns[colIndex].index === obj.cond) {
                                    obj.label = this.columns[colIndex].label;
                                    break;
                                }
                            }
                            break;
                        default:
                            for (let colIndex in this.columns) {
                                if (this.columns[colIndex].index === obj.column) {
                                    obj.label = this.columns[colIndex].label;

                                    if (this.columns[colIndex].type === 'boolean') {
                                        if (obj.val === '1') {
                                            obj.val = this.__('ui::app.datagrid.true');
                                        } else {
                                            obj.val = this.__('ui::app.datagrid.false');
                                        }
                                    }
                                }
                            }
                            break;
                    }

                    if (obj.column !== undefined && obj.val !== undefined) {
                        this.filters.push(obj);
                    }

                    obj = {};
                }
            },

            paginate: function (e) {
                for (let i = 0; i < this.filters.length; i++) {
                    if (this.filters[i].column == 'perPage') {
                        this.filters.splice(i, 1);
                    }
                }

                this.filters.push({"column": "perPage", "cond": "eq", "val": e.target.value});

                this.makeURL();
            },

            updateFilter: function ({key, value, cond}) {
                this.filters = this.filters.filter(filter => {
                    if (filter.column == key) {
                        return false;
                    }

                    return true;
                });

                let data = {
                    "column": key,
                    "val": value
                }

                if (cond) {
                    data['cond'] = cond;
                }

                this.filters.push(data);

                this.makeURL();
            },

            setActiveTabs: function (column) {
                for (const index in this.tabs) {
                    for (const tabValueIndex in this.tabs[index]) {
                        this.tabs[index][tabValueIndex].isActive = false;
                    }
                }

                for (const index in this.tabs) {
                    var applied = false;

                    this.filters.forEach(filter => {
                        if (filter.column == index) {
                            for (const tabValueIndex in this.tabs[index]) {
                                if (this.tabs[index][tabValueIndex].key == filter.val) {
                                    applied = true;
                                    this.tabs[index][tabValueIndex].isActive = true;
                                }
                            }
                        }
                    });

                    if (! applied) { 
                        this.tabs[index][0].isActive = true;
                    }
                }
            },

            onSubmit: function (event) {
                this.$root.toggleButtonDisable(true);

                this.$validator.validateAll()
                    .then(result => {
                        if (result) {
                            this.$http[this.massActionValue.method.toLowerCase()](this.massActionValue.action, {
                                rows: this.selectedTableRows,
                                value: this.massActionOptionValue
                            })
                                .then(response => {
                                    EventBus.$emit('refresh_table_data', {usePrevious: true});

                                    this.selectAllRows(false);

                                    this.$root.toggleButtonDisable(false);
                                })
                                .catch(error => {
                                    this.$root.toggleButtonDisable(false);
                                });
                        } else {
                            this.$root.toggleButtonDisable(false);

                            EventBus.$emit('onFormError')
                        }
                    });
            },
        }
    };
</script>