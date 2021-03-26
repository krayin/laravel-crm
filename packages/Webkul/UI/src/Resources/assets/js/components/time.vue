<template>
    <div class="time-container">
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
			
			value: String
		},

		data() {
			return {
				datepicker: null
			};
		},

		mounted() {
			var self = this;

			this.datepicker = new Flatpickr(this.$el.getElementsByTagName("input")[0], {
				enableTime: true,
				noCalendar: true,
				dateFormat: "H:i",
				time_24hr: true,
				onChange: function(selectedDates, dateStr, instance) {
					self.$emit('onChange', dateStr)
				},
			});
		}
	};
</script>