<v-datagrid-search
    :is-loading="isLoading"
    :available="available"
    :applied="applied"
    :src="src"
    @search="search"
    @filter="filter"
    @applySavedFilter="applySavedFilter"
>
    <?php echo e($slot); ?>

</v-datagrid-search>

<?php if (! $__env->hasRenderedOnce('76f93390-18bf-473e-befa-3ffaab41def1')): $__env->markAsRenderedOnce('76f93390-18bf-473e-befa-3ffaab41def1');
$__env->startPush('scripts'); ?>
    <script
        type="text/x-template"
        id="v-datagrid-search-template"
    >
        <slot
            name="search"
            :available="available"
            :applied="applied"
            :search="search"
            :get-searched-values="getSearchedValues"
        >
            <template v-if="isLoading">
                <?php if (isset($component)) { $__componentOriginal8ce9e918ea587709cbb1111cee6c8506 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal8ce9e918ea587709cbb1111cee6c8506 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'admin::components.shimmer.datagrid.toolbar.search','data' => []] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('admin::shimmer.datagrid.toolbar.search'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(Illuminate\View\AnonymousComponent::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal8ce9e918ea587709cbb1111cee6c8506)): ?>
<?php $attributes = $__attributesOriginal8ce9e918ea587709cbb1111cee6c8506; ?>
<?php unset($__attributesOriginal8ce9e918ea587709cbb1111cee6c8506); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal8ce9e918ea587709cbb1111cee6c8506)): ?>
<?php $component = $__componentOriginal8ce9e918ea587709cbb1111cee6c8506; ?>
<?php unset($__componentOriginal8ce9e918ea587709cbb1111cee6c8506); ?>
<?php endif; ?>
            </template>

            <template v-else>
                <div class="flex w-full items-center gap-x-1.5">
                    <!-- Search Panel -->
                    <div class="flex max-w-[445px] items-center max-sm:w-full max-sm:max-w-full">
                        <div class="relative w-full">
                            <div class="icon-search absolute top-1.5 flex items-center text-2xl ltr:left-3 rtl:right-3"></div>

                            <input
                                type="text"
                                name="search"
                                :value="getSearchedValues('all')"
                                class="block w-full rounded-lg border bg-white py-1.5 leading-6 text-gray-600 transition-all hover:border-gray-400 focus:border-gray-400 dark:border-gray-800 dark:bg-gray-900 dark:text-gray-300 dark:hover:border-gray-400 dark:focus:border-gray-400 ltr:pl-10 ltr:pr-3 rtl:pl-3 rtl:pr-10"
                                placeholder="<?php echo app('translator')->get('admin::app.components.datagrid.toolbar.search.title'); ?>"
                                autocomplete="off"
                                @keyup.enter="search"
                            >
                        </div>
                    </div>

                    <!-- Filter Panel -->
                    <?php if (isset($component)) { $__componentOriginala1ce46efabdacd3e830b42cc9abd2e40 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginala1ce46efabdacd3e830b42cc9abd2e40 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'admin::components.datagrid.toolbar.filter','data' => []] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('admin::datagrid.toolbar.filter'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(Illuminate\View\AnonymousComponent::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
                        <template #filter="{
                            available,
                            applied,
                            filters,
                            applyFilter,
                            applyColumnValues,
                            findAppliedColumn,
                            hasAnyAppliedColumnValues,
                            getAppliedColumnValues,
                            removeAppliedColumnValue,
                            removeAppliedColumnAllValues
                        }">
                            <slot
                                name="filter"
                                :available="available"
                                :applied="applied"
                                :filters="filters"
                                :apply-filter="applyFilter"
                                :apply-column-values="applyColumnValues"
                                :find-applied-column="findAppliedColumn"
                                :has-any-applied-column-values="hasAnyAppliedColumnValues"
                                :get-applied-column-values="getAppliedColumnValues"
                                :remove-applied-column-value="removeAppliedColumnValue"
                                :remove-applied-column-all-values="removeAppliedColumnAllValues"
                            >
                            </slot>
                        </template>
                     <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginala1ce46efabdacd3e830b42cc9abd2e40)): ?>
<?php $attributes = $__attributesOriginala1ce46efabdacd3e830b42cc9abd2e40; ?>
<?php unset($__attributesOriginala1ce46efabdacd3e830b42cc9abd2e40); ?>
<?php endif; ?>
<?php if (isset($__componentOriginala1ce46efabdacd3e830b42cc9abd2e40)): ?>
<?php $component = $__componentOriginala1ce46efabdacd3e830b42cc9abd2e40; ?>
<?php unset($__componentOriginala1ce46efabdacd3e830b42cc9abd2e40); ?>
<?php endif; ?>
                </div>
            </template>
        </slot>
    </script>

    <script type="module">
        app.component('v-datagrid-search', {
            template: '#v-datagrid-search-template',

            props: ['isLoading', 'available', 'applied', 'src'],

            emits: ['search', 'filter', 'applySavedFilter'],

            data() {
                return {
                    filters: {
                        columns: [],
                    },
                };
            },

            mounted() {
                this.filters.columns = this.applied.filters.columns.filter((column) => column.index === 'all');
            },

            methods: {
                /**
                 * Perform a search operation based on the input value.
                 *
                 * @param {Event} $event
                 * @returns {void}
                 */
                search($event) {
                    let requestedValue = $event.target.value;

                    let appliedColumn = this.filters.columns.find(column => column.index === 'all');

                    if (! requestedValue) {
                        appliedColumn.value = [];

                        this.$emit('search', this.filters);

                        return;
                    }

                    if (appliedColumn) {
                        appliedColumn.value = [requestedValue];
                    } else {
                        this.filters.columns.push({
                            index: 'all',
                            value: [requestedValue]
                        });
                    }

                    this.$emit('search', this.filters);
                },

                filter(filter) {
                    this.$emit('filter', filter);
                },

                applySavedFilter(filter) {
                    this.$emit('applySavedFilter', filter);
                },

                /**
                 * Get the searched values for a specific column.
                 *
                 * @param {string} columnIndex
                 * @returns {Array}
                 */
                getSearchedValues(columnIndex) {
                    let appliedColumn = this.filters.columns.find(column => column.index === 'all');

                    return appliedColumn?.value ?? [];
                },
            },
        });
    </script>
<?php $__env->stopPush(); endif; ?>
<?php /**PATH C:\laragon\www\adv-crm\packages\Webkul\Admin\src/resources/views/components/datagrid/toolbar/search.blade.php ENDPATH**/ ?>