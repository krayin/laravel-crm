<template>
    <tbody>
        <tr
            :key="collectionIndex"
            v-for="(row, collectionIndex) in dataCollection"
        >
            <template v-for="(column, rowIndex) in row">
                <td :key="rowIndex" v-html="column" v-if="rowIndex != 'action'"></td>
                
                <td class="actions" :key="rowIndex" v-else>
                    <template v-for="(action, index) in column">
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
            </template>
        </tr>

        <tr v-if="dataCollection.length == 0" class="no-records">
            <td colspan="10">
                {{ __('ui.datagrid.no-records') }}
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
    export default {
        props: ['dataCollection', 'actions'],

        methods: {
            doAction: function ({event, route, method}) {
                if (confirm(this.__('ui.datagrid.massaction.delete'))) {
                    this.$http[method.toLowerCase()](route)
                        .then(response => {
                            event.preventDefault();

                            // add alert
                        }).catch(error => {
                            event.preventDefault();
                            // add alert
                        });
                }
            },
        }
    };
</script>