<template>
    <date-range-picker
        ref="picker"
        :class="classes"
        :opens="opens"
        :minDate="minDate"
        :maxDate="maxDate"
        :singleDatePicker="singleDatePicker"
        :timePicker="timePicker"
        :timePicker24Hour="timePicker24Hour"
        :showWeekNumbers="showWeekNumbers"
        :showDropdowns="showDropdowns"
        :autoApply="autoApply"
        :ranges="show_ranges ? undefined : false"
        :linkedCalendars="linkedCalendars"
        :dateFormat="dateFormat"
        :always-show-calendars="false"
        :alwaysShowCalendars="alwaysShowCalendars"
        :append-to-body="appendToBody"
        :closeOnEsc="closeOnEsc"
        v-model="dateRange"
        @update="updateValues"
        @toggle="checkOpen"
    >
        <template v-slot:input="picker" style="min-width: 350px;">
            {{ picker.startDate | moment("D MMM YYYY") }} -
            {{ picker.endDate | moment("D MMM YYYY") }}
        </template>
    </date-range-picker>
</template>

<script>
import DateRangePicker from "vue2-daterange-picker";
import "vue2-daterange-picker/dist/vue2-daterange-picker.css";

export default {
    components: { DateRangePicker },

    props: ["classes", "endDate", "startDate", "update"],

    data: function() {
        return {
            opens: "left",
            minDate: "2021-03-01",
            maxDate: this.$moment().format("YYYY-MM-DD"),
            dateRange: {
                endDate: this.endDate,
                startDate: this.startDate
            },
            single_range_picker: true,
            show_ranges: true,
            singleDatePicker: false,
            timePicker: false,
            timePicker24Hour: true,
            showDropdowns: true,
            autoApply: false,
            showWeekNumbers: true,
            linkedCalendars: true,
            alwaysShowCalendars: true,
            appendToBody: false,
            closeOnEsc: true
        };
    },

    methods: {
        updateValues: function(values) {
            this.dateRange.startDate = values.startDate;

            this.dateRange.endDate = values.endDate;

            let datesRange = [
                this.$moment(this.dateRange.startDate).format("YYYY-MM-DD"),
                this.$moment(this.dateRange.endDate).format("YYYY-MM-DD")
            ];

            datesRange = datesRange.join(",");

            this.update({ datesRange });
        },

        checkOpen(open) {},

        dateFormat(classes, date) {
            let yesterday = new Date();

            let d1 = date;
            let d2 = yesterday.setDate(yesterday.getDate() - 1);

            if (!classes.disabled) {
                classes.disabled = d1 === d2;
            }

            return classes;
        }
    }
};
</script>
