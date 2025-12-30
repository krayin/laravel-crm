<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag; ?>
<?php foreach($attributes->onlyProps([
    'name'        => null,
    'controlName' => '',
]) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
} ?>
<?php $attributes = $attributes->exceptProps([
    'name'        => null,
    'controlName' => '',
]); ?>
<?php foreach (array_filter(([
    'name'        => null,
    'controlName' => '',
]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
} ?>
<?php $__defined_vars = get_defined_vars(); ?>
<?php foreach ($attributes as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
} ?>
<?php unset($__defined_vars); ?>

<v-error-message
    <?php echo e($attributes); ?>

    name="<?php echo e($name ?? $controlName); ?>"
    v-slot="{ message }"
>
    <p
        <?php echo e($attributes->merge(['class' => 'mt-1 text-xs italic text-red-600'])); ?>

        v-text="message"
    >
    </p>
</v-error-message>
<?php /**PATH C:\laragon\www\adv-crm\packages\Webkul\Admin\src/resources/views/components/form/control-group/error.blade.php ENDPATH**/ ?>