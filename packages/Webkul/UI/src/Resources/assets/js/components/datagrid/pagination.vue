<template>
    <div :class="`pagination ${tabView ? 'tab-view' : 'full-view'}`" v-if="paginationData.has_pages">
        <template v-if="tabView">
            <!-- <span>{{ paginationData.current_page *  perPage }}-{{ (paginationData.current_page *  perPage) + perPage }} of {{ paginationData.total_rows }}</span> -->

            <a class="page-item previous" v-if="paginationData.on_first_page">
                <i class="icon arrow-left-line-icon"></i>
            </a>

            <a
                v-else
                id="previous"
                class="page-item previous"
                @click="changePage({
                    url: paginationData.previous_page_url,
                    page_number: paginationData.current_page - 1
                })"
            >
                <i class="icon arrow-left-line-icon"></i>
            </a>
            
            <a
                id="next"
                class="page-item next"
                v-if="paginationData.has_more_pages"
                @click="changePage({
                    url: paginationData.next_page_url,
                    page_number: paginationData.current_page + 1
                })"
            >
                <i class="icon arrow-right-line-icon"></i>
            </a>

            <a class="page-item next" v-else>
                <i class="icon arrow-right-line-icon"></i>
            </a>
        </template>

        <template v-else>
            <a class="page-item previous" v-if="paginationData.on_first_page">
                <i class="icon arrow-left-line-icon"></i>
            </a>

            <a
                v-else
                id="previous"
                class="page-item previous"
                :href="paginationData.previous_page_url"
                :data-page="paginationData.previous_page_url"
            >
                <i class="icon arrow-left-line-icon"></i>
            </a>

            <template v-for="(element, index) in paginationData.elements">
                <template v-if="typeof(element) == 'string'">
                    <a class="page-item disabled" aria-disabled="true" :key="index">
                        {{ element }}
                    </a>
                </template>

                <template v-else>
                    <template v-for="(url, page) in element">
                        <template v-if="paginationData.current_page == page">
                            <a class="page-item active" :key="`${index} + ${page}`">
                                {{ page }}
                            </a>
                        </template>

                        <template v-else>
                            <a class="page-item as" :href="url" :key="`${index} + ${page}`">
                                {{ page }}
                            </a>
                        </template>
                    </template>
                </template>
            </template>

            <a
                id="next"
                class="page-item next"
                v-if="paginationData.has_more_pages"
                :href="paginationData.next_page_url"
            >
                <i class="icon arrow-right-line-icon"></i>
            </a>

            <a class="page-item next" v-else>
                <i class="icon arrow-right-line-icon"></i>
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

            paginationData: function () {
                return this.tableData.paginationData;
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