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
                            @change="selectAllRows"
                        />
                    </template>

                    <template v-else>
                        <input
                            type="checkbox"
                            :key="Math.random()"
                            @change="selectAllRows"
                        />
                    </template>

                    <label class="checkbox-view" for="checkbox"></label>
                </span>
            </th>

            <th
                :key="index"
                class="grid_head"
                v-text="column.label"
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
        props: ['columns', 'actions', 'massActions'],

        data: function () {
            return {

            }
        },

        computed: {
            ...mapState({
                allSelected : state => state.allSelected,
            }),
        },

        methods: {
            ...mapActions([
                'selectAllRows',
            ]),
        }
    };
</script>