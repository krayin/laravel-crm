<template>
    <thead>
        <tr>
            <th
                :key="index"
                class="grid_head"
                v-text="column.label"
                v-for="(column, index) in columns">
            </th>

            <th v-if="actions.length > 0">
                {{ __('ui.datagrid.actions') }}
            </th>
        </tr>
    </thead>
</template>

<script>
    export default {
        props: ['columns', 'actions'],

        data: function () {
            return {}
        }
    };
</script>