<template>
    <tbody>
        <template v-if="resultLoaded">
            <tr
                :key="collectionIndex"
                v-for="(row, collectionIndex) in records"
                :class="
                    `${selectedTableRows.indexOf(row.id) > -1 ? 'active' : ''}`
                "
                :style="row.rowProperties"
            >
                <td v-if="massActions.length > 0">
                    <span class="checkbox">
                        <template
                            v-if="
                                selectedTableRows.filter(
                                    (item, index) => item == row.id
                                ).length
                            "
                        >
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
                        v-html="getRowContent(row[column.index])"
                        :title="column.title ? row[column.index] : ''"
                        :class="column.class"
                    ></td>
                </template>

                <td class="action">
                    <template v-for="(action, index) in actions">
                        <a
                            :key="index"
                            :href="row[`${action.key}_url`]"
                            :title="action.title"
                            :data-action="row[`${action.key}_url`]"
                            v-if="action.method == 'GET'"
                            :data-method="action.method"
                        >
                            <i
                                :data-route="row[`${action.key}_url`]"
                                :class="`icon ${action.icon}`"
                            ></i>
                        </a>

                        <a
                            v-else
                            :key="index"
                            :title="action.title"
                            :data-action="row[`${action.key}_url`]"
                            :data-method="action.method"
                            @click="
                                doAction({
                                    event: $event,
                                    route: row[`${action.key}_url`],
                                    method: action.method,
                                    confirm_text: action.confirm_text
                                })
                            "
                        >
                            <i
                                :key="index"
                                :data-route="row[`${action.key}_url`]"
                                :class="`icon ${action.icon}`"
                            ></i>
                        </a>
                    </template>
                </td>
            </tr>

            <tr v-if="records.length == 0" class="no-records">
                <td colspan="10">
                    {{ __("ui.datagrid.no-records") }}
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
import { mapState, mapActions } from "vuex";

export default {
    props: ["resultLoaded"],

    computed: {
        ...mapState({
            tableData: state => state.tableData,

            selectedTableRows: state => state.selectedTableRows
        }),

        columns: function() {
            return this.tableData.columns;
        },

        actions: function() {
            return this.tableData.actions;
        },

        massActions: function() {
            return this.tableData.massActions;
        },

        records: function() {
            return this.tableData.records.data;
        }
    },

    methods: {
        ...mapActions(["selectTableRow"]),

        doAction: function({ event, route, method, confirm_text }) {
            if (confirm_text) {
                if (confirm(confirm_text)) {
                    this.performAjax({ event, route, method });
                }
            } else {
                this.performAjax({ event, route, method, type: "download" });
            }
        },

        redirectRow: function(redirectURL) {
            if (redirectURL) {
                window.location = redirectURL;
            }
        },

        getRowContent: function(content) {
            return content || (content === 0 ? content : "--");
        },

        performAjax: function({ event, route, method, type }) {
            this.$http[method.toLowerCase()](route)
                .then(response => {
                    event.preventDefault();

                    if (type == "download") {
                        this.download(
                            response.data.fileName,
                            response.data.fileContent
                        );
                    } else {
                        this.addFlashMessages({
                            type: "success",
                            message: response.data.message
                        });

                        EventBus.$emit("refresh_table_data", {
                            usePrevious: true
                        });
                    }
                })
                .catch(error => {
                    event.preventDefault();

                    this.addFlashMessages({
                        type: "error",
                        message: error.response.data.message
                    });
                });
        },

        download: function(fileName, fileContent) {
            let dlAnchorElem = document.createElement("a");

            dlAnchorElem.id = "downloadAnchorElem";

            document.body.appendChild(dlAnchorElem);

            dlAnchorElem = document.getElementById("downloadAnchorElem");

            dlAnchorElem.setAttribute(
                "href",
                `data:text/plain;charset=utf-8,${fileContent}`
            );

            dlAnchorElem.setAttribute("download", `${fileName}`);

            dlAnchorElem.click();

            dlAnchorElem.parentNode.removeChild(dlAnchorElem);
        }
    }
};
</script>
