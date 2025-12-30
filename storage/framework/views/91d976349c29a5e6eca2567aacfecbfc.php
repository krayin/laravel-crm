<v-datagrid-pagination
    :is-loading="isLoading"
    :available="available"
    :applied="applied"
    @changePage="changePage"
    @changePerPageOption="changePerPageOption"
>
    <?php echo e($slot); ?>

</v-datagrid-pagination>

<?php if (! $__env->hasRenderedOnce('5a84cff1-99f0-4ef9-bc3a-99e172911c3d')): $__env->markAsRenderedOnce('5a84cff1-99f0-4ef9-bc3a-99e172911c3d');
$__env->startPush('scripts'); ?>
    <script
        type="text/x-template"
        id="v-datagrid-pagination-template"
    >
        <slot
            name="pagination"
            :available="available"
            :applied="applied"
            :change-page="changePage"
            :change-per-page-option="changePerPageOption"
        >
            <template v-if="isLoading">
                <?php if (isset($component)) { $__componentOriginal869a63ef9e28062492da25c3d445f145 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal869a63ef9e28062492da25c3d445f145 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'admin::components.shimmer.datagrid.toolbar.pagination','data' => []] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('admin::shimmer.datagrid.toolbar.pagination'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(Illuminate\View\AnonymousComponent::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal869a63ef9e28062492da25c3d445f145)): ?>
<?php $attributes = $__attributesOriginal869a63ef9e28062492da25c3d445f145; ?>
<?php unset($__attributesOriginal869a63ef9e28062492da25c3d445f145); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal869a63ef9e28062492da25c3d445f145)): ?>
<?php $component = $__componentOriginal869a63ef9e28062492da25c3d445f145; ?>
<?php unset($__componentOriginal869a63ef9e28062492da25c3d445f145); ?>
<?php endif; ?>
            </template>

            <template v-else>
                <div class="flex items-center gap-x-2">
                    <p class="whitespace-nowrap text-gray-600 dark:text-gray-300 max-sm:hidden">
                        <?php echo app('translator')->get('admin::app.components.datagrid.toolbar.per-page'); ?>
                    </p>

                    <?php if (isset($component)) { $__componentOriginalaf937e0ec72fa678d3a0c6dc6c0ac5f2 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalaf937e0ec72fa678d3a0c6dc6c0ac5f2 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'admin::components.dropdown.index','data' => []] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('admin::dropdown'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(Illuminate\View\AnonymousComponent::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
                         <?php $__env->slot('toggle', null, []); ?> 
                            <div class="flex items-center gap-1">
                                <button
                                    type="button"
                                    class="inline-flex w-full max-w-max cursor-pointer appearance-none items-center justify-between gap-x-2 rounded-md border bg-white px-2.5 py-1.5 text-center leading-6 text-gray-600 transition-all marker:shadow hover:border-gray-400 focus:border-gray-400 dark:border-gray-800 dark:bg-gray-900 dark:text-gray-300 dark:hover:border-gray-400 dark:focus:border-gray-400"
                                >
                                    <span>
                                        {{ applied.pagination.perPage }}
                                    </span>
    
                                    <span class="icon-down-arrow text-2xl"></span>
                                </button>
                            </div>
                         <?php $__env->endSlot(); ?>

                         <?php $__env->slot('menu', null, []); ?> 
                            <?php if (isset($component)) { $__componentOriginal0223c8534d6a243be608c3a65289c4d0 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal0223c8534d6a243be608c3a65289c4d0 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'admin::components.dropdown.menu.item','data' => ['vFor' => 'perPageOption in available.meta.per_page_options','@click' => 'changePerPageOption(perPageOption)']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('admin::dropdown.menu.item'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(Illuminate\View\AnonymousComponent::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes(['v-for' => 'perPageOption in available.meta.per_page_options','@click' => 'changePerPageOption(perPageOption)']); ?>
                                {{ perPageOption }}
                             <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal0223c8534d6a243be608c3a65289c4d0)): ?>
<?php $attributes = $__attributesOriginal0223c8534d6a243be608c3a65289c4d0; ?>
<?php unset($__attributesOriginal0223c8534d6a243be608c3a65289c4d0); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal0223c8534d6a243be608c3a65289c4d0)): ?>
<?php $component = $__componentOriginal0223c8534d6a243be608c3a65289c4d0; ?>
<?php unset($__componentOriginal0223c8534d6a243be608c3a65289c4d0); ?>
<?php endif; ?>
                         <?php $__env->endSlot(); ?>
                     <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalaf937e0ec72fa678d3a0c6dc6c0ac5f2)): ?>
<?php $attributes = $__attributesOriginalaf937e0ec72fa678d3a0c6dc6c0ac5f2; ?>
<?php unset($__attributesOriginalaf937e0ec72fa678d3a0c6dc6c0ac5f2); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalaf937e0ec72fa678d3a0c6dc6c0ac5f2)): ?>
<?php $component = $__componentOriginalaf937e0ec72fa678d3a0c6dc6c0ac5f2; ?>
<?php unset($__componentOriginalaf937e0ec72fa678d3a0c6dc6c0ac5f2); ?>
<?php endif; ?>
                   
                    <div class="whitespace-nowrap text-gray-600 dark:text-gray-300">
                        <span>
                            {{ available.meta.from ?? 0 }} - {{ available.meta.to ?? 0 }}
                        </span>

                        <span>
                            <?php echo app('translator')->get('admin::app.components.datagrid.toolbar.of'); ?>
                        </span>

                        <span>
                            {{ available.meta.total }}
                        </span>
                       
                    </div>

                    <div class="flex items-center gap-1">
                        <div
                            class="inline-flex w-full max-w-max cursor-pointer appearance-none items-center justify-between gap-x-1 rounded-md border border-transparent p-1.5 text-center text-gray-600 transition-all marker:shadow hover:bg-gray-200 active:border-gray-300 dark:text-gray-300 dark:hover:bg-gray-800"
                            @click="changePage('previous')"
                        >
                            <span class="icon-left-arrow rtl:icon-right-arrow text-2xl"></span>
                        </div>

                        <div
                            class="inline-flex w-full max-w-max cursor-pointer appearance-none items-center justify-between gap-x-1 rounded-md border border-transparent p-1.5 text-center text-gray-600 transition-all marker:shadow hover:bg-gray-200 active:border-gray-300 dark:text-gray-300 dark:hover:bg-gray-800"
                            @click="changePage('next')"
                        >
                            <span class="icon-right-arrow rtl:icon-left-arrow text-2xl"></span>
                        </div>
                    </div>
                </div>
            </template>
        </slot>
    </script>

    <script type="module">
        app.component('v-datagrid-pagination', {
            template: '#v-datagrid-pagination-template',

            props: ['isLoading', 'available', 'applied'],

            emits: ['changePage', 'changePerPageOption'],

            methods: {
                /**
                 * Change Page.
                 *
                 * The reason for choosing the numeric approach over the URL approach is to prevent any conflicts with our existing
                 * URLs. If we were to use the URL approach, it would introduce additional arguments in the `get` method, necessitating
                 * the addition of a `url` prop. Instead, by using the numeric approach, we can let Axios handle all the query parameters
                 * using the `applied` prop. This allows for a cleaner and more straightforward implementation.
                 *
                 * @param {string|integer} directionOrPageNumber
                 * @returns {void}
                 */
                changePage(directionOrPageNumber) {
                    let newPage;

                    if (typeof directionOrPageNumber === 'string') {
                        if (directionOrPageNumber === 'previous') {
                            newPage = this.available.meta.current_page - 1;
                        } else if (directionOrPageNumber === 'next') {
                            newPage = this.available.meta.current_page + 1;
                        } else {
                            console.warn('Invalid Direction Provided : ' + directionOrPageNumber);

                            return;
                        }
                    }  else if (typeof directionOrPageNumber === 'number') {
                        newPage = directionOrPageNumber;
                    } else {
                        console.warn('Invalid Input Provided: ' + directionOrPageNumber);

                        return;
                    }

                    /**
                     * Check if the `newPage` is within the valid range.
                     */
                    if (newPage >= 1 && newPage <= this.available.meta.last_page) {
                        this.$emit('changePage', newPage);
                    } else {
                        console.warn('Invalid Page Provided: ' + newPage);
                    }
                },

                /**
                 * Change per page option.
                 *
                 * @param {integer} option
                 * @returns {void}
                 */
                changePerPageOption(option) {
                    this.$emit('changePerPageOption', option);
                },
            },
        });
    </script>
<?php $__env->stopPush(); endif; ?>
<?php /**PATH C:\laragon\www\adv-crm\packages\Webkul\Admin\src/resources/views/components/datagrid/toolbar/pagination.blade.php ENDPATH**/ ?>