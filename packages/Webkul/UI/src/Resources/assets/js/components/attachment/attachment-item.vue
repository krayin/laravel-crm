<template>
    <div class="attachment-item" v-show="show">
        <span>
            <input type="file" :name="inputName" @change="attachmentSelected($event)" ref="attachment" />

            {{ name }}

            <i class="icon close-icon" @click="removeAttachment"></i>
        </span>
    </div>
</template>

<script>
    export default {
        props: {
            attachment: {
                type: Object,

                required: false,

                default: null
            },

            inputName: {
                type: String,

                required: false,

                default: 'attachments'
            },
        },

        data: function() {
            return {
                show: false,

                name: ''
            }
        },

        mounted: function() {
            this.$refs.attachment.click();
        },

        methods: {
            attachmentSelected: function() {
                var attachment = this.$refs.attachment;

                if (attachment.files && attachment.files[0]) {
                    this.show = true;

                    this.name = attachment.files[0].name;
                }
            },

            removeAttachment () {
                this.$emit('onRemoveAttachment', this.attachment);
            }
        }
    }
</script>
