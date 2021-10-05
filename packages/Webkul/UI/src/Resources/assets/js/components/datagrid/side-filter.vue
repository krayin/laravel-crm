<template>
    <div :class="`sidebar-filter`">
        <header>
            <h1>
                <span>{{ __("ui.datagrid.filter.title") }}</span>

                <div class="right">
                    <label @click="removeAll">
                        {{ __("ui.datagrid.filter.remove_all") }}
                    </label>

                    <i class="icon close-icon" @click="toggleSidebarFilter"></i>
                </div>
            </h1>
        </header>

        <template v-for="(data, key) in columns || tableData.columns">
            <div
                :class="`form-group ${data.type == 'date_range' ? 'date' : ''}`"
                :key="key"
                v-if="data.type"
            >
                <label v-if="data.filterable">{{ data.label }}</label>

                <div class="field-container">
                    <template
                        v-if="data.filterable && data.type == 'integer_range'"
                    >
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

                    <template
                        v-else-if="data.filterable && data.type == 'date_range'"
                    >
                        <date-range-basic
                            :date-range-key="key"
                            :start-date="data.values[0]"
                            :end-date="data.values[1]"
                            @onChange="changeDateRange(key, $event)"
                        ></date-range-basic>
                    </template>

                    <template
                        v-else-if="data.filterable && data.type == 'dropdown'"
                    >
                        <select
                            class="control"
                            @change="pushFieldValue(key, $event, data.index)"
                        >
                            <option value="" disabled selected>
                                {{ data.label }}
                            </option>

                            <option
                                :value="option.value"
                                :key="index"
                                v-for="(option, index) in data.dropdown_options"
                            >
                                {{ option.label }}
                            </option>
                        </select>

                        <div class="selected-options">
                            <span
                                :key="index"
                                v-for="(value, index) in data.values"
                                class="badge badge-md badge-pill badge-secondary"
                            >
                                {{ getFilteredValue(value, data) }}

                                <i
                                    class="icon close-icon ml-10"
                                    @click="removeFieldValue(key, index, data.index)"
                                ></i>
                            </span>
                        </div>
                    </template>

                    <template v-else>
                        <template v-if="data.filterable">
                            <span
                                class="control"
                                @click="toggleInput(data.index)"
                                v-if="! addField[data.index]"
                            >
                                <i class="icon add-icon"></i> {{ data.label }}
                            </span>

                            <div class="enter-new" v-else>
                                <input
                                    type="text"
                                    class="control mb-10"
                                    :placeholder="data.label"
                                    :id="`enter-new-${data.index}`"
                                    @keyup.enter="
                                        pushFieldValue(key, $event, data.index)
                                    "
                                />
                            </div>

                            <div class="selected-options">
                                <span
                                    :key="index"
                                    v-for="(value, index) in data.values"
                                    class="badge badge-md badge-pill badge-secondary"
                                >
                                    {{ getFilteredValue(value, data) }}

                                    <i
                                        class="icon close-icon ml-10"
                                        @click="
                                            removeFieldValue(
                                                key,
                                                index,
                                                data.index
                                            )
                                        "
                                    ></i>
                                </span>
                            </div>
                        </template>
                    </template>

                    <i
                        class="icon close-icon"
                        @click="
                            removeFilter({
                                type: data.type,
                                key,
                                index: data.index
                            })
                        "
                        v-if="data.filterable"
                    ></i>
                </div>
            </div>
        </template>
    </div>
</template>

<script>
import { mapState, mapActions } from "vuex";

export default {
    props: ["columns"],

    data: function() {
        return {
            addField: {}
        };
    },

    computed: {
        ...mapState({
            filterData: state => state.filterData,

            tableData: state => state.tableData,

            sidebarFilter: state => state.sidebarFilter
        })
    },

    methods: {
        ...mapActions(["toggleSidebarFilter", "updateFilterValues"]),

        toggleInput: function(key, event) {
            this.addField[key] = ! this.addField[key];

            this.$forceUpdate();

            setTimeout(() => {
                document.getElementById(`enter-new-${key}`).focus();
            });
        },

        pushFieldValue: function(key, { target }, indexKey) {
            this.addField[indexKey] = false;

            const values =
                (this.columns || this.tableData.columns)[key].values || [];

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

        removeFieldValue: function(key, index, indexKey) {
            const values = (this.columns || this.tableData.columns)[key].values;
            values.splice(index, 1);

            this.updateFilterValues({
                key: indexKey,
                values
            });

            this.$forceUpdate();
        },

        removeAll: function() {
            /* for default tables cases */
            if (this.$store.state.filters.length !== undefined) {
                this.$store.state.filters = this.$store.state.filters.filter(
                    filter => filter.column == "view_type" && filter.val == "table"
                );

                (this.columns || this.tableData.columns).forEach(
                    (column, index) => {
                        if (column.type && column.type == "date_range") {
                            $(".flatpickr-input").val('');
                        }
                    }
                );
            } else {
                /**
                 * For kanban case.
                 *
                 * To Do (@devansh-webkul): This needs to be supported by
                 * all types present in the kanban columns. Currently added
                 * for `created_at`.
                 */
                $(".flatpickr-input").val('');

                this.updateFilterValues({
                    key: 'created_at',
                    values: ['', '']
                });
            }

            this.$forceUpdate();
        },

        removeFilter: function({ type, key, index }) {
            if (type == "add" && this.addField[index]) {
                this.addField[index] = false;
            }

            let values = (this.columns || this.tableData.columns)[key].values;
            values = "";

            if (type === 'date_range') {
                $(`#dateRange${key}`).find($(".flatpickr-input")).val('');
            }

            this.updateFilterValues({
                key,
                values
            });

            this.$forceUpdate();
        },

        changeDateRange: function(key, event) {
            setTimeout(() => {
                this.updateFilterValues({
                    key,
                    values: event,
                    condition: "bw"
                });
            }, 0);
        },

        getFilteredValue: function(value, data) {
            if (data.type == "dropdown" && data.dropdown_options) {
                let dropdown_option = data.dropdown_options.filter(
                    option => option.value == value
                );

                if (dropdown_option.length > 0) {
                    return dropdown_option[0].label;
                }
            }

            return value;
        }
    }
};
</script>
