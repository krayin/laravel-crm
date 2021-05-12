<template>
    <tbody>
        <tr
            :key="collectionIndex"
            v-for="(row, collectionIndex) in dataCollection"
        >
            <td v-if="massActions.length > 0">
                <span class="checkbox">
                    <template v-if="selectedTableRows.filter((item, index) => item == row.id).length">
                        <input
                            type="checkbox"
                            checked="checked"
                            :key="Math.random()"
                            :id="`checkbox-${row.id}`"
                            @change="selectTableRow(row.id)"
                        />
                    </template>

                    <template v-else>
                        <input
                            type="checkbox"
                            :key="Math.random()"
                            :id="`checkbox-${row.id}`"
                            @change="selectTableRow(row.id)"
                        />
                    </template>

                    <label class="checkbox-view" for="checkbox"></label>
                </span>
            </td>

            <template v-for="(column, rowIndex) in columns">
                <td :key="rowIndex" v-html="row[column.index]"></td>
            </template>

            <td v-if="row['action']">
                <template v-for="(action, index) in row['action']">
                    <a
                        :key="index"
                        :href="action.route"
                        :title="action.title"
                        :target="action.target"
                        :data-action="action.route"
                        v-if="action.method == 'GET'"
                        :data-method="action.method"
                    >
                        <i :data-route="action.route" :class="`icon ${action.icon}`"></i>
                    </a>

                    <a
                        v-else
                        :key="index"
                        :title="action.title"
                        :target="action.target"
                        :data-action="action.route"
                        :data-method="action.method"
                        @click="doAction({
                            event: $event,
                            route: action.route,
                            method: action.method
                        })"
                    >
                        <i :key="index" :data-route="action.route" :class="`icon ${action.icon}`"></i>
                    </a>
                </template>
            </td>
        </tr>

        <tr v-if="dataCollection.length == 0" class="no-records">
            <td colspan="10">
                {{ __('ui.datagrid.no-records') }}
            </td>
        </tr>
    </tbody>
</template>

<script>
    import { mapState, mapActions } from 'vuex';

    export default {
        computed: {
            ...mapState({
                tableData : state => state.tableData,
                selectedTableRows : state => state.selectedTableRows,
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

            dataCollection: function () {
                return this.tableData.records.data;
            },
        },

        methods: {
            ...mapActions([
                'selectTableRow',
            ]),

            doAction: function ({event, route, method}) {
                if (confirm(this.__('ui.datagrid.massaction.delete'))) {
                    this.$http[method.toLowerCase()](route)
                        .then(response => {
                            event.preventDefault();

                            if (response.data.status) {
                                this.addFlashMessages({
                                    type    : "success",
                                    message : response.data.message,
                                });

                                EventBus.$emit('refresh_table_data', {usePrevious: true});
                            }
                        }).catch(error => {
                            event.preventDefault();

                            this.addFlashMessages({
                                type    : "error",
                                message : error.response.data.message,
                            });
                        });
                }
            },
        }
    };
</script>