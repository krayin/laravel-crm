<template>
    <thead>
        <tr>
            <th class="master-checkbox" v-if="massActions.length > 0">
                <span class="checkbox">
                    <template v-if="allSelected">
                        <input
                            type="checkbox"
                            checked="checked"
                            :key="Math.random()"
                            @change="selectAllRows(false)"
                        />
                    </template>

                    <template v-else>
                        <input
                            type="checkbox"
                            :key="Math.random()"
                            @change="selectAllRows(true)"
                        />
                    </template>

                    <label class="checkbox-view" for="checkbox"></label>
                </span>
            </th>

            <th
                :key="index"
                v-text="column.label"
                @click="column.sortable ? sortCollection(index) : ''"
                v-for="(column, index) in columns">
            </th>

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
            }),

            columns: function () {
                return this.tableData.columns;
            },

            actions: function () {
                return this.tableData.actions;
            },

            massActions: function () {
                return this.tableData.massactions;
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
                    key     : 'sort',
                    cond    : this.columns[index].index,
                    value   : this.currentSort == 'desc' ? 'asc' : 'desc',
                });
            },
        }
    };
</script>