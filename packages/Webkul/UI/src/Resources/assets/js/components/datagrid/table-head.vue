<template>
    <thead>
        <tr>
            <th
                :key="index"
                class="grid_head"
                v-text="column.label"
                v-for="(column, index) in columns">
            </th>
        </tr>
    </thead>
</template>

<script>
    export default {
        props: ['columns'],

        data: function () {
            return {}
        }
    };
</script>