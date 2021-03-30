<template>
    <div class="pagination" v-if="paginationData.has_pages" :style="`margin-top: ${tabView ? '0px' : '20px'}`">
        <template v-if="tabView">
            <!-- <span>{{ paginationData.current_page *  perPage }}-{{ (paginationData.current_page *  perPage) + perPage }} of {{ paginationData.total_rows }}</span> -->

            <!-- TODO stop redirecting redirect -->
            <a class="page-item previous" v-if="paginationData.on_first_page">
                <i class="fa fa-arrow-left"></i>
            </a>
            <a
                v-else
                id="previous"
                class="page-item previous"
                :href="paginationData.previous_page_url"
            >
                <i class="fa fa-arrow-left"></i>
            </a>
            
            <a
                id="next"
                class="page-item next"
                v-if="paginationData.has_more_pages"
                :href="paginationData.next_page_url"
            >
                <i class="fa fa-arrow-right"></i>
            </a>
            <a class="page-item next" v-else>
                <i class="fa fa-arrow-right"></i>
            </a>
        </template>

        <template v-else>
            <a class="page-item previous" v-if="paginationData.on_first_page">
                <i class="fa fa-angle-left"></i>
            </a>
            <a
                v-else
                id="previous"
                class="page-item previous"
                :href="paginationData.previous_page_url"
                :data-page="paginationData.previous_page_url"
            >
                <i class="fa fa-angle-left"></i>
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
                <i class="fa fa-angle-right"></i>
            </a>
            <a class="page-item next" v-else>
                <i class="fa fa-angle-right"></i>
            </a>
        </template>
    </div>
</template>

<script>
    export default {
        props: ['paginationData', 'tabView', 'perPage'],

        data: function () {
            return {

            }
        },

        methods: {
            changePage: function () {
                // paginationData.previous_page_url

                // EventBus.$emit('update_filter', {
                //     key: page
                // });
            }
        }
    };
</script>

<style scoped>
    i, a {
        font-weight: 700;
        font-size: 16px !important;
    }
</style>