<template>
    <div>
        <!-- Toggler -->
        <div @click="open">
            <slot name="toggle"></slot>
        </div>

        <!-- Overlay -->
        <div v-show="isOpen" class="drawer-overlay"></div>

        <!-- Content -->
        <div
            class="drawer"
            :class="{'show': isOpen}"
        >
            <!-- Header Slot-->
            <div class="header">
                <slot name="header"></slot>
            </div>

            <!-- Content Slot -->
            <div class="content">
                <slot name="content"></slot>
            </div>
        </div>
    </div>
</template>

<script>
    export default {
        props: {
            isActive: {
                type: Boolean,
                default: false
            },

            position: {
                type: String,
                default: 'right'
            },
        },

        inject: ['$validator'],

        data() {
            return {
                isOpen: this.isActive
            };
        },

        watch: {
            isActive(newVal) {
                this.isOpen = newVal;
            }
        },

        methods: {
            toggle() {
                this.isOpen = !this.isOpen;

                document.body.style.overflow = this.isOpen ? 'hidden' : 'auto';

                this.$emit('toggle', { isActive: this.isOpen });
            },

            open() {
                this.isOpen = true;

                document.body.style.overflow = 'hidden';

                this.$emit('open', { isActive: this.isOpen });
            },

            close() {
                this.isOpen = false;

                document.body.style.overflow = 'auto';
                
                this.$emit('close', { isActive: this.isOpen });
            }
        }
    };
</script>