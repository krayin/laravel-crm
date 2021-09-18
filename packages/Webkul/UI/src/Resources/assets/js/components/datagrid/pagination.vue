<template>
    <div :class="`pagination ${tabView ? 'tab-view' : 'full-view'}`" v-if="records.data.length > 0">
        <template v-if="tabView">
            <a class="page-item previous disabled" v-if="! records.prev_page_url">
                <i class="icon arrow-left-line-icon"></i>
            </a>

            <a
                v-else
                id="previous"
                class="page-item previous"
                @click="changePage({
                    url: records.prev_page_url,
                    page_number: records.current_page - 1
                })"
            >
                <i class="icon arrow-left-line-icon"></i>
            </a>

            <a
                id="next"
                class="page-item next"
                v-if="records.next_page_url"
                @click="changePage({
                    url: records.next_page_url,
                    page_number: records.current_page + 1
                })"
            >
                <i class="icon arrow-right-line-icon"></i>
            </a>

            <a class="page-item next disabled" v-else>
                <i class="icon arrow-right-line-icon"></i>
            </a>
        </template>

        <template v-else>
            <a
                v-for="(link, index) in records.links"
                :key="index"
                href="javascript:void(0);"
                :data-page="link.url"
                :class="
                    `page-item ${index == 0 ? 'previous' : ''} ${
                        link.active ? 'active' : ''
                    } ${index == records.links.length - 1 ? 'next' : ''}`
                "
                @click="changePage({
                    url: link.url,
                    page_number: records.current_page
                })"
            >
                <i class="icon angle-left-icon" v-if="index == 0"></i>
                <i
                        class="icon angle-right-icon"
                        v-else-if="index == records.links.length - 1"
                ></i>
                <span v-text="link.label" v-else></span>
            </a>
        </template>
    </div>
</template>

<script>
    import { mapState } from 'vuex';

    export default {
        props: ['tabView', 'perPage'],

        data: function () {
            return {

            }
        },

        computed: {
            ...mapState({
                tableData : state => state.tableData,
            }),

            records: function () {
                return this.tableData.records;
            },
        },

        methods: {
            changePage: function ({url, page_number}) {
                EventBus.$emit('updateFilter', {
                    key     : 'page',
                    value   : page_number
                });
            }
        }
    };
</script>
