<template>
    <div :class="`sidebar-filter ${sidebarFilter ? 'show' : ''}`" v-if="sidebarFilter">
        Hello Sidebar Filter
        <i class="fa fa-times cursor-pointer" @click="toggleSidebarFilter"></i>
    </div>
</template>

<script>
    import { mapState, mapActions } from 'vuex';

    export default {
        props: [
        ],

        data: function () {
            return {
            }
        },

        computed: {
            ...mapState({
                sidebarFilter : state => state.sidebarFilter,
            }),
        },

        methods: {
            ...mapActions([
                'toggleSidebarFilter',
            ]),
        },
    };
</script>