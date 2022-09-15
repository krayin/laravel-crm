<template>
    <div class="attachment-wrapper">
        <div
            @dragcenter.prevent="toggleActive"
            @dragleave.prevent="toggleActive"
            @dragover.prevent
            @drop.prevent="drop"
            :class="{ 'active-dropzone': active }"
            class="dropzone"
        >
            <span>Drag or drop files</span>

            <span>OR</span>

            <attachment-item
                v-for="attachment in attachments"
                :key="attachment.id"
                :attachment="attachment"
                :input-name="inputName"
                @onRemoveAttachment="removeAttachment($event)"
            ></attachment-item>

            <label class="add-attachment-link" @click="addAttachment">
                <i class="icon attachment-icon"></i>
                {{ __("ui.add-attachment") }}
            </label>
        </div>
    </div>
</template>

<script>
export default {
    props: {
        data: {
            type: Array | String,

            required: false,

            default: () => [],
        },

        inputName: {
            type: String,

            required: false,

            default: "attachments[]",
        },
    },

    data: function () {
        return {
            active: false,

            attachmentCount: 0,

            attachments: [],

            dropzoneFile: [],
        };
    },

    created() {
        let self = this;

        this.data.forEach(function (attachment) {
            attachment.isNew = false;

            self.attachments.push(attachment);

            self.attachmentCount++;
        });
    },

    methods: {
        addAttachment() {
            this.attachmentCount++;

            this.attachments.push({
                id: "attachment_" + this.attachmentCount,
                isNew: true,
            });
        },

        drop: function (event) {
            this.attachmentCount++;

            const id = "attachment_" + this.attachmentCount;

            this.attachments.push({
                id,
                isNew: false,
                type: "dropzone",
                file: event.dataTransfer.files[0]
            });
        },

        toggleActive() {
            this.active = !this.active;
        },

        removeAttachment(attachment) {
            let index = this.attachments.indexOf(attachment);

            Vue.delete(this.attachments, index);
        },
    },
};
</script>

<style>
.dropzone {
    width: 400px;
    padding: 1%;
    display: flex;
    flex-direction: column;
    justify-content: center;
    align-items: center;
    row-gap: 16px;
    transition: 0.3s ease all;
}

.dropzone label {
    padding: 8px 12px;
    transition: 0.3s ease all;
}

.active-dropzone {
    border-style: dashed;
    border-color: darkgrey;
}

.active-dropzone input {
    display: none;
}

.attachment-wrapper .attachment-item span {
    word-break: break-all;
}
</style>
