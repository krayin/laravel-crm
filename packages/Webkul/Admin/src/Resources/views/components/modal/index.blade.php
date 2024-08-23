@props([
    'isActive' => false,
    'position' => 'center',
    'size'     => 'normal',
])

<v-modal
    is-active="{{ $isActive }}"
    position="{{ $position }}"
    size="{{ $size }}"
    {{ $attributes }}
>
    @isset($toggle)
        <template v-slot:toggle>
            {{ $toggle }}
        </template>
    @endisset

    @isset($header)
        <template v-slot:header="{ toggle, isOpen }">
            <div {{ $header->attributes->merge(['class' => 'flex items-center justify-between gap-2.5 border-b px-4 py-3 dark:border-gray-800']) }}>
                {{ $header }}

                <span
                    class="icon-cross-large cursor-pointer text-3xl hover:rounded-md hover:bg-gray-100 dark:hover:bg-gray-950"
                    @click="toggle"
                >
                </span>
            </div>
        </template>
    @endisset

    @isset($content)
        <template v-slot:content>
            <div {{ $content->attributes->merge(['class' => 'border-b px-4 py-2.5 dark:border-gray-800']) }}>
                {{ $content }}
            </div>
        </template>
    @endisset

    @isset($footer)
        <template v-slot:footer>
            <div {{ $content->attributes->merge(['class' => 'flex justify-end px-4 py-2.5']) }}>
                {{ $footer }}
            </div>
        </template>
    @endisset
</v-modal>

@pushOnce('scripts')
    <script
        type="text/x-template"
        id="v-modal-template"
    >
        <div>
            <div @click="toggle">
                <slot name="toggle">
                </slot>
            </div>

            <transition
                tag="div"
                name="modal-overlay"
                enter-class="duration-300 ease-[cubic-bezier(.4,0,.2,1)]"
                enter-from-class="opacity-0"
                enter-to-class="opacity-100"
                leave-class="duration-200 ease-[cubic-bezier(.4,0,.2,1)]"
                leave-from-class="opacity-100"
                leave-to-class="opacity-0"
            >
                <div
                    class="fixed inset-0 z-[10002] bg-gray-500 bg-opacity-50 transition-opacity"
                    v-show="isOpen"
                ></div>
            </transition>

            <transition
                tag="div"
                name="modal-content"
                enter-class="duration-300 ease-[cubic-bezier(.4,0,.2,1)]"
                :enter-from-class="enterFromLeaveToClasses"
                enter-to-class="translate-y-0 opacity-100"
                leave-class="duration-300 ease-[cubic-bezier(.4,0,.2,1)]"
                leave-from-class="translate-y-0 opacity-100"
                :leave-to-class="enterFromLeaveToClasses"
            >
                <div
                    class="fixed inset-0 z-[10003] transform overflow-y-auto transition"
                    v-if="isOpen"
                >
                    <div class="flex min-h-full items-end justify-center p-4 sm:items-center sm:p-0">
                        <div
                            class="box-shadow absolute z-[999] w-full max-w-[568px] overflow-hidden rounded-lg bg-white dark:bg-gray-900"
                            :class="[positionClass, sizeClass]"
                        >
                            <!-- Header Slot -->
                            <slot
                                name="header"
                                :toggle="toggle"
                                :isOpen="isOpen"
                            >
                            </slot>

                            <!-- Content Slot -->
                            <slot name="content"></slot>
                            
                            <!-- Footer Slot -->
                            <slot name="footer"></slot>
                        </div>
                    </div>
                </div>
            </transition>
        </div>
    </script>

    <script type="module">
        app.component('v-modal', {
            template: '#v-modal-template',

            props: [
                'isActive',
                'position',
                'size'
            ],

            emits: [
                'toggle',
                'open',
                'close',
            ],

            data() {
                return {
                    isOpen: this.isActive,
                };
            },

            computed: {
                positionClass() {
                    return {
                        'center': 'items-center justify-center',
                        'top-center': 'top-4',
                        'bottom-center': 'bottom-4',
                        'bottom-right': 'bottom-4 right-4',
                        'bottom-left': 'bottom-4 left-4',
                        'top-right': 'top-4 right-4',
                        'top-left': 'top-4 left-4',
                    }[this.position];
                },

                sizeClass() {
                    return {
                        'normal': 'max-w-[568px]',
                        'medium': 'max-w-[768px]',
                        'large': 'max-w-[950px]',
                    }[this.size] || 'max-w-[568px]';
                },

                enterFromLeaveToClasses() {
                    return {
                        'center': '-translate-y-4 opacity-0',
                        'top-center': '-translate-y-4 opacity-0',
                        'bottom-center': 'translate-y-4 opacity-0',
                        'bottom-right': 'translate-y-4 opacity-0',
                        'bottom-left': 'translate-y-4 opacity-0',
                        'top-right': '-translate-y-4 opacity-0',
                        'top-left': '-translate-y-4 opacity-0',
                    }[this.position];
                }
            },

            methods: {
                toggle() {
                    this.isOpen = ! this.isOpen;

                    if (this.isOpen) {
                        document.body.style.overflow = 'hidden';
                    } else {
                        document.body.style.overflow ='auto';
                    }

                    this.$emit('toggle', { isActive: this.isOpen });
                },

                open() {
                    this.isOpen = true;

                    document.body.style.overflow = 'hidden';

                    this.$emit('open', { isActive: this.isOpen });
                },

                close() {
                    this.isOpen = false;

                    document.body.style.overflow = 'auto';

                    this.$emit('close', { isActive: this.isOpen });
                }
            }
        });
    </script>
@endPushOnce