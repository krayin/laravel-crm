<v-flash-item
    v-for='flash in flashes'
    :key='flash.uid'
    :flash="flash"
    @onRemove="remove($event)"
>
</v-flash-item>

@pushOnce('scripts')
    <script
        type="text/x-template"
        id="v-flash-item-template"
    >
        <div
            class="flex w-max items-start justify-between gap-2 rounded-lg bg-white p-3 shadow-[0px_10px_20px_0px_rgba(0,0,0,0.12)] dark:bg-gray-950"
            :style="typeStyles[flash.type]['container']"
            @mouseenter="pauseTimer"
            @mouseleave="resumeTimer"
        >
            <!-- Icon -->
            <span
                class="icon-toast-done rounded-full bg-white text-2xl dark:bg-gray-900"
                :class="iconClasses[flash.type]"
                :style="typeStyles[flash.type]['icon']"
            ></span>

            <div class="flex flex-col gap-1.5">
                <!-- Heading -->
                <p class="text-base font-semibold dark:text-white">
                    @{{ typeHeadings[flash.type] }}
                </p>

                <!-- Message -->
                <p
                    class="flex items-center break-all text-sm dark:text-white"
                    :style="typeStyles[flash.type]['message']"
                >

                    @{{ flash.message }}
                </p>
            </div>

            <button
                class="relative ml-4 inline-flex rounded-full bg-white p-1.5 text-gray-400 dark:bg-gray-950"
                @click="remove"
            >
                <span class="icon-cross-large text-2xl text-slate-800"></span>

                <svg class="absolute inset-0 h-full w-full -rotate-90" viewBox="0 0 24 24">
                    <circle
                        class="text-gray-200"
                        stroke-width="1.5"
                        stroke="#D0D4DA"
                        fill="transparent"
                        r="10"
                        cx="12"
                        cy="12"
                    />
                    
                    <circle
                        class="text-blue-600 transition-all duration-100 ease-out"
                        stroke-width="1.5"
                        :stroke-dasharray="circumference"
                        :stroke-dashoffset="strokeDashoffset"
                        stroke-linecap="round"
                        :stroke="typeStyles[flash.type]['stroke']"
                        fill="transparent"
                        r="10"
                        cx="12"
                        cy="12"
                    />
                </svg>
            </button>
        </div>
    </script>

    <script type="module">
        app.component('v-flash-item', {
            template: '#v-flash-item-template',

            props: ['flash'],

            data() {
                return {
                    iconClasses: {
                        success: 'icon-success',

                        error: 'icon-error',

                        warning: 'icon-warning',

                        info: 'icon-info',
                    },

                    typeHeadings: {
                        success: "@lang('admin::app.components.flash-group.success')",

                        error: "@lang('admin::app.components.flash-group.error')",

                        warning: "@lang('admin::app.components.flash-group.warning')",

                        info: "@lang('admin::app.components.flash-group.info')",
                    },

                    typeStyles: {
                        success: {
                            container: 'border-left: 8px solid #16A34A',

                            icon: 'color: #16A34A',

                            stroke: '#16A34A',
                        },

                        error: {
                            container: 'border-left: 8px solid #FF4D50',

                            icon: 'color: #FF4D50',

                            stroke: '#FF4D50',
                        },

                        warning: {
                            container: 'border-left: 8px solid #FBAD15',

                            icon: 'color: #FBAD15',

                            stroke: '#FBAD15',
                        },

                        info: {
                            container: 'border-left: 8px solid #0E90D9',

                            icon: 'color: #0E90D9',

                            stroke: '#0E90D9',
                        },
                    },

                    duration: 5000,

                    progress: 0,
                    
                    circumference: 2 * Math.PI * 10,

                    timer: null,

                    isPaused: false,
                    
                    remainingTime: 5000,
                };
            },

            computed: {
                strokeDashoffset() {
                    return this.circumference - (this.progress / 100) * this.circumference;
                }
            },

            created() {
                this.startTimer();
            },

            beforeUnmount() {
                this.stopTimer();
            },

            methods: {
                remove() {
                    this.$emit('onRemove', this.flash)
                },

                startTimer() {
                    const interval = 100;
                    
                    const step = (100 / (this.duration / interval));

                    this.timer = setInterval(() => {
                        if (! this.isPaused) {
                            this.progress += step;

                            this.remainingTime -= interval;

                            if (this.progress >= 100) {
                                this.stopTimer();
                                this.remove();
                            }
                        }
                    }, interval);
                },

                stopTimer() {
                    clearInterval(this.timer);
                },

                pauseTimer() {
                    this.isPaused = true;
                },

                resumeTimer() {
                    this.isPaused = false;
                },
            }
        });
    </script>
@endpushOnce
