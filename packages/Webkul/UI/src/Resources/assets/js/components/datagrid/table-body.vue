<template>
    <tbody>
        <tr
            :key="collectionIndex"
            v-for="(row, collectionIndex) in dataCollection"
        >
            <td
                :key="rowIndex"
                v-text="column"
                v-for="(column, rowIndex) in row">
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
        props: ['dataCollection'],
    };
</script>