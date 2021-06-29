<template>
    <div :class="`sidebar-filter`">
        <header>
            <h1>
                <span>{{ __('ui.datagrid.filter.title') }}</span>

                <div class="right">
                    <label @click="removeAll">{{ __('ui.datagrid.filter.remove_all') }}</label>

                    <i class="icon close-icon" @click="toggleSidebarFilter"></i>
                </div>
            </h1>
        </header>
        
        <template v-for="(data, key) in (columns || tableData.columns)">
            <div :class="`form-group ${data.filterable_type == 'date_range' ? 'date' : ''}`" :key="key" v-if="data.filterable_type">
                <label>{{ data.label }}</label>

                <div class="field-container">
                    <template v-if="data.filterable_type == 'integer_range'">
                        <input
                            type="text"
                            placeholder="Start"
                            class="control half"
                            v-model="filterData[key].values[0]"
                        />

                        <span class="middle-text">to</span>
                        
                        <input
                            type="text"
                            placeholder="End"
                            class="control half"
                            v-model="filterData[key].values[1]"
                        />
                    </template>

                    <template v-else-if="data.filterable_type == 'add'">
                        <span class="control" @click="toggleInput(data.index)" v-if="! addField[data.index]">
                            <i class="icon add-icon"></i> {{ data.label }}
                        </span>

                        <div class="enter-new" v-else>
                            <input
                                type="text"
                                class="control mb-10"
                                :placeholder="data.label"
                                :id="`enter-new-${data.index}`"
                                @keyup.enter="pushFieldValue(key, $event, data.index)"
                            />
                        </div>
                    </template>

                    <template v-else-if="data.filterable_type == 'date_range'">
                        <date>
                            <input
                                type="text"
                                class="control half"
                                placeholder="Start Date"
                                v-model="data.values[0]"
                                @change="changeDateRange(key, data.values)"
                            />
                        </date>

                        <span class="middle-text">{{ __('ui.datagrid.filter.to') }}</span>
                        
                        <date>
                            <input
                                type="text"
                                class="control half"
                                placeholder="End Date"
                                v-model="data.values[1]"
                                @change="changeDateRange(key, data.values)"
                            />
                        </date>
                    </template>

                    <template v-else-if="data.filterable_type == 'dropdown'">
                        <select class="control" @change="pushFieldValue(key, $event, data.index)">
                            <option value="" disabled selected>
                                {{ data.label }}
                            </option>
                            <option :value="option.value" :key="index" v-for="(option, index) in data.filterable_options">
                                {{ option.label }}
                            </option>
                        </select>
                    </template>

                    <i class="icon close-icon" @click="removeFilter({type: data.filterable_type, key, index: data.index})"></i>

                    <template v-if="data.filterable_type == 'add' || data.filterable_type == 'dropdown'">
                        <div class="selected-options">
                            <span
                                :key="index"
                                v-for="(value, index) in data.values"
                                class="badge badge-md badge-pill badge-secondary"
                            >
                                {{ getFilteredValue(value, data) }}

                                <i class="icon close-icon ml-10" @click="removeFieldValue(key, index, data.index)"></i>
                            </span>
                        </div>
                    </template>
                </div>
            </div>
        </template>
    </div>
</template>

<script>
    import { mapState, mapActions } from 'vuex';

    export default {
        props: ['columns'],

        data: function () {
            return {
                addField: {},
            }
        },

        computed: {
            ...mapState({
                filterData      : state => state.filterData,
                tableData       : state => state.tableData,
                sidebarFilter   : state => state.sidebarFilter,
            }),
        },

        methods: {
            ...mapActions([
                'toggleSidebarFilter',
                'updateFilterValues'
            ]),

            toggleInput: function (key, event) {
                this.addField[key] = ! this.addField[key];

                this.$forceUpdate();

                setTimeout(() => {
                    document.getElementById(`enter-new-${key}`).focus();
                })
            },

            pushFieldValue: function (key, {target}, indexKey) {
                this.addField[indexKey] = false;

                const values = (this.columns || this.tableData.columns)[key].values || [];

                if (values.indexOf(target.value) == -1) {
                    values.push(target.value);
    
                    this.updateFilterValues({
                        key: indexKey,
                        values
                    });
                }

                target.value = "";

                this.$forceUpdate();
            },

            removeFieldValue: function (key, index, indexKey) {
                const values = (this.columns || this.tableData.columns)[key].values;
                values.splice(index, 1);
                
                this.updateFilterValues({
                    key: indexKey,
                    values
                });

                this.$forceUpdate();
            },

            removeAll: function () {
                this.$store.state.filters = this.$store.state.filters.filter(filter => filter.column == 'type' && filter.val == 'table');

                (this.columns || this.tableData.columns).forEach((column, index) => {
                    if (column.filterable_type && column.filterable_type == 'date_range') {
                        this.removeFilter({
                            key     : index,
                            index   : column.index,
                            type    : column.filterable_type,
                        })
                    }
                });

                this.$forceUpdate();
            },

            removeFilter: function ({type, key, index}) {
                if (type == "add" && this.addField[index]) {
                    this.addField[index] = false;
                }

                var values = (this.columns || this.tableData.columns)[key].values;
                values = "";
                
                this.updateFilterValues({
                    key,
                    values
                });

                this.$forceUpdate();
            },

            changeDateRange: function (key, values) {
                setTimeout(() => {
                    this.updateFilterValues({key, values, condition: 'bw'})
                }, 0);
            },

            getFilteredValue: function (value, data) {
                if (data.filterable_type == 'dropdown' && data.filterable_options) {
                    var filterable_option = data.filterable_options.filter(option => option.value == value);

                    if (filterable_option.length > 0) {
                        return filterable_option[0].label;
                    }
                }

                return value;
            }
        },
    };
</script>