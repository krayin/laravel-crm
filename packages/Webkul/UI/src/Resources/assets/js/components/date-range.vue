<template>
    <date-range-picker
        ref="picker"
        :class="classes"
        :opens="opens"
        :locale-data="{ firstDay: 1, format: 'yyyy-mm-dd HH:MM:ss' }"
        :minDate="minDate" :maxDate="maxDate"
        :singleDatePicker="singleDatePicker"
        :timePicker="timePicker"
        :timePicker24Hour="timePicker24Hour"
        :showWeekNumbers="showWeekNumbers"
        :showDropdowns="showDropdowns"
        :autoApply="autoApply"
        v-model="dateRange"
        :ranges="show_ranges ? undefined : false"
        @update="updateValues"
        @toggle="checkOpen"
        :linkedCalendars="linkedCalendars"
        :dateFormat="dateFormat"
        :always-show-calendars="false"
        :alwaysShowCalendars="alwaysShowCalendars"
        :append-to-body="appendToBody"
        :closeOnEsc="closeOnEsc"
    >
        <template v-slot:input="picker" style="min-width: 350px;">
            {{ picker.startDate | moment("D MMM YYYY") }} - {{ picker.endDate | moment("D MMM YYYY") }}
        </template>
    </date-range-picker>
</template>

<script>
    import DateRangePicker from 'vue2-daterange-picker';
    import 'vue2-daterange-picker/dist/vue2-daterange-picker.css';

    export default {
        components: { DateRangePicker },

        props: ['classes'],

        data: function () {
            return {
                opens: 'left',
                minDate: '2019-05-02 04:00:00',
                maxDate: '2020-12-26 14:00:00',
                // minDate: '',
                // maxDate: '',
                dateRange: {
                    startDate: '2019-12-10',
                    endDate: '2019-12-20',
                },
                single_range_picker: false,
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
                closeOnEsc: true,
            }
        },

          methods: {
            updateValues (values) {
                console.log('event: update', {...values})
                this.dateRange.startDate = values.startDate;
                this.dateRange.endDate = values.endDate;

                // this.dateRange.startDate = dateUtil.format(values.startDate, 'yyyy-mm-dd HH:MM:ss');
                // this.dateRange.endDate = dateUtil.format(values.endDate, 'yyyy-mm-dd HH:MM:ss');
            },

            checkOpen (open) {
                console.log('event: open', open)
            },

            dateFormat (classes, date) {
                let yesterday = new Date();
                let d1 = date
                let d2 = yesterday.setDate(yesterday.getDate() - 1)

                // let d1 = dateUtil.format(date, 'isoDate')
                // let d2 = dateUtil.format(yesterday.setDate(yesterday.getDate() - 1), 'isoDate')

                if (!classes.disabled) {
                    classes.disabled = d1 === d2
                }

                return classes;
            }
        }
    };
</script>
