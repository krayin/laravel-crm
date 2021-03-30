<template>
    <div class="pagination" v-if="paginationData.has_pages">
        <a class="page-item previous" v-if="paginationData.on_first_page">
            <i class="icon angle-left-icon"></i>
        </a>
        <a
            v-else
            id="previous"
            class="page-item previous"
            :href="paginationData.previous_page_url"
            :data-page="paginationData.previous_page_url"
        >
            <i class="icon angle-left-icon"></i>
        </a>

        <!-- @foreach ($elements as $element)
            @if (is_string($element))
                <a class="page-item disabled" aria-disabled="true">
                    {{ $element }}
                </a>
            @endif

            @if (is_array($element))
                @foreach ($element as $page => $url)
                    @if ($page == $paginator->currentPage())
                        <a class="page-item active">
                            {{ $page }}
                        </a>
                    @else
                        <a class="page-item as" href="{{ urldecode($url) }}">
                            {{ $page }}
                        </a>
                    @endif
                @endforeach
            @endif
        @endforeach -->

        <a
            id="next"
            class="page-item next"
            v-if="paginationData.has_more_pages"
            :href="paginationData.next_page_url"
        >
            <i class="icon angle-right-icon"></i>
        </a>

        <a class="page-item next" v-else>
            <i class="icon angle-right-icon"></i>
        </a>
    </div>
</template>

<script>
    export default {
        props: ['paginationData'],

        data: function () {
            return {

            }
        }
    };
</script>