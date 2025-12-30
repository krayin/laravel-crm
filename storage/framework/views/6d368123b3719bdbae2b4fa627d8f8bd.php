<template v-if="isLoading">
    <?php if (isset($component)) { $__componentOriginal53a6a59aff92302e5d442819eff9928e = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal53a6a59aff92302e5d442819eff9928e = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'admin::components.shimmer.datagrid.toolbar','data' => []] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('admin::shimmer.datagrid.toolbar'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(Illuminate\View\AnonymousComponent::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal53a6a59aff92302e5d442819eff9928e)): ?>
<?php $attributes = $__attributesOriginal53a6a59aff92302e5d442819eff9928e; ?>
<?php unset($__attributesOriginal53a6a59aff92302e5d442819eff9928e); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal53a6a59aff92302e5d442819eff9928e)): ?>
<?php $component = $__componentOriginal53a6a59aff92302e5d442819eff9928e; ?>
<?php unset($__componentOriginal53a6a59aff92302e5d442819eff9928e); ?>
<?php endif; ?>
</template>

<template v-else>
    <div class="flex items-center justify-between gap-4 rounded-t-lg border border-b-0 border-gray-200 bg-white p-4 dark:border-gray-800 dark:bg-gray-900 dark:text-gray-300 max-md:flex-wrap">
        <!-- Left Toolbar -->
        <div class="toolbarLeft flex gap-x-1">
            <?php echo e($toolbarLeftBefore); ?>

            
            <!-- Mass Actions Panel -->
            <transition-group
                tag='div'
                name="flash-group"
                enter-from-class="ltr:translate-y-full rtl:-translate-y-full"
                enter-active-class="transform transition duration-300 ease-[cubic-bezier(.4,0,.2,1)]"
                enter-to-class="ltr:translate-y-0 rtl:-translate-y-0"
                leave-from-class="ltr:translate-y-0 rtl:-translate-y-0"
                leave-active-class="transform transition duration-300 ease-[cubic-bezier(.4,0,.2,1)]"
                leave-to-class="ltr:translate-y-full rtl:-translate-y-full"
                class='fixed bottom-10 left-1/2 z-[10003] grid -translate-x-1/2 justify-items-end gap-2.5'
            >
                <div v-if="applied.massActions.indices.length">
                    <?php if (isset($component)) { $__componentOriginal4d4f2b1de021a6e7f5de47a73d24d6e7 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal4d4f2b1de021a6e7f5de47a73d24d6e7 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'admin::components.datagrid.toolbar.mass-action','data' => []] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('admin::datagrid.toolbar.mass-action'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(Illuminate\View\AnonymousComponent::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
                        <template #mass-action="{
                            available,
                            applied,
                            massActions,
                            validateMassAction,
                            performMassAction
                        }">
                            <slot
                                name="mass-action"
                                :available="available"
                                :applied="applied"
                                :mass-actions="massActions"
                                :validate-mass-action="validateMassAction"
                                :perform-mass-action="performMassAction"
                            >
                            </slot>
                        </template>
                     <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal4d4f2b1de021a6e7f5de47a73d24d6e7)): ?>
<?php $attributes = $__attributesOriginal4d4f2b1de021a6e7f5de47a73d24d6e7; ?>
<?php unset($__attributesOriginal4d4f2b1de021a6e7f5de47a73d24d6e7); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal4d4f2b1de021a6e7f5de47a73d24d6e7)): ?>
<?php $component = $__componentOriginal4d4f2b1de021a6e7f5de47a73d24d6e7; ?>
<?php unset($__componentOriginal4d4f2b1de021a6e7f5de47a73d24d6e7); ?>
<?php endif; ?>
                </div>
            </transition-group>

            <!-- Search Panel -->
            <?php if (isset($component)) { $__componentOriginal58f36baa236fdcf7210aeba123baf4fb = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal58f36baa236fdcf7210aeba123baf4fb = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'admin::components.datagrid.toolbar.search','data' => []] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('admin::datagrid.toolbar.search'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(Illuminate\View\AnonymousComponent::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
                <template #search="{
                    available,
                    applied,
                    search,
                    getSearchedValues,
                }">
                    <slot
                        name="search"
                        :available="available"
                        :applied="applied"
                        :search="search"
                        :get-searched-values="getSearchedValues"
                    >
                    </slot>
                </template>
             <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal58f36baa236fdcf7210aeba123baf4fb)): ?>
<?php $attributes = $__attributesOriginal58f36baa236fdcf7210aeba123baf4fb; ?>
<?php unset($__attributesOriginal58f36baa236fdcf7210aeba123baf4fb); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal58f36baa236fdcf7210aeba123baf4fb)): ?>
<?php $component = $__componentOriginal58f36baa236fdcf7210aeba123baf4fb; ?>
<?php unset($__componentOriginal58f36baa236fdcf7210aeba123baf4fb); ?>
<?php endif; ?>

            <?php echo e($toolbarLeftAfter); ?>

        </div>

        <!-- Right Toolbar -->
        <div class="toolbarRight flex gap-x-4">
            <?php echo e($toolbarRightBefore); ?>

            
            <!-- Pagination Panel -->
            <?php if (isset($component)) { $__componentOriginal9b35ef007f3e75ca62a80a48f27dc12e = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal9b35ef007f3e75ca62a80a48f27dc12e = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'admin::components.datagrid.toolbar.pagination','data' => []] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('admin::datagrid.toolbar.pagination'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(Illuminate\View\AnonymousComponent::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
                <template #pagination="{
                    available,
                    applied,
                    changePage,
                    changePerPageOption
                }">
                    <slot
                        name="pagination"
                        :available="available"
                        :applied="applied"
                        :change-page="changePage"
                        :change-per-page-option="changePerPageOption"
                    >
                    </slot>
                </template>
             <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal9b35ef007f3e75ca62a80a48f27dc12e)): ?>
<?php $attributes = $__attributesOriginal9b35ef007f3e75ca62a80a48f27dc12e; ?>
<?php unset($__attributesOriginal9b35ef007f3e75ca62a80a48f27dc12e); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal9b35ef007f3e75ca62a80a48f27dc12e)): ?>
<?php $component = $__componentOriginal9b35ef007f3e75ca62a80a48f27dc12e; ?>
<?php unset($__componentOriginal9b35ef007f3e75ca62a80a48f27dc12e); ?>
<?php endif; ?>

            <?php echo e($toolbarRightAfter); ?>

        </div>
    </div>
</template>
<?php /**PATH C:\laragon\www\adv-crm\packages\Webkul\Admin\src/resources/views/components/datagrid/toolbar.blade.php ENDPATH**/ ?>