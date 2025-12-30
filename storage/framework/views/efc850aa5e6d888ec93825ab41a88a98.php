<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag; ?>
<?php foreach($attributes->onlyProps([
    'isActive' => false,
    'position' => 'right',
    'width'    => '500px',
]) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
} ?>
<?php $attributes = $attributes->exceptProps([
    'isActive' => false,
    'position' => 'right',
    'width'    => '500px',
]); ?>
<?php foreach (array_filter(([
    'isActive' => false,
    'position' => 'right',
    'width'    => '500px',
]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
} ?>
<?php $__defined_vars = get_defined_vars(); ?>
<?php foreach ($attributes as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
} ?>
<?php unset($__defined_vars); ?>

<v-drawer
    <?php echo e($attributes); ?>

    is-active="<?php echo e($isActive); ?>"
    position="<?php echo e($position); ?>"
    width="<?php echo e($width); ?>"
>
    <?php if(isset($toggle)): ?>
        <template v-slot:toggle>
            <?php echo e($toggle); ?>

        </template>
    <?php endif; ?>

    <?php if(isset($header)): ?>
        <template v-slot:header="{ close }">
            <div <?php echo e($header->attributes->merge(['class' => 'flex justify-between items-center gap-y-2.5 border-b p-3 dark:border-gray-800 max-sm:px-4'])); ?>>
                <?php echo e($header); ?>


                <div class="w-full flex-1 ltr:right-3 ltr:text-right rtl:left-3 rtl:text-left">
                    <span
                        class="icon-cross-large cursor-pointer text-3xl hover:rounded-md hover:bg-gray-100 dark:hover:bg-gray-950"
                        @click="close"
                    >
                    </span>
                </div>
            </div>
        </template>
    <?php endif; ?>

    <?php if(isset($content)): ?>
        <template v-slot:content>
            <div <?php echo e($content->attributes->merge(['class' => 'flex-1 overflow-auto p-3 max-sm:px-4'])); ?>>
                <?php echo e($content); ?>

            </div>
        </template>
    <?php endif; ?>

    <?php if(isset($footer)): ?>
        <template v-slot:footer>
            <div <?php echo e($footer->attributes->merge(['class' => 'pb-8'])); ?>>
                <?php echo e($footer); ?>

            </div>
        </template>
    <?php endif; ?>
</v-drawer>

<?php if (! $__env->hasRenderedOnce('19df59e9-dc56-4269-8fbf-cc9aee273fd1')): $__env->markAsRenderedOnce('19df59e9-dc56-4269-8fbf-cc9aee273fd1');
$__env->startPush('scripts'); ?>
    <script
        type="text/x-template"
        id="v-drawer-template"
    >
        <div>
            <!-- Toggler -->
            <div @click="open">
                <slot name="toggle">
                </slot>
            </div>

            <!-- Overlay -->
            <transition
                tag="div"
                name="drawer-overlay"
                enter-class="ease-out duration-300"
                enter-from-class="opacity-0"
                enter-to-class="opacity-100"
                leave-class="ease-in duration-200"
                leave-from-class="opacity-100"
                leave-to-class="opacity-0"
            >
                <div
                    class="fixed inset-0 z-[10002] bg-gray-500 bg-opacity-50 transition-opacity"
                    v-show="isOpen"
                ></div>
            </transition>

            <!-- Content -->
            <transition
                tag="div"
                name="drawer"
                :enter-from-class="enterFromLeaveToClasses"
                enter-active-class="transform transition duration-200 ease-in-out"
                enter-to-class="translate-x-0"
                leave-from-class="translate-x-0"
                leave-active-class="transform transition duration-200 ease-in-out"
                :leave-to-class="enterFromLeaveToClasses"
            >
                <div
                    class="fixed z-[10003] m-3 rounded-lg bg-white dark:bg-gray-900 max-sm:!w-[calc(100%-24px)]"
                    :class="{
                        'inset-x-0 top-0': position == 'top',
                        'inset-x-0 bottom-0': position == 'bottom',
                        'inset-y-0 ltr:right-0 rtl:left-0': position == 'right',
                        'inset-y-0 ltr:left-0 rtl:right-0': position == 'left'
                    }"
                    :style="'width:' + width"
                    v-if="isOpen"
                >
                    <div class="pointer-events-auto h-full w-full overflow-auto rounded-lg bg-white dark:bg-gray-900">
                        <div class="flex h-full w-full flex-col">
                            <div class="min-h-0 min-w-0 flex-1 overflow-auto">
                                <div class="flex h-full flex-col">
                                    <!-- Header Slot-->
                                    <slot
                                        name="header"
                                        :close="close"
                                    >
                                    </slot>

                                    <!-- Content Slot -->
                                    <slot name="content"></slot>

                                    <!-- Footer Slot -->
                                    <slot name="footer"></slot>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </transition>
        </div>
    </script>

    <script type="module">
        app.component('v-drawer', {
            template: '#v-drawer-template',

            props: [
                'isActive',
                'position',
                'width'
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

            watch: {
                isActive: function(newVal, oldVal) {
                    this.isOpen = newVal;
                }
            },

            computed: {
                enterFromLeaveToClasses() {
                    if (this.position == 'top') {
                        return '-translate-y-full';
                    } else if (this.position == 'bottom') {
                        return 'translate-y-full';
                    } else if (this.position == 'left') {
                        return 'ltr:-translate-x-full rtl:translate-x-full';
                    } else if (this.position == 'right') {
                        return 'ltr:translate-x-full rtl:-translate-x-full';
                    }
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
            },
        });
    </script>
<?php $__env->stopPush(); endif; ?><?php /**PATH C:\laragon\www\adv-crm\packages\Webkul\Admin\src/resources/views/components/drawer/index.blade.php ENDPATH**/ ?>