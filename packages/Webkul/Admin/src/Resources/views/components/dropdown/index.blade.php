@props(['position' => 'bottom-left'])

<v-dropdown position="{{ $position }}" {{ $attributes->merge(['class' => 'relative']) }}>
    @isset($toggle)
        {{ $toggle }}

        <template v-slot:toggle>
            {{ $toggle }}
        </template>
    @endisset

    @isset($content)
        <template #content="{ isActive, positionStyles }">
            <div
                {{ $content->attributes->merge(['class' => 'absolute z-10 w-max rounded bg-white py-5 shadow-[0px_10px_20px_0px_#0000001F] dark:bg-gray-900 border border-gray-300 dark:border-gray-800']) }}
                :style="positionStyles"
                v-show="isActive"
            >
                {{ $content }}
            </div>
        </template>
    @endisset

    @isset($menu)
        <template #menu="{ isActive, positionStyles }">
            <ul
                {{ $menu->attributes->merge(['class' => 'absolute z-10 w-max rounded bg-white py-4 shadow-[0px_10px_20px_0px_#0000001F] dark:bg-gray-900']) }}
                :style="positionStyles"
                v-show="isActive"
            >
                {{ $menu }}
            </ul>
        </template>
    @endisset
</v-dropdown>

@pushOnce('scripts')
    <script
        type="text/x-template"
        id="v-dropdown-template"
    >
        <div>
            <div
                class="flex select-none items-center"
                ref="toggleBlock"
                @click="toggle()"
            >
                <slot name="toggle"></slot>
            </div>

            <transition
                tag="div"
                name="dropdown"
                enter-active-class="transition duration-100 ease-out"
                enter-from-class="scale-95 transform opacity-0"
                enter-to-class="scale-100 transform opacity-100"
                leave-active-class="transition duration-75 ease-in"
                leave-from-class="scale-100 transform opacity-100"
                leave-to-class="scale-95 transform opacity-0"
            >
                <div>
                    <slot
                        name="content"
                        :position-styles="positionStyles"
                        :is-active="isActive"
                    ></slot>

                    <slot
                        name="menu"
                        :position-styles="positionStyles"
                        :is-active="isActive"
                    ></slot>
                </div>
            </transition>
        </div>
    </script>

    <script type="module">
        app.component('v-dropdown', {
            template: '#v-dropdown-template',

            props: {
                position: String,

                closeOnClick: {
                    type: Boolean,
                    required: false,
                    default: true
                },
            },

            data() {
                return {
                    toggleBlockWidth: 0,

                    toggleBlockHeight: 0,

                    isActive: false,
                };
            },

            created() {
                window.addEventListener('click', this.handleFocusOut);
            },

            mounted() {
                this.toggleBlockWidth = this.$refs.toggleBlock.clientWidth;

                this.toggleBlockHeight = this.$refs.toggleBlock.clientHeight;
            },

            beforeDestroy() {
                window.removeEventListener('click', this.handleFocusOut);
            },

            computed: {
                positionStyles() {
                    switch (this.position) {
                        case 'bottom-left':
                            return [
                                `min-width: ${this.toggleBlockWidth}px`,
                                `top: ${this.toggleBlockHeight}px`,
                                'left: 0',
                            ];

                        case 'bottom-right':
                            return [
                                `min-width: ${this.toggleBlockWidth}px`,
                                `top: ${this.toggleBlockHeight}px`,
                                'right: 0',
                            ];

                        case 'top-left':
                            return [
                                `min-width: ${this.toggleBlockWidth}px`
                                `bottom: ${this.toggleBlockHeight*2}px`,
                                'left: 0',
                            ];

                        case 'top-right':
                            return [
                                `min-width: ${this.toggleBlockWidth}px`
                                `bottom: ${this.toggleBlockHeight*2}px`,
                                'right: 0',
                            ];

                        default:
                            return [
                                `min-width: ${this.toggleBlockWidth}px`
                                `top: ${this.toggleBlockHeight}px`,
                                'left: 0',
                            ];
                    }
                },
            },

            methods: {
                toggle() {
                    this.isActive = ! this.isActive;
                },

                handleFocusOut(e) {
                    if (! this.$el.contains(e.target) || (this.closeOnClick && this.$el.children[1].contains(e.target))) {
                        this.isActive = false;
                    }
                },
            },
        });
    </script>
@endPushOnce
