<template>
    <div :class="`spinner-container ${fullPage ? 'full-page' : ''}`">
        <div class="meter"></div>
    </div>
</template>

<script>
    export default {
        props: ['fullPage'],

        mounted: function () {
            if (this.fullPage) {
                $('body').css('overflow', 'hidden');
            }
        },

        beforeDestroy: function () {
            if (this.fullPage) {
                $('body').css('overflow', 'unset');
            }
        }
    }
</script>