<template>
    <div class="attachment-item" v-show="show">
        <span>
            <input
                :id="attachment.id ?? ''"
                class="attachment"
                type="file"
                name="attachment[]"
                @change="attachmentSelected($event)"
                ref="attachment"
            />

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

            default: null,
        },

        inputName: {
            type: String,

            required: false,
        },
    },

    data: function () {
        return {
            show: false,

            name: "",
        };
    },

    mounted: function () {
        this.activate();
    },

    methods: {
        activate: function () {
            if (this.attachment.isNew) {
                this.openFileBrowser();
            } else {
                if (this.attachment?.type) {
                    this.addDropZoneFile();

                    return;
                }

                this.showExistingAttachment();
            }
        },

        openFileBrowser: function () {
            this.$refs.attachment.click();
        },

        addDropZoneFile() {
            let data = new DataTransfer();

            data.items.add(this.attachment.file);

            this.$refs.attachment.files = data.files;

            this.attachmentSelected();
        },

        attachmentSelected: function () {
            let attachment = this.$refs.attachment;

            if (attachment.files && attachment.files[0]) {
                this.show = true;

                this.name = attachment.files[0].name;
            }
        },

        showExistingAttachment: function () {
            this.show = true;

            this.name = this.attachment.name;
        },

        removeAttachment: function () {
            this.$emit("onRemoveAttachment", this.attachment);
        },
    },
};
</script>
