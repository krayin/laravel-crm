<template>
	<div class="datetime-container">
		<slot>
			<input type="text" :name="name" class="control" :value="value" data-input>
		</slot>
	</div>
</template>

<script>
    import Flatpickr from "flatpickr";

    export default {
        props: {
            name: String,

            value: String,
        },

        data() {
            return {
                datepicker: null
            };
        },

        created() {

        },

        mounted() {
            var self = this;

            this.datepicker = new Flatpickr(this.$el.getElementsByTagName("input")[0], {
                allowInput: true,
                altFormat: "Y-m-d H:i:S",
                dateFormat: "Y-m-d H:i:S",
                enableTime: true,
                time_24hr: true,
                weekNumbers: true,
                onChange: function (selectedDates, dateStr, instance) {
                    self.$emit('onChange', dateStr)
                },
            });
        },
    };
</script>