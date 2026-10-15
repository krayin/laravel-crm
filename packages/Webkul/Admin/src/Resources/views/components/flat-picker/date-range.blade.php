{{--
    A date-range picker: one control showing the selected range, which opens a panel holding the
    common presets alongside a two-month calendar.

    It replaces a pair of start/end inputs. Those made the common case — "last 30 days", "this
    month" — a manual job of picking two dates and knowing what today is, and gave no indication of
    the range as a whole.

    `start` and `end` are emitted as `Y-m-d` strings, which is the shape the dashboard's filters and
    its export URL already use, so nothing downstream has to change.
--}}
<v-date-range-picker {{ $attributes }}>
    {{ $slot }}
</v-date-range-picker>

@pushOnce('scripts')
    <script
        type="text/x-template"
        id="v-date-range-picker-template"
    >
        <div class="relative">
            <!-- The control, showing the range it currently holds. -->
            <button
                type="button"
                class="flex min-h-[39px] w-full items-center gap-2 rounded-md border border-gray-300 bg-white px-3 py-2 text-sm text-gray-600 transition-all hover:border-gray-400 dark:border-gray-800 dark:bg-gray-900 dark:text-gray-300 dark:hover:border-gray-400"
                @click.stop="isOpen = ! isOpen"
            >
                <i class="icon-calendar text-xl text-gray-400"></i>

                <span
                    class="whitespace-nowrap"
                    v-text="label"
                ></span>

                <i class="icon-down-arrow text-xl text-gray-400 ltr:ml-auto rtl:mr-auto"></i>
            </button>

            <div
                class="absolute top-full z-[1000] mt-1 flex rounded-lg border border-gray-300 bg-white shadow-lg dark:border-gray-800 dark:bg-gray-900 ltr:right-0 rtl:left-0"
                v-if="isOpen"
                @click.stop
            >
                <!--
                    The presets. Each one resolves against today, so "Last 30 Days" means the last
                    thirty days whenever it is clicked rather than a range frozen at render time.
                -->
                <div class="flex w-[160px] flex-col border-gray-200 py-2 dark:border-gray-800 ltr:border-r rtl:border-l">
                    <button
                        type="button"
                        class="cursor-pointer px-4 py-2 text-sm text-gray-600 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-950 ltr:text-left rtl:text-right"
                        :class="{ 'bg-gray-100 font-semibold text-gray-800 dark:bg-gray-950 dark:text-white': activePreset === preset.key }"
                        v-for="preset in presets"
                        :key="preset.key"
                        @click="applyPreset(preset)"
                        v-text="preset.label"
                    ></button>
                </div>

                <div class="flex flex-col">
                    <!--
                        Two months at once, so a range spanning a boundary is picked in one go.

                        Flatpickr adopts the element it is given as its "input" — marking it
                        `flatpickr-input readonly` and appending the calendar as a sibling. Pointing
                        it at a styled container therefore left that container empty but still
                        padded, showing as a blank block above the months. It is handed a dedicated
                        input instead, so only the calendar occupies the wrapper.

                        That input is hidden with a class rather than `type="hidden"`, which
                        flatpickr rewrites to a text input on adoption — leaving the raw
                        `2026-09-14 to 2026-10-14` value on display above the months. The range is
                        already reported in the footer below.
                    -->
                    <div
                        class="p-3"
                        ref="calendarHost"
                    >
                        <input
                            class="hidden"
                            ref="calendarInput"
                        >
                    </div>

                    <!-- What is currently selected, in the same format the filters carry. -->
                    <div class="flex gap-6 border-t border-gray-200 px-4 py-3 dark:border-gray-800">
                        <div>
                            <p class="text-xs text-gray-500 dark:text-gray-400">
                                @lang('admin::app.dashboard.index.start-date')
                            </p>

                            <p
                                class="text-sm font-semibold text-gray-800 dark:text-white"
                                v-text="displayDate(start)"
                            ></p>
                        </div>

                        <div>
                            <p class="text-xs text-gray-500 dark:text-gray-400">
                                @lang('admin::app.dashboard.index.end-date')
                            </p>

                            <p
                                class="text-sm font-semibold text-gray-800 dark:text-white"
                                v-text="displayDate(end)"
                            ></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </script>

    <script type="module">
        app.component('v-date-range-picker', {
            template: '#v-date-range-picker-template',

            props: {
                startDate: String,

                endDate: String,
            },

            /**
             * Declared so `change` is treated as this component's own event. Without it Vue also
             * lets the calendar input's native DOM `change` fall through to the same listener, and
             * that event — arriving last and carrying no range — overwrote the dates with
             * `undefined`, so a hand-picked range reached the reports with no `start` or `end` and
             * every report silently fell back to its default period.
             */
            emits: ['change'],

            data() {
                return {
                    isOpen: false,

                    start: this.startDate,

                    end: this.endDate,

                    /**
                     * Which preset the current range matches, so the rail shows what is selected
                     * rather than leaving every option looking equally inactive. Null when the range
                     * was picked by hand on the calendar.
                     */
                    activePreset: null,

                    datepicker: null,

                    /**
                     * True only while the calendar is being built or re-pointed, so the changes we
                     * make ourselves are not mistaken for the user's.
                     */
                    isActivating: false,
                };
            },

            computed: {
                presets() {
                    return [
                        { key: 'today', label: "@lang('admin::app.dashboard.index.date-presets.today')" },
                        { key: 'yesterday', label: "@lang('admin::app.dashboard.index.date-presets.yesterday')" },
                        { key: 'last-30-days', label: "@lang('admin::app.dashboard.index.date-presets.last-30-days')" },
                        { key: 'this-week', label: "@lang('admin::app.dashboard.index.date-presets.this-week')" },
                        { key: 'this-month', label: "@lang('admin::app.dashboard.index.date-presets.this-month')" },
                        { key: 'last-month', label: "@lang('admin::app.dashboard.index.date-presets.last-month')" },
                        { key: 'last-3-months', label: "@lang('admin::app.dashboard.index.date-presets.last-3-months')" },
                        { key: 'last-6-months', label: "@lang('admin::app.dashboard.index.date-presets.last-6-months')" },
                        { key: 'this-year', label: "@lang('admin::app.dashboard.index.date-presets.this-year')" },
                    ];
                },

                /**
                 * The range as the control shows it.
                 */
                label() {
                    if (! this.start || ! this.end) {
                        return "@lang('admin::app.dashboard.index.start-date')";
                    }

                    return `${this.displayDate(this.start)} – ${this.displayDate(this.end)}`;
                },
            },

            mounted() {
                window.addEventListener('click', this.handleClickOutside);
            },

            watch: {
                /**
                 * The calendar is built when the panel opens, not on mount: the panel is behind a
                 * `v-if`, so its container does not exist yet while the component is mounting and
                 * Flatpickr was being handed `undefined`.
                 *
                 * It is torn down when the panel closes for the same reason — `v-if` removes the
                 * host element and the calendar inside it, and a retained instance pointing at that
                 * discarded element left the reopened panel showing the presets beside a blank gap.
                 */
                isOpen(open) {
                    if (! open) {
                        this.datepicker?.destroy();

                        this.datepicker = null;

                        return;
                    }

                    this.$nextTick(() => this.activate());
                },
            },

            beforeUnmount() {
                window.removeEventListener('click', this.handleClickOutside);
            },

            methods: {
                activate() {
                    /**
                     * Rebuilding on every open would stack calendars inside the container, so an
                     * existing instance is reused and simply re-pointed at the current range.
                     */
                    if (this.datepicker) {
                        this.isActivating = true;

                        this.datepicker.setDate([this.start, this.end], false);

                        this.isActivating = false;

                        return;
                    }

                    if (! this.$refs.calendarInput) {
                        return;
                    }

                    /**
                     * Building the calendar replays the current range through `onChange`, which
                     * would read as a hand-picked range and clear the preset the rail is marking.
                     * The flag distinguishes our own setup from a real click on a day.
                     */
                    this.isActivating = true;

                    this.datepicker = new Flatpickr(this.$refs.calendarInput, {
                        mode: 'range',
                        inline: true,
                        showMonths: 2,
                        dateFormat: 'Y-m-d',
                        defaultDate: [this.start, this.end],

                        /**
                         * The dashboard reports on what has already happened, and the server clamps
                         * a future end date back to today. A range picked entirely in the future
                         * therefore came back inverted — "29 Nov - 14 Oct" — so future days are not
                         * offered in the first place.
                         */
                        maxDate: 'today',

                        /**
                         * An inline calendar is otherwise appended to the body, where it would sit
                         * outside the panel. Positioning it in the wrapper keeps it beside the
                         * presets and lets the click-outside check see it as part of the component.
                         */
                        appendTo: this.$refs.calendarHost,

                        onChange: (selectedDates) => {
                            if (this.isActivating) {
                                return;
                            }

                            /**
                             * A range is only meaningful once both ends are chosen; flatpickr fires
                             * on the first click too, which would otherwise emit a half-range.
                             */
                            if (selectedDates.length !== 2) {
                                return;
                            }

                            this.start = this.toIsoDate(selectedDates[0]);
                            this.end = this.toIsoDate(selectedDates[1]);

                            this.activePreset = null;

                            this.emitChange();
                        },
                    });

                    this.isActivating = false;
                },

                applyPreset(preset) {
                    const [start, end] = this.resolvePreset(preset.key);

                    this.start = start;
                    this.end = end;
                    this.activePreset = preset.key;

                    /**
                     * The calendar is kept in step with the rail, so the highlighted days always
                     * match the range the control reports.
                     */
                    this.isActivating = true;

                    this.datepicker?.setDate([start, end], false);

                    this.isActivating = false;

                    this.emitChange();

                    /**
                     * A preset is a complete choice, so the panel closes on it. Leaving it open
                     * meant the next click on the control dismissed the panel instead of reopening
                     * it, and hid the dashboard the range was picked to look at.
                     */
                    this.isOpen = false;
                },

                /**
                 * Each preset is resolved against today rather than stored, so the ranges stay
                 * correct however long the page has been open.
                 */
                resolvePreset(key) {
                    const today = new Date();

                    const startOfToday = new Date(today.getFullYear(), today.getMonth(), today.getDate());

                    const shiftDays = (date, days) => {
                        const copy = new Date(date);

                        copy.setDate(copy.getDate() + days);

                        return copy;
                    };

                    switch (key) {
                        case 'today':
                            return [this.toIsoDate(startOfToday), this.toIsoDate(startOfToday)];

                        case 'yesterday': {
                            const yesterday = shiftDays(startOfToday, -1);

                            return [this.toIsoDate(yesterday), this.toIsoDate(yesterday)];
                        }

                        case 'last-30-days':
                            return [this.toIsoDate(shiftDays(startOfToday, -29)), this.toIsoDate(startOfToday)];

                        case 'this-week': {
                            /**
                             * Weeks start on Sunday here, matching the calendar's own column order.
                             */
                            const weekStart = shiftDays(startOfToday, -startOfToday.getDay());

                            return [this.toIsoDate(weekStart), this.toIsoDate(startOfToday)];
                        }

                        case 'this-month':
                            return [
                                this.toIsoDate(new Date(today.getFullYear(), today.getMonth(), 1)),
                                this.toIsoDate(startOfToday),
                            ];

                        case 'last-month':
                            return [
                                this.toIsoDate(new Date(today.getFullYear(), today.getMonth() - 1, 1)),
                                this.toIsoDate(new Date(today.getFullYear(), today.getMonth(), 0)),
                            ];

                        case 'last-3-months':
                            return [
                                this.toIsoDate(new Date(today.getFullYear(), today.getMonth() - 3, today.getDate())),
                                this.toIsoDate(startOfToday),
                            ];

                        case 'last-6-months':
                            return [
                                this.toIsoDate(new Date(today.getFullYear(), today.getMonth() - 6, today.getDate())),
                                this.toIsoDate(startOfToday),
                            ];

                        case 'this-year':
                            return [
                                this.toIsoDate(new Date(today.getFullYear(), 0, 1)),
                                this.toIsoDate(startOfToday),
                            ];

                        default:
                            return [this.start, this.end];
                    }
                },

                emitChange() {
                    this.$emit('change', { start: this.start, end: this.end });
                },

                /**
                 * `Y-m-d` built from the date's own parts rather than `toISOString()`, which
                 * converts to UTC and can land on the previous day for anyone east of Greenwich.
                 */
                toIsoDate(date) {
                    const month = String(date.getMonth() + 1).padStart(2, '0');

                    const day = String(date.getDate()).padStart(2, '0');

                    return `${date.getFullYear()}-${month}-${day}`;
                },

                displayDate(value) {
                    if (! value) {
                        return '';
                    }

                    const [year, month, day] = value.split('-').map(Number);

                    return new Date(year, month - 1, day).toLocaleDateString(undefined, {
                        year: 'numeric',
                        month: 'short',
                        day: 'numeric',
                    });
                },

                handleClickOutside(e) {
                    if (! this.$el.contains(e.target)) {
                        this.isOpen = false;
                    }
                },
            },
        });
    </script>
@endPushOnce
