<template>
    <div class="modal-container" v-if="isModalOpen">
        <div class="modal-header">
            <slot name="header">
                <slot name="header-title">
                    <h3>Default header</h3>
                </slot>

                <div class="header-actions">
                    <slot name="header-actions">
                        <i class="icon close-icon" @click="closeModal"></i>
                    </slot>
                </div>
            </slot>
        </div>

        <div class="modal-body">
            <slot name="body">
                Default body
            </slot>
        </div>
    </div>
</template>

<script>
    export default {
        props: ['id', 'isOpen'],

        created () {
            this.closeModal();
        },

        inject: ['$validator'],

        computed: {
            isModalOpen () {
                this.addClassToBody();

                return this.isOpen;
            }
        },

        methods: {
            closeModal () {
                this.$root.$set(this.$root.modalIds, this.id, false);
            },

            addClassToBody () {
                var body = document.querySelector("body");

                if (this.isOpen) {
                    body.classList.add("modal-open");
                } else {
                    body.classList.remove("modal-open");
                }
            }
        }
    }
</script>