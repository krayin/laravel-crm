<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag; ?>
<?php foreach($attributes->onlyProps([
    'isActive' => true,
]) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
} ?>
<?php $attributes = $attributes->exceptProps([
    'isActive' => true,
]); ?>
<?php foreach (array_filter(([
    'isActive' => true,
]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
} ?>
<?php $__defined_vars = get_defined_vars(); ?>
<?php foreach ($attributes as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
} ?>
<?php unset($__defined_vars); ?>

<div <?php echo e($attributes->merge(['class' => 'box-shadow rounded-lg border border-gray-200 dark:border-gray-800 bg-white dark:bg-gray-900'])); ?>>
    <v-accordion
        is-active="<?php echo e($isActive); ?>"
        <?php echo e($attributes); ?>

    >
        <?php if (isset($component)) { $__componentOriginald4a2ee3b74458b25b1bb9b9a154dc326 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginald4a2ee3b74458b25b1bb9b9a154dc326 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'admin::components.shimmer.accordion.index','data' => ['class' => 'h-[271px] w-[360px]']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('admin::shimmer.accordion'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(Illuminate\View\AnonymousComponent::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'h-[271px] w-[360px]']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginald4a2ee3b74458b25b1bb9b9a154dc326)): ?>
<?php $attributes = $__attributesOriginald4a2ee3b74458b25b1bb9b9a154dc326; ?>
<?php unset($__attributesOriginald4a2ee3b74458b25b1bb9b9a154dc326); ?>
<?php endif; ?>
<?php if (isset($__componentOriginald4a2ee3b74458b25b1bb9b9a154dc326)): ?>
<?php $component = $__componentOriginald4a2ee3b74458b25b1bb9b9a154dc326; ?>
<?php unset($__componentOriginald4a2ee3b74458b25b1bb9b9a154dc326); ?>
<?php endif; ?>

        <?php if(isset($header)): ?>
            <template v-slot:header="{ toggle, isOpen }">
                <div <?php echo e($header->attributes->merge(['class' => 'flex items-center justify-between p-1.5'])); ?>>
                    <?php echo e($header); ?>


                    <span
                        :class="`cursor-pointer rounded-md p-1.5 text-2xl transition-all hover:bg-gray-100 dark:hover:bg-gray-950 ${isOpen ? 'icon-up-arrow' : 'icon-down-arrow'}`"
                        @click="toggle"
                    ></span>
                </div>
            </template>
        <?php endif; ?>

        <?php if(isset($content)): ?>
            <template v-slot:content="{ isOpen }">
                <div
                    <?php echo e($content->attributes->merge(['class' => 'px-4 pb-4'])); ?>

                    v-show="isOpen"
                >
                    <?php echo e($content); ?>

                </div>
            </template>
        <?php endif; ?>
    </v-accordion>
</div>

<?php if (! $__env->hasRenderedOnce('8620c1e7-1eb8-4695-b271-ef3ff92b25d6')): $__env->markAsRenderedOnce('8620c1e7-1eb8-4695-b271-ef3ff92b25d6');
$__env->startPush('scripts'); ?>
    <script
        type="text/x-template"
        id="v-accordion-template"
    >
        <div>
            <slot
                name="header"
                :toggle="toggle"
                :isOpen="isOpen"
            >
                Default Header
            </slot>

            <slot
                name="content"
                :isOpen="isOpen"
            >
                Default Content
            </slot>
        </div>
    </script>

    <script type="module">
        app.component('v-accordion', {
            template: '#v-accordion-template',

            props: [
                'isActive',
            ],

            data() {
                return {
                    isOpen: this.isActive,
                };
            },

            methods: {
                toggle() {
                    this.isOpen = ! this.isOpen;

                    this.$emit('toggle', { isActive: this.isOpen });
                },
            },
        });
    </script>
<?php $__env->stopPush(); endif; ?>
<?php /**PATH C:\laragon\www\adv-crm\packages\Webkul\Admin\src/resources/views/components/accordion/index.blade.php ENDPATH**/ ?>