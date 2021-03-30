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
    </tbody>
</template>

<script>
    export default {
        props: ['dataCollection'],
    };
</script>