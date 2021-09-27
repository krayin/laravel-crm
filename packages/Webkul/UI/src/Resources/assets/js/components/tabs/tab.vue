<template>
    <div v-show="isActive">
        <slot></slot>
    </div>
</template>

<script>
    export default {
        props: {
            name: { 
                required: true 
            },

            selected: {
                default: false
            }
        },

        inject: ['$validator'],
        
        data() {
            return {
                isActive: false,

                hasError: false
            };
        },
        
        mounted() {
            this.addHasErrorClass()

            eventBus.$on('onFormError', this.addHasErrorClass);
            
            this.isActive = this.selected;
        },

        methods: {
            addHasErrorClass: function() {
                var self = this;

                self.hasError = false;

                setTimeout(function() {
                    $(self.$el).find('.form-group').each(function(index, element) {
                        if ($(element).hasClass('has-error')) {
                            self.hasError = true;
                        }
                    });
                }, 0);
            }
        }
    }
</script>