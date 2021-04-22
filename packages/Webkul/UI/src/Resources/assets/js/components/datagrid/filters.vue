<template>
    <div class="grid-container">
        <!-- searchbox and filters section -->
        <div class="datagrid-filters" id="datagrid-filters">
            <div class="filter-left">
                <div class="search-filter form-group" v-if="tableData.enableSearch">
                    <input
                        type="search"
                        class="control"
                        id="search-field"
                        v-model="searchValue"
                        :placeholder="__('ui.datagrid.search')"
                        @keyup="searchCollection(searchValue)"
                    />
                </div>
            </div>

            <div class="filter-right">
                <div class="dropdown-filters per-page" v-if="tableData.enablePerPage">
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

                <div class="filter-btn" v-if="tableData.enableFilters">
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
                :tabs-collection="tableData.tabFilters[0].values"
                v-if="tableData.tabFilters && tableData.tabFilters[0]"
                :class="`${tableData.tabFilters[0].type} d-inline-block`"
                :event-data="{key: tableData.tabFilters[0].key, 'cond' : tableData.tabFilters[0].condition}"
            ></tabs>

            <div class="tabs-right-container">
                <section>
                    <pagination-component tab-view="true" :per-page="perPage"></pagination-component>
                </section>

                <tabs
                    event-value-key="value"
                    event-key="update_filter"
                    :tabs-collection="tableData.tabFilters[1].values"
                    v-if="tableData.tabFilters && tableData.tabFilters[1]"
                    :class="`${tableData.tabFilters[1].type} d-inline-block`"
                    :event-data="{key: tableData.tabFilters[1].key, 'cond' : tableData.tabFilters[1].condition}"
                ></tabs>

                <div class="custom-design-container" v-if="customTabFilter">
                    <label>
                        {{ __('ui.datagrid.filter.date_range') }}
                    </label>

                    <div class="control-group date">
                        <date>
                            <input
                                type="text"
                                class="control half"
                                placeholder="Start Date"
                                v-model="custom_filter[0]"
                            />
                        </date>

                        <span class="middle-text">{{ __('ui.datagrid.filter.to') }}</span>
                        
                        <date>
                            <input
                                type="text"
                                class="control half"
                                placeholder="End Date"
                                v-model="custom_filter[1]"
                            />
                        </date>
                    </div>

                    <button type="button" class="btn btn-sm btn-primary" @click="applyCustomFilter">
                        {{ __('ui.datagrid.filter.done') }}
                    </button>
                </div>
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
        data: function () {
            return {
                massActionValue: {},
                massActionOptionValue: null,
                url: new URL(window.location.href),
                sortDesc: 'desc',
                sortAsc: 'asc',
                searchValue: '',
                filters: [],
                type: null,
                stringCondition: null,
                booleanCondition: null,
                numberCondition: null,
                datetimeCondition: null,
                stringValue: null,
                booleanValue: null,
                perPage: 10,
                debounce: {},
                ignoreDisplayFilter: ['duration', 'type'],
                sidebarFilter: false,
                custom_filter: [null, null],
                customTabFilter: false,
            }
        },

        computed: {
            ...mapState({                
                tableData : state => state.tableData,
                customTabFilter : state => state.customTabFilter,
                selectedTableRows : state => state.selectedTableRows,
            }),

            columns: function () {
                return this.tableData.columns;
            },

            extraFilters: function () {
                return this.tableData.extraFilters;
            },
        },

        watch: {
            filters: function (newValue, oldValue) {
                this.$store.state.filters = newValue;
            },

            '$store.state.filters': function (newValue, oldValue) {
                this.filters = newValue;

                this.makeURL();
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
                if (data.value == 'custom') {
                    this.$store.state.customTabFilter = ! this.$store.state.customTabFilter;
                } else {
                    this.updateFilter(data);
                }
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

                for (let i = 0; i < this.filters.length; i++) {
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

                EventBus.$emit('refresh_table_data', {
                    newParams,
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
                this.filters = this.filters.filter(filter => filter.column != key);

                if (value != "" && value != ",") {
                    let data = {
                        "column": key,
                        "val"   : value
                    }
    
                    if (cond) {
                        data['cond'] = cond;
                    }
    
                    this.filters.push(data);
                }

                this.makeURL();
            },

            setActiveTabs: function () {
                var defaultSelectrdIndex = [];

                for (const index in this.tableData.tabFilters) {
                    for (const tabValueIndex in this.tableData.tabFilters[index].values) {
                        if (this.tableData.tabFilters[index].values[tabValueIndex].isActive) {
                            defaultSelectrdIndex[index] = tabValueIndex;
                        }

                        this.tableData.tabFilters[index].values[tabValueIndex].isActive = false;
                    }
                }

                for (const index in this.tableData.tabFilters) {
                    var applied = false;

                    this.filters.forEach(filter => {
                        if (filter.column == this.tableData.tabFilters[index].key) {
                            for (const tabValueIndex in this.tableData.tabFilters[index].values) {
                                if (
                                    (this.tableData.tabFilters[index].values[tabValueIndex].key == filter.val)
                                    || (
                                        filter.cond == "bw"
                                        && this.tableData.tabFilters[index].values[tabValueIndex].key == "custom"
                                    )
                                ) {
                                    applied = true;
                                    this.tableData.tabFilters[index].values[tabValueIndex].isActive = true;
                                }
                            }
                        }
                    });

                    if (! applied) {
                        this.tableData.tabFilters[index].values[defaultSelectrdIndex[index]].isActive = true;
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

                                    this.massActionValue = {};
                                    this.massActionOptionValue = null;

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

            applyCustomFilter: function () {
                if (this.custom_filter[0] && this.custom_filter[1]) {
                    var data = {
                        cond: 'bw',
                        key: 'duration',
                        value: `${this.custom_filter[0]},${this.custom_filter[1]}`
                    }
                    
                    this.updateFilter(data);

                    this.$store.state.customTabFilter = false;
                }
            },

            removeFilter: function (filter) {
                for (let i in this.filters) {
                    if (this.filters[i].column === filter.column
                        && this.filters[i].cond === filter.cond
                        && this.filters[i].val === filter.val) {
                        this.filters.splice(i, 1);

                        this.makeURL();
                    }
                }
            },
        }
    };
</script>