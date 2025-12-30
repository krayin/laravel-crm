<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag; ?>
<?php foreach($attributes->onlyProps(['isMultiRow' => false]) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
} ?>
<?php $attributes = $attributes->exceptProps(['isMultiRow' => false]); ?>
<?php foreach (array_filter((['isMultiRow' => false]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
} ?>
<?php $__defined_vars = get_defined_vars(); ?>
<?php foreach ($attributes as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
} ?>
<?php unset($__defined_vars); ?>

<?php if(! $isMultiRow): ?>
    <div class="row grid grid-cols-6 items-center gap-2.5 border-b px-4 py-2.5 dark:border-gray-800">
        <div class="shimmer h-[26px] w-6"></div>

        <div class="shimmer h-[17px] w-[100px]"></div>

        <div class="shimmer h-[17px] w-[100px]"></div>

        <div class="shimmer h-[17px] w-[100px]"></div>

        <div class="shimmer h-[17px] w-[100px]"></div>

        <div class="shimmer h-[17px] w-[100px] place-self-end"></div>
    </div>
<?php else: ?>
    <div class="row grid grid-cols-[2fr_1fr_1fr] items-center gap-2.5 border-b px-4 py-2.5 dark:border-gray-800">
        <div class="flex items-center gap-2.5">
            <div class="shimmer h-6 w-6"></div>

            <div class="shimmer h-[17px] w-[200px]"></div>
        </div>

        <div class="shimmer h-[17px] w-[200px]"></div>

        <div class="shimmer h-[17px] w-[200px]"></div>
    </div>
<?php endif; ?>
<?php /**PATH C:\laragon\www\adv-crm\packages\Webkul\Admin\src/resources/views/components/shimmer/datagrid/table/head.blade.php ENDPATH**/ ?>