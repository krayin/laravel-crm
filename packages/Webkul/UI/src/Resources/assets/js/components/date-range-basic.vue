<template>
    <div :id="`dateRange${dateRangeKey}`" class="form-group date">
        <div class="date-container">
            <input
                ref="startDate"
                type="text"
                class="control half"
                :placeholder="__('ui.datagrid.filter.start_date')"
            />
        </div>

        <span class="middle-text">{{ __("ui.datagrid.filter.to") }}</span>

        <div class="date-container">
            <input
                ref="endDate"
                type="text"
                class="control half"
                :placeholder="__('ui.datagrid.filter.end_date')"
            />
        </div>
    </div>
</template>

<script>
import Flatpickr from "flatpickr";

export default {
    props: ["dateRangeKey", "startDate", "endDate"],

    data: function() {
        return {
            dates: [this.startDate, this.endDate],
            config: {
                allowInput: true,
                altFormat: "Y-m-d",
                dateFormat: "Y-m-d",
                weekNumbers: true
            },
            startDatePicker: null,
            endDatePicker: null
        };
    },

    mounted: function() {
        this.activateStartDatePicker();

        this.activateEndDatePicker();
    },

    methods: {
        activateStartDatePicker: function() {
            let self = this;

            this.startDatePicker = new Flatpickr(this.$refs.startDate, {
                ...this.config,
                onChange: function(selectedDates, dateStr, instance) {
                    self.dates[0] = dateStr;

                    self.endDatePicker.set("minDate", dateStr);

                    self.$emit("onChange", self.dates);
                }
            });
        },

        activateEndDatePicker: function() {
            let self = this;

            this.endDatePicker = new Flatpickr(this.$refs.endDate, {
                ...this.config,
                onChange: function(selectedDates, dateStr, instance) {
                    self.dates[1] = dateStr;

                    self.startDatePicker.set("maxDate", dateStr);

                    self.$emit("onChange", self.dates);
                }
            });
        }
    }
};
</script>
