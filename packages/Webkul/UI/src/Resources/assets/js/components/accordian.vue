<template>
    <div class="accordian" :class="[isActive ? 'active' : '', className, ! isActive && hasError ? 'has-error' : '']" :id="id">
        <div class="accordian-header" @click="toggleAccordian()">
            <slot name="header">
                {{ title }}

                <i class="icon" :class="iconClass"></i>
            </slot>
        </div>

        <div class="accordian-content">
            <slot name="body"></slot>
        </div>
    </div>
</template>

<script>
    export default {
        props: {
            title: String,

            id: String,

            className: String,

            active: Boolean
        },

        inject: ['$validator'],

        data: function() {
            return {
                isActive: false,

                hasError: false
            }
        },

        mounted: function() {
            this.addHasErrorClass()

            eventBus.$on('onFormError', this.addHasErrorClass);

            this.isActive = this.active;
        },

        methods: {
            toggleAccordian: function() {
                this.isActive = ! this.isActive;
            },

            addHasErrorClass: function() {
                var this_this = this;

                setTimeout(function() {
                    $(this_this.$el).find('.control-group').each(function(index, element) {
                        if ($(element).hasClass('has-error')) {
                            this_this.hasError = true;
                        }
                    });
                }, 0);
            }
        },

        computed: {
            iconClass() {
                return {
                    'arrow-down-icon': ! this.isActive,
                    'arrow-up-icon': this.isActive,
                };
            }
        }
    }
</script>