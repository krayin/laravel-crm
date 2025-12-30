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

<?php for($i = 0;  $i < 10; $i++): ?>
    <?php if(! $isMultiRow): ?>
        <div class="row grid grid-cols-6 gap-2.5 border-b px-4 py-4 text-gray-600 dark:border-gray-800 dark:text-gray-300">
            <div class="shimmer mb-0.5 h-6 w-6"></div>

            <div class="shimmer h-[17px] w-[100px]"></div>

            <div class="shimmer h-[17px] w-[100px]"></div>

            <div class="shimmer h-[17px] w-[100px]"></div>

            <div class="shimmer h-[17px] w-[100px]"></div>

            <div class="flex gap-2.5 place-self-end">
                <div class="shimmer h-6 w-6 p-1.5"></div>

                <div class="shimmer h-6 w-6 p-1.5"></div>
            </div>
        </div>
    <?php else: ?>
        <div class="row grid grid-cols-[2fr_1fr_1fr] gap-2.5 border-b px-4 py-2.5 text-gray-600 dark:border-gray-800 dark:text-gray-300">
            <div class="flex gap-2.5">
                <div class="shimmer h-6 w-6"></div>

                <div class="flex flex-col gap-1.5">
                    <div class="shimmer h-[19px] w-[250px]"></div>

                    <div class="shimmer h-[17px] w-[150px]"></div>

                    <div class="shimmer h-[17px] w-[150px]"></div>
                </div>
            </div>

            <div class="flex flex-col gap-1.5">
                <div class="shimmer h-[19px] w-[250px]"></div>

                <div class="shimmer h-[17px] w-[150px]"></div>

                <div class="shimmer h-[17px] w-[150px]"></div>
            </div>

            <div class="flex flex-col gap-1.5">
                <div class="shimmer h-[19px] w-[250px]"></div>

                <div class="shimmer h-[17px] w-[150px]"></div>

                <div class="shimmer h-[17px] w-[150px]"></div>
            </div>
        </div>
    <?php endif; ?>
<?php endfor; ?>
<?php /**PATH C:\laragon\www\adv-crm\packages\Webkul\Admin\src/resources/views/components/shimmer/datagrid/table/body.blade.php ENDPATH**/ ?>