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

      dropzoneFile : [],


    };
  },

  created() {
    let self = this;

    this.data.forEach(function (attachment) {
      attachment.isNew = false;
      
      self.attachments.push(attachment);
      
      self.attachmentCount++;
    });

    console.log(self.attachments)

  },

  methods: {
    addAttachment() {
      this.attachmentCount++;

      this.attachments.push({
        id: "attachment_" + this.attachmentCount,
        isNew: true,
      });

    },

    drop:function(event){
      // alert("sd");
      let data = new DataTransfer();

      // drop zone
      data.items.add(event.dataTransfer.files[0]);

      let selector = document.getElementsByClassName('attachment');
      
      selector.forEach((item) => {
        data.items.add(item.files[0]);
      });
      
      selector[0].files = data.files;
      
    },

    toggleActive(){
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
  height: 200px;
  display: flex;
  flex-direction: column;
  justify-content: center;
  align-items: center;
  row-gap: 16px;
  border: 2px dashed solid #41b883;
  background-color: #fff;
  transition: 0.3s ease all;

  
}

.dropzone label {
    padding: 8px 12px;
    color: #fff;
    background-color: #41b883;
    transition: 0.3s ease all;
  }


.active-dropzone {
  color: #fff;
  border-color: #fff;
  background-color: #41b883;

}

.active-dropzone  label {
    background-color: #fff;
    color: #41b883;
  }

 .active-dropzone input {
    display: none;
  }

</style>


