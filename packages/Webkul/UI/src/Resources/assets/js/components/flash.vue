<template>
    <div class="alert" v-bind:class="flash.type">
        <span class="icon" v-bind:class="icons[flash.type]"></span>
        <p>{{ flash.message }}</p>
        <span class="icon close-white-icon" @click="remove"></span>
    </div>
</template>

<script>
    export default {
        props: ['flash'],

        data: function() {
            return {
                icons: {
                    'success': 'circle-check-white-icon',
                    'warning': 'circle-info-white-icon',
                    'error': 'circle-close-white-icon',
                    'info': 'circle-info-white-icon',
                }
            }
        },

        created: function() {
            var self = this;
            
            setTimeout(function() {
                self.$emit('onRemoveFlash', self.flash);
            }, 5000)
        },

        methods: {
            remove: function() {
                this.$el.remove();
            }
        }
    }
</script>