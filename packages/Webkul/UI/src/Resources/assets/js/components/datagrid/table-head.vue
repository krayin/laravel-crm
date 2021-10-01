<template>
    <thead>
        <tr>
            <th class="master-checkbox" v-if="massActions.length > 0 && dataCollection.length > 0">
                <span class="checkbox">
                    <template v-if="allSelected">
                        <input
                            type="checkbox"
                            checked="checked"
                            :key="Math.random()"
                            @change="selectAllRows(false)"
                        />

                        <label class="checkbox-view" for="checkbox"></label>
                    </template>

                    <template v-else>
                        <input
                            type="checkbox"
                            :key="Math.random()"
                            @change="selectAllRows(true)"
                        />

                        <label :class="`checkbox-${selectedTableRows.length > 0 ? 'dash' : 'view'}`" for="checkbox"></label>
                    </template>
                </span>
            </th>

            <template v-for="(column, index) in columns">
                <th
                    :key="index"
                    v-html="column.label"
                    v-if="column.type != 'hidden'"
                    :style="column.head_style || ''"
                    @click="column.sortable ? sortCollection(index) : ''"
                    :class="[column.class ? column.class : column.index, `${column.sortable ? 'cursor-pointer' : ''}`]"
                ></th>
            </template>

            <th v-if="actions.length > 0" class="actions">
                {{ __('ui.datagrid.actions') }}
            </th>
        </tr>
    </thead>
</template>

<script>
    import { mapState, mapActions } from 'vuex';

    export default {
        data: function () {
            return {
                currentSort: 'desc',
            }
        },

        computed: {
            ...mapState({
                tableData : state => state.tableData,

                filters : state => state.filters,

                allSelected : state => state.allSelected,

                selectedTableRows : state => state.selectedTableRows,
            }),

            columns: function () {
                return this.tableData.columns;
            },

            actions: function () {
                return this.tableData.actions;
            },

            massActions: function () {
                return this.tableData.massActions;
            },

            dataCollection: function () {
                return this.tableData.records.data;
            },
        },

        methods: {
            ...mapActions([
                'selectAllRows',
            ]),

            findCurrentSort: function () {
                for (let i in this.filters) {
                    if (this.filters[i].column === 'sort') {
                        this.currentSort = this.filters[i].val;
                    }
                }
            },

            sortCollection: function (index) {
                this.findCurrentSort();

                EventBus.$emit('updateFilter', {
                    key   : 'sort',
                    cond  : this.columns[index].index,
                    value : this.currentSort == 'desc' ? 'asc' : 'desc',
                });
            },
        }
    };
</script>
