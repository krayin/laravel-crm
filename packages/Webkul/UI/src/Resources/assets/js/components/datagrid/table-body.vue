<template>
    <tbody>
        <template v-if="resultLoaded">
            <tr
                :key="collectionIndex"
                v-for="(row, collectionIndex) in dataCollection"
                :class="`${selectedTableRows.indexOf(row.id) > -1 ? 'active' : ''}`"
            >
                <td v-if="massActions.length > 0" class="checkbox">
                    <span>
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
                    <td
                        :key="rowIndex"
                        v-if="column.type != 'hidden'"
                        @click="redirectRow(row.redirect_url)"
                        v-text="getRowContent(row[column.index])"
                        :title="column.title ? row[column.index] : ''"
                        :class="[row.redirect_url ? 'cursor-pointer' : '', column.class || column.index ]"
                    ></td>
                </template>

                <td v-if="row['action']" class="action">
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
                                event           : $event,
                                route           : action.route,
                                method          : action.method,
                                confirm_text    : action.confirm_text,
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
        </template>

        <tr class="no-records" v-else>
            <td colspan="10">
                <spinner-meter></spinner-meter>
            </td>
        </tr>
    </tbody>
</template>

<script>
    import { mapState, mapActions } from 'vuex';

    export default {
        props: ['resultLoaded'],

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

            doAction: function ({event, route, method, confirm_text}) {
                if (confirm_text) {
                    if (confirm(confirm_text)) {
                        this.performAjax({event, route, method});
                    }
                } else {
                    this.performAjax({event, route, method, type: 'download'});
                }
            },

            redirectRow: function (redirectURL) {
                if (redirectURL) {
                    window.location = redirectURL;
                }
            },

            getRowContent: function (content) {
                if (content) {
                    // content = content.replace("<script>", "<\/script>");
                }
                return content || (content === 0 ? content : '--')
            },

            performAjax: function ({event, route, method, type}) {
                this.$http[method.toLowerCase()](route)
                    .then(response => {
                        event.preventDefault();

                        if (response.data.status) {
                            if (type == 'download') {
                                var dlAnchorElem = document.createElement('a');
                                dlAnchorElem.id = 'downloadAnchorElem';

                                document.body.appendChild(dlAnchorElem);

                                var dataStr = `data:text/plain;charset=utf-8,${response.data.fileContent}`;
                                var dlAnchorElem = document.getElementById('downloadAnchorElem');

                                dlAnchorElem.setAttribute("href", dataStr);
                                dlAnchorElem.setAttribute("download", `${response.data.fileName}`);

                                dlAnchorElem.click();

                                dlAnchorElem.parentNode.removeChild(dlAnchorElem);
                            } else {
                                this.addFlashMessages({
                                    type    : "success",
                                    message : response.data.message,
                                });

                                EventBus.$emit('refresh_table_data', {usePrevious: true});
                            }
                        }
                    }).catch(error => {
                        event.preventDefault();

                        this.addFlashMessages({
                            type    : "error",
                            message : error.response.data.message,
                        });
                    });
            }
        }
    };
</script>
