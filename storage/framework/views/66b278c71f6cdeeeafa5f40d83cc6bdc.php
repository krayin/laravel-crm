<v-datagrid-filter
    :src="src"
    :is-loading="isLoading"
    :available="available"
    :applied="applied"
    @applyFilters="filter"
    @applySavedFilter="applySavedFilter"
>
    <?php echo e($slot); ?>

</v-datagrid-filter>

<?php if (! $__env->hasRenderedOnce('aef25697-5e0e-4b06-bea4-e7d40049a6a1')): $__env->markAsRenderedOnce('aef25697-5e0e-4b06-bea4-e7d40049a6a1');
$__env->startPush('scripts'); ?>
    <script
        type="text/x-template"
        id="v-datagrid-filter-template"
    >
        <slot
            name="filter"
            :available="available"
            :applied="applied"
            :filters="filters"
            :apply-filters="applyFilters"
            :apply-column-values="applyColumnValues"
            :find-applied-column="findAppliedColumn"
            :has-any-applied-column-values="hasAnyAppliedColumnValues"
            :get-applied-column-values="getAppliedColumnValues"
            :remove-applied-column-value="removeAppliedColumnValue"
            :remove-applied-column-all-values="removeAppliedColumnAllValues"
        >
            <template v-if="isLoading">
                <?php if (isset($component)) { $__componentOriginal30c390bbdb2e5520a1556559f6b9f4c0 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal30c390bbdb2e5520a1556559f6b9f4c0 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'admin::components.shimmer.datagrid.toolbar.filter','data' => []] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('admin::shimmer.datagrid.toolbar.filter'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(Illuminate\View\AnonymousComponent::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal30c390bbdb2e5520a1556559f6b9f4c0)): ?>
<?php $attributes = $__attributesOriginal30c390bbdb2e5520a1556559f6b9f4c0; ?>
<?php unset($__attributesOriginal30c390bbdb2e5520a1556559f6b9f4c0); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal30c390bbdb2e5520a1556559f6b9f4c0)): ?>
<?php $component = $__componentOriginal30c390bbdb2e5520a1556559f6b9f4c0; ?>
<?php unset($__componentOriginal30c390bbdb2e5520a1556559f6b9f4c0); ?>
<?php endif; ?>
            </template>

            <template v-else>
                <?php if (isset($component)) { $__componentOriginal9bfb526197f1d7304e7fade44c26fbb8 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal9bfb526197f1d7304e7fade44c26fbb8 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'admin::components.drawer.index','data' => ['width' => '350px','ref' => 'filterDrawer']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('admin::drawer'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(Illuminate\View\AnonymousComponent::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes(['width' => '350px','ref' => 'filterDrawer']); ?>
                     <?php $__env->slot('toggle', null, []); ?> 
                        <div class="relative flex cursor-pointer items-center rounded-md bg-sky-100 px-4 py-[9px] font-semibold text-sky-600 dark:bg-brandColor dark:text-white">
                            <?php echo app('translator')->get('admin::app.components.datagrid.toolbar.filter.title'); ?>

                            <span
                                class="absolute right-2 top-2 h-1.5 w-1.5 rounded-full bg-sky-600 dark:bg-white"
                                v-if="hasAnyAppliedColumn()"
                            >
                            </span>
                        </div>
                     <?php $__env->endSlot(); ?>

                     <?php $__env->slot('header', null, ['class' => 'p-3.5']); ?> 
                        <!-- Apply Filter Title -->
                        <div
                            v-if="! isShowSavedFilters"
                            class="flex items-center justify-between"
                        >
                            <p class="text-xl font-semibold text-gray-800 dark:text-white">
                                <?php echo app('translator')->get('admin::app.components.datagrid.filters.title'); ?>
                            </p>

                            <div
                                v-if="hasAnyAppliedColumn() || isFilterDirty"
                                class="cursor-pointer text-xs font-medium leading-8 text-brandColor hover:underline ltr:ml-11 ltr:mr-11 rtl:mr-11"
                                @click="removeAllAppliedFilters()"
                            >
                                <?php echo app('translator')->get('admin::app.components.datagrid.filters.custom-filters.clear-all'); ?>
                            </div>
                        </div>

                        <!-- Save Filter Title -->
                        <div v-else class="flex items-center gap-x-1">
                            <i
                                class="icon-left-arrow rtl:icon-right-arrow mt-0.5 cursor-pointer text-[26px] !font-bold hover:rounded-md hover:bg-gray-100 dark:hover:bg-gray-950"
                                @click="backToFilters"
                            >
                            </i>

                            <p class="text-xl font-semibold text-gray-800 dark:text-white">
                                {{ applied.savedFilterId ? '<?php echo app('translator')->get('admin::app.components.datagrid.toolbar.filter.update-filter'); ?>' : '<?php echo app('translator')->get('admin::app.components.datagrid.toolbar.filter.save-filter'); ?>' }}
                            </p>
                        </div>
                     <?php $__env->endSlot(); ?>

                     <?php $__env->slot('content', null, ['class' => '!p-0']); ?> 
                        <template v-if="! isShowSavedFilters">
                            <!-- Quick Filters Accordion -->
                            <?php if (isset($component)) { $__componentOriginale6717d929d3edd1e7d9927d6c11ccc02 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginale6717d929d3edd1e7d9927d6c11ccc02 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'admin::components.accordion.index','data' => ['class' => 'select-none rounded-none !border-none !shadow-none','vIf' => 'savedFilters.available.length > 0']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('admin::accordion'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(Illuminate\View\AnonymousComponent::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'select-none rounded-none !border-none !shadow-none','v-if' => 'savedFilters.available.length > 0']); ?>
                                 <?php $__env->slot('header', null, ['class' => 'px-4']); ?> 
                                    <p class="w-full text-base font-semibold text-gray-800 dark:text-white">
                                        <?php echo app('translator')->get('admin::app.components.datagrid.toolbar.filter.quick-filters'); ?>
                                    </p>
                                 <?php $__env->endSlot(); ?>

                                 <?php $__env->slot('content', null, ['class' => 'border-b !p-0 dark:border-gray-800']); ?> 
                                    <div class="grid !p-0">
                                        <!-- Listing of Quick Filters (Saved Filters) -->
                                        <div v-for="(filter,index) in savedFilters.available">
                                            <div
                                                class="flex cursor-pointer items-center justify-between px-4 py-1.5 text-gray-700 hover:bg-gray-50 dark:text-white dark:hover:bg-gray-950"
                                                :class="{ 'bg-gray-50 dark:bg-gray-950 font-semibold': applied.savedFilterId == filter.id }"
                                                @click="applySavedFilter(filter)"
                                            >
                                                <span class="text-xs font-medium text-gray-800 dark:text-white">{{ filter.name }}</span>

                                                <span
                                                    class="icon-cross-large rounded p-1.5 text-lg hover:bg-gray-200 dark:hover:bg-gray-800"
                                                    @click.stop="deleteSavedFilter(filter)"
                                                >
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                 <?php $__env->endSlot(); ?>
                             <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginale6717d929d3edd1e7d9927d6c11ccc02)): ?>
<?php $attributes = $__attributesOriginale6717d929d3edd1e7d9927d6c11ccc02; ?>
<?php unset($__attributesOriginale6717d929d3edd1e7d9927d6c11ccc02); ?>
<?php endif; ?>
<?php if (isset($__componentOriginale6717d929d3edd1e7d9927d6c11ccc02)): ?>
<?php $component = $__componentOriginale6717d929d3edd1e7d9927d6c11ccc02; ?>
<?php unset($__componentOriginale6717d929d3edd1e7d9927d6c11ccc02); ?>
<?php endif; ?>

                            <!-- Filters Accordion -->
                            <?php if (isset($component)) { $__componentOriginale6717d929d3edd1e7d9927d6c11ccc02 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginale6717d929d3edd1e7d9927d6c11ccc02 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'admin::components.accordion.index','data' => ['class' => 'select-none !rounded-none !border-none !shadow-none']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('admin::accordion'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(Illuminate\View\AnonymousComponent::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'select-none !rounded-none !border-none !shadow-none']); ?>
                                 <?php $__env->slot('header', null, ['class' => 'px-4']); ?> 
                                    <p class="text-base font-semibold text-gray-800 dark:text-white">
                                        <?php echo app('translator')->get('admin::app.components.datagrid.toolbar.filter.custom-filters'); ?>
                                    </p>
                                 <?php $__env->endSlot(); ?>

                                 <?php $__env->slot('content', null, ['class' => '']); ?> 
                                    <!-- All Filters -->
                                    <div v-for="column in available.columns">
                                        <div v-if="column.filterable">
                                            <!-- Boolean -->
                                            <div v-if="column.type === 'boolean'">
                                                <!-- Dropdown -->
                                                <template v-if="column.filterable_type === 'dropdown'">
                                                    <div class="flex items-center justify-between">
                                                        <p
                                                            class="text-xs font-medium text-gray-800 dark:text-white"
                                                            v-text="column.label"
                                                        >
                                                        </p>

                                                        <div
                                                            class="flex items-center gap-x-1.5"
                                                            @click="removeAppliedColumnAllValues(column.index)"
                                                        >
                                                            <p
                                                                class="cursor-pointer text-xs font-medium leading-6 text-brandColor"
                                                                v-if="hasAnyAppliedColumnValues(column.index)"
                                                            >
                                                                <?php echo app('translator')->get('admin::app.components.datagrid.filters.custom-filters.clear-all'); ?>
                                                            </p>
                                                        </div>
                                                    </div>

                                                    <div class="mb-2 mt-1.5">
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
                                                                <button
                                                                    type="button"
                                                                    class="inline-flex w-full cursor-pointer appearance-none items-center justify-between gap-x-2 rounded-md border bg-white px-2.5 py-1.5 text-center leading-6 text-gray-600 transition-all marker:shadow hover:border-gray-400 focus:border-gray-400 dark:border-gray-800 dark:bg-gray-900 dark:text-gray-300 dark:hover:border-gray-400 dark:focus:border-gray-400"
                                                                >
                                                                    <!-- If Allow Multiple Values -->
                                                                    <span
                                                                        class="text-sm text-gray-400 dark:text-gray-400"
                                                                        v-text="'<?php echo app('translator')->get('admin::app.components.datagrid.filters.select'); ?>'"
                                                                        v-if="column.allow_multiple_values"
                                                                    >
                                                                    </span>

                                                                    <!-- If Allow Single Value -->
                                                                    <span
                                                                        class="text-sm text-gray-400 dark:text-gray-400"
                                                                        v-text="column.filterable_options.find((option => option.value === getAppliedColumnValues(column.index)))?.label ?? '<?php echo app('translator')->get('admin::app.components.datagrid.filters.select'); ?>'"
                                                                        v-else
                                                                    >
                                                                    </span>

                                                                    <span class="icon-down-arrow text-2xl"></span>
                                                                </button>
                                                             <?php $__env->endSlot(); ?>

                                                             <?php $__env->slot('menu', null, []); ?> 
                                                                <?php if (isset($component)) { $__componentOriginal0223c8534d6a243be608c3a65289c4d0 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal0223c8534d6a243be608c3a65289c4d0 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'admin::components.dropdown.menu.item','data' => ['vFor' => 'option in column.filterable_options','vText' => 'option.label','@click' => 'addFilter(option.value, column)']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('admin::dropdown.menu.item'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(Illuminate\View\AnonymousComponent::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes(['v-for' => 'option in column.filterable_options','v-text' => 'option.label','@click' => 'addFilter(option.value, column)']); ?>
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
                                                    </div>

                                                    <div class="mb-4 flex flex-wrap gap-2">
                                                        <!-- If Allow Multiple Values -->
                                                        <template v-if="column.allow_multiple_values">
                                                            <p
                                                                class="flex items-center rounded bg-gray-600 px-2 py-1 font-semibold text-white"
                                                                v-for="appliedColumnValue in getAppliedColumnValues(column.index)"
                                                            >
                                                                <!-- Retrieving the label from the options based on the applied column value. -->
                                                                <span v-text="column.filterable_options.find((option => option.value == appliedColumnValue)).label"></span>

                                                                <span
                                                                    class="icon-cross-large-large cursor-pointer text-lg text-white ltr:ml-1.5 rtl:mr-1.5"
                                                                    @click="removeAppliedColumnValue(column.index, appliedColumnValue)"
                                                                >
                                                                </span>
                                                            </p>
                                                        </template>
                                                    </div>
                                                </template>

                                                <!-- Basic (If Needed) -->
                                                <template v-else></template>
                                            </div>

                                            <!-- Date -->
                                            <div v-else-if="column.type === 'date'">
                                                <!-- Range -->
                                                <template v-if="column.filterable_type === 'date_range'">
                                                    <div class="flex items-center justify-between">
                                                        <p
                                                            class="text-xs font-medium text-gray-800 dark:text-white"
                                                            v-text="column.label"
                                                        >
                                                        </p>

                                                        <div
                                                            class="flex items-center gap-x-1.5"
                                                            @click="removeAppliedColumnAllValues(column.index)"
                                                        >
                                                            <p
                                                                class="cursor-pointer text-xs font-medium leading-6 text-brandColor"
                                                                v-if="hasAnyAppliedColumnValues(column.index)"
                                                            >
                                                                <?php echo app('translator')->get('admin::app.components.datagrid.filters.custom-filters.clear-all'); ?>
                                                            </p>
                                                        </div>
                                                    </div>

                                                    <div class="mt-1.5 grid grid-cols-2 gap-1.5">
                                                        <p
                                                            class="cursor-pointer rounded-md border px-3 py-2 text-center text-sm font-medium leading-6 text-gray-600 transition-all hover:border-gray-400 dark:border-gray-800 dark:text-gray-300 dark:hover:border-gray-400"
                                                            v-for="option in column.filterable_options"
                                                            v-text="option.label"
                                                            @click="addFilter(
                                                                $event,
                                                                column,
                                                                { quickFilter: { isActive: true, selectedFilter: option } }
                                                            )"
                                                        >
                                                        </p>

                                                        <?php if (isset($component)) { $__componentOriginalfb6be9e824dd35fb24e37e299d255b9b = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalfb6be9e824dd35fb24e37e299d255b9b = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'admin::components.flat-picker.date','data' => [':allowInput' => 'false']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('admin::flat-picker.date'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(Illuminate\View\AnonymousComponent::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes([':allow-input' => 'false']); ?>
                                                            <input
                                                                type="date"
                                                                :name="`${column.index}[from]`"
                                                                value=""
                                                                class="flex min-h-[39px] w-full rounded-md border px-3 py-2 text-sm text-gray-600 transition-all hover:border-gray-400 dark:border-gray-800 dark:bg-gray-900 dark:text-gray-300 dark:hover:border-gray-400 ltr:pr-8 rtl:pl-8"
                                                                :placeholder="column.label"
                                                                :ref="`${column.index}[from]`"
                                                                @change="addFilter(
                                                                    $event,
                                                                    column,
                                                                    { range: { name: 'from' }, quickFilter: { isActive: false } }
                                                                )"
                                                            />
                                                         <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalfb6be9e824dd35fb24e37e299d255b9b)): ?>
<?php $attributes = $__attributesOriginalfb6be9e824dd35fb24e37e299d255b9b; ?>
<?php unset($__attributesOriginalfb6be9e824dd35fb24e37e299d255b9b); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalfb6be9e824dd35fb24e37e299d255b9b)): ?>
<?php $component = $__componentOriginalfb6be9e824dd35fb24e37e299d255b9b; ?>
<?php unset($__componentOriginalfb6be9e824dd35fb24e37e299d255b9b); ?>
<?php endif; ?>

                                                        <?php if (isset($component)) { $__componentOriginalfb6be9e824dd35fb24e37e299d255b9b = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalfb6be9e824dd35fb24e37e299d255b9b = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'admin::components.flat-picker.date','data' => [':allowInput' => 'false']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('admin::flat-picker.date'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(Illuminate\View\AnonymousComponent::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes([':allow-input' => 'false']); ?>
                                                            <input
                                                                type="date"
                                                                :name="`${column.index}[to]`"
                                                                value=""
                                                                class="flex min-h-[39px] w-full rounded-md border px-3 py-2 text-sm text-gray-600 transition-all hover:border-gray-400 dark:border-gray-800 dark:bg-gray-900 dark:text-gray-300 dark:hover:border-gray-400 ltr:pr-8 rtl:pl-8"
                                                                :placeholder="column.label"
                                                                :ref="`${column.index}[from]`"
                                                                @change="addFilter(
                                                                    $event,
                                                                    column,
                                                                    { range: { name: 'to' }, quickFilter: { isActive: false } }
                                                                )"
                                                            />
                                                         <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalfb6be9e824dd35fb24e37e299d255b9b)): ?>
<?php $attributes = $__attributesOriginalfb6be9e824dd35fb24e37e299d255b9b; ?>
<?php unset($__attributesOriginalfb6be9e824dd35fb24e37e299d255b9b); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalfb6be9e824dd35fb24e37e299d255b9b)): ?>
<?php $component = $__componentOriginalfb6be9e824dd35fb24e37e299d255b9b; ?>
<?php unset($__componentOriginalfb6be9e824dd35fb24e37e299d255b9b); ?>
<?php endif; ?>

                                                        <div class="mb-4 flex flex-wrap gap-2">
                                                            <p
                                                                class="flex items-center rounded bg-gray-600 px-2 py-1 font-semibold text-white"
                                                                v-if="findAppliedColumn(column.index)"
                                                            >
                                                                <span>
                                                                    {{ getFormattedDates(findAppliedColumn(column.index)) }}
                                                                </span>

                                                                <span
                                                                    class="icon-cross-large cursor-pointer text-lg text-white ltr:ml-1.5 rtl:mr-1.5"
                                                                    @click="removeAppliedColumnValue(column.index)"
                                                                >
                                                                </span>
                                                            </p>
                                                        </div>
                                                    </div>
                                                </template>

                                                <!-- Basic -->
                                                <template v-else>
                                                    <div class="flex items-center justify-between">
                                                        <p
                                                            class="text-xs font-medium text-gray-800 dark:text-white"
                                                            v-text="column.label"
                                                        >
                                                        </p>

                                                        <div
                                                            class="flex items-center gap-x-1.5"
                                                            @click="removeAppliedColumnAllValues(column.index)"
                                                        >
                                                            <p
                                                                class="cursor-pointer text-xs font-medium leading-6 text-brandColor"
                                                                v-if="hasAnyAppliedColumnValues(column.index)"
                                                            >
                                                                <?php echo app('translator')->get('admin::app.components.datagrid.filters.custom-filters.clear-all'); ?>
                                                            </p>
                                                        </div>
                                                    </div>

                                                    <div class="mt-1.5 grid gap-1.5">
                                                        <?php if (isset($component)) { $__componentOriginalfb6be9e824dd35fb24e37e299d255b9b = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalfb6be9e824dd35fb24e37e299d255b9b = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'admin::components.flat-picker.date','data' => [':allowInput' => 'false']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('admin::flat-picker.date'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(Illuminate\View\AnonymousComponent::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes([':allow-input' => 'false']); ?>
                                                            <input
                                                                type="date"
                                                                :name="column.index"
                                                                value=""
                                                                class="flex min-h-[39px] w-full rounded-md border px-3 py-2 text-sm text-gray-600 transition-all hover:border-gray-400 dark:border-gray-800 dark:bg-gray-900 dark:text-gray-300 dark:hover:border-gray-400"
                                                                :placeholder="column.label"
                                                                :ref="column.index"
                                                                @change="addFilter($event, column)"
                                                            />
                                                         <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalfb6be9e824dd35fb24e37e299d255b9b)): ?>
<?php $attributes = $__attributesOriginalfb6be9e824dd35fb24e37e299d255b9b; ?>
<?php unset($__attributesOriginalfb6be9e824dd35fb24e37e299d255b9b); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalfb6be9e824dd35fb24e37e299d255b9b)): ?>
<?php $component = $__componentOriginalfb6be9e824dd35fb24e37e299d255b9b; ?>
<?php unset($__componentOriginalfb6be9e824dd35fb24e37e299d255b9b); ?>
<?php endif; ?>

                                                        <div class="mb-4 flex flex-wrap gap-2">
                                                            <p
                                                                class="flex items-center rounded bg-gray-600 px-2 py-1 font-semibold text-white"
                                                                v-if="findAppliedColumn(column.index)"
                                                            >
                                                                <span>
                                                                    {{ getFormattedDates(findAppliedColumn(column.index)) }}
                                                                </span>

                                                                <span
                                                                    class="icon-cross-large cursor-pointer text-lg text-white ltr:ml-1.5 rtl:mr-1.5"
                                                                    @click="removeAppliedColumnValue(column.index)"
                                                                >
                                                                </span>
                                                            </p>
                                                        </div>
                                                    </div>
                                                </template>
                                            </div>

                                            <!-- Date Time -->
                                            <div v-else-if="column.type === 'datetime'">
                                                <!-- Range -->
                                                <template v-if="column.filterable_type === 'datetime_range'">
                                                    <div class="flex items-center justify-between">
                                                        <p
                                                            class="text-xs font-medium text-gray-800 dark:text-white"
                                                            v-text="column.label"
                                                        >
                                                        </p>

                                                        <div
                                                            class="flex items-center gap-x-1.5"
                                                            @click="removeAppliedColumnAllValues(column.index)"
                                                        >
                                                            <p
                                                                class="cursor-pointer text-xs font-medium leading-6 text-brandColor"
                                                                v-if="hasAnyAppliedColumnValues(column.index)"
                                                            >
                                                                <?php echo app('translator')->get('admin::app.components.datagrid.filters.custom-filters.clear-all'); ?>
                                                            </p>
                                                        </div>
                                                    </div>

                                                    <div class="my-4 grid grid-cols-2 gap-1.5">
                                                        <p
                                                            class="cursor-pointer rounded-md border px-3 py-2 text-center text-sm font-medium leading-6 text-gray-600 transition-all hover:border-gray-400 dark:border-gray-800 dark:text-gray-300 dark:hover:border-gray-400"
                                                            v-for="option in column.filterable_options"
                                                            v-text="option.label"
                                                            @click="addFilter(
                                                                $event,
                                                                column,
                                                                { quickFilter: { isActive: true, selectedFilter: option } }
                                                            )"
                                                        >
                                                        </p>

                                                        <?php if (isset($component)) { $__componentOriginal2199caa745350ee1c77f1b71e4499f91 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal2199caa745350ee1c77f1b71e4499f91 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'admin::components.flat-picker.datetime','data' => [':allowInput' => 'false']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('admin::flat-picker.datetime'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(Illuminate\View\AnonymousComponent::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes([':allow-input' => 'false']); ?>
                                                            <input
                                                                type="datetime-local"
                                                                :name="`${column.index}[from]`"
                                                                value=""
                                                                class="flex min-h-[39px] w-full rounded-md border px-3 py-2 text-sm text-gray-600 transition-all hover:border-gray-400 dark:border-gray-800 dark:bg-gray-900 dark:text-gray-300 dark:hover:border-gray-400"
                                                                :placeholder="column.label"
                                                                :ref="`${column.index}[from]`"
                                                                @change="addFilter(
                                                                    $event,
                                                                    column,
                                                                    { range: { name: 'from' }, quickFilter: { isActive: false } }
                                                                )"
                                                            />
                                                         <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal2199caa745350ee1c77f1b71e4499f91)): ?>
<?php $attributes = $__attributesOriginal2199caa745350ee1c77f1b71e4499f91; ?>
<?php unset($__attributesOriginal2199caa745350ee1c77f1b71e4499f91); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal2199caa745350ee1c77f1b71e4499f91)): ?>
<?php $component = $__componentOriginal2199caa745350ee1c77f1b71e4499f91; ?>
<?php unset($__componentOriginal2199caa745350ee1c77f1b71e4499f91); ?>
<?php endif; ?>

                                                        <?php if (isset($component)) { $__componentOriginal2199caa745350ee1c77f1b71e4499f91 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal2199caa745350ee1c77f1b71e4499f91 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'admin::components.flat-picker.datetime','data' => [':allowInput' => 'false']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('admin::flat-picker.datetime'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(Illuminate\View\AnonymousComponent::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes([':allow-input' => 'false']); ?>
                                                            <input
                                                                type="datetime-local"
                                                                :name="`${column.index}[to]`"
                                                                value=""
                                                                class="flex min-h-[39px] w-full rounded-md border px-3 py-2 text-sm text-gray-600 transition-all hover:border-gray-400 dark:border-gray-800 dark:bg-gray-900 dark:text-gray-300 dark:hover:border-gray-400"
                                                                :placeholder="column.label"
                                                                :ref="`${column.index}[from]`"
                                                                @change="addFilter(
                                                                    $event,
                                                                    column,
                                                                    { range: { name: 'to' }, quickFilter: { isActive: false } }
                                                                )"
                                                            />
                                                         <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal2199caa745350ee1c77f1b71e4499f91)): ?>
<?php $attributes = $__attributesOriginal2199caa745350ee1c77f1b71e4499f91; ?>
<?php unset($__attributesOriginal2199caa745350ee1c77f1b71e4499f91); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal2199caa745350ee1c77f1b71e4499f91)): ?>
<?php $component = $__componentOriginal2199caa745350ee1c77f1b71e4499f91; ?>
<?php unset($__componentOriginal2199caa745350ee1c77f1b71e4499f91); ?>
<?php endif; ?>

                                                        <div class="mb-4 flex flex-wrap gap-2">
                                                            <p
                                                                class="flex items-center rounded bg-gray-600 px-2 py-1 font-semibold text-white"
                                                                v-if="findAppliedColumn(column.index)"
                                                            >
                                                                <span>
                                                                    {{ getFormattedDates(findAppliedColumn(column.index)) }}
                                                                </span>

                                                                <span
                                                                    class="icon-cross-large cursor-pointer text-lg text-white ltr:ml-1.5 rtl:mr-1.5"
                                                                    @click="removeAppliedColumnValue(column.index)"
                                                                >
                                                                </span>
                                                            </p>
                                                        </div>
                                                    </div>
                                                </template>

                                                <!-- Basic -->
                                                <template v-else>
                                                    <div class="flex items-center justify-between">
                                                        <p
                                                            class="text-xs font-medium text-gray-800 dark:text-white"
                                                            v-text="column.label"
                                                        >
                                                        </p>

                                                        <div
                                                            class="flex items-center gap-x-1.5"
                                                            @click="removeAppliedColumnAllValues(column.index)"
                                                        >
                                                            <p
                                                                class="cursor-pointer text-xs font-medium leading-6 text-brandColor"
                                                                v-if="hasAnyAppliedColumnValues(column.index)"
                                                            >
                                                                <?php echo app('translator')->get('admin::app.components.datagrid.filters.custom-filters.clear-all'); ?>
                                                            </p>
                                                        </div>
                                                    </div>

                                                    <div class="my-4 grid">
                                                        <?php if (isset($component)) { $__componentOriginal2199caa745350ee1c77f1b71e4499f91 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal2199caa745350ee1c77f1b71e4499f91 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'admin::components.flat-picker.datetime','data' => [':allowInput' => 'false']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('admin::flat-picker.datetime'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(Illuminate\View\AnonymousComponent::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes([':allow-input' => 'false']); ?>
                                                            <input
                                                                type="datetime-local"
                                                                :name="column.index"
                                                                value=""
                                                                class="flex min-h-[39px] w-full rounded-md border px-3 py-2 text-sm text-gray-600 transition-all hover:border-gray-400 dark:border-gray-800 dark:bg-gray-900 dark:text-gray-300 dark:hover:border-gray-400"
                                                                :placeholder="column.label"
                                                                :ref="column.index"
                                                                @change="addFilter($event, column)"
                                                            />
                                                         <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal2199caa745350ee1c77f1b71e4499f91)): ?>
<?php $attributes = $__attributesOriginal2199caa745350ee1c77f1b71e4499f91; ?>
<?php unset($__attributesOriginal2199caa745350ee1c77f1b71e4499f91); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal2199caa745350ee1c77f1b71e4499f91)): ?>
<?php $component = $__componentOriginal2199caa745350ee1c77f1b71e4499f91; ?>
<?php unset($__componentOriginal2199caa745350ee1c77f1b71e4499f91); ?>
<?php endif; ?>

                                                        <div class="mb-4 flex flex-wrap gap-2">
                                                            <p
                                                                class="flex items-center rounded bg-gray-600 px-2 py-1 font-semibold text-white"
                                                                v-if="findAppliedColumn(column.index)"
                                                            >
                                                                <span>
                                                                    {{ getFormattedDates(findAppliedColumn(column.index)) }}
                                                                </span>

                                                                <span
                                                                    class="icon-cross-large cursor-pointer text-lg text-white ltr:ml-1.5 rtl:mr-1.5"
                                                                    @click="removeAppliedColumnValue(column.index)"
                                                                >
                                                                </span>
                                                            </p>
                                                        </div>
                                                    </div>
                                                </template>
                                            </div>

                                            <!-- Rest -->
                                            <div v-else>
                                                <!-- Dropdown -->
                                                <template v-if="column.filterable_type === 'dropdown'">
                                                    <div class="flex items-center justify-between">
                                                        <p
                                                            class="text-xs font-medium text-gray-800 dark:text-white"
                                                            v-text="column.label"
                                                        >
                                                        </p>

                                                        <div
                                                            class="flex items-center gap-x-1.5"
                                                            @click="removeAppliedColumnAllValues(column.index)"
                                                        >
                                                            <p
                                                                class="cursor-pointer text-xs font-medium leading-6 text-brandColor"
                                                                v-if="hasAnyAppliedColumnValues(column.index)"
                                                            >
                                                                <?php echo app('translator')->get('admin::app.components.datagrid.filters.custom-filters.clear-all'); ?>
                                                            </p>
                                                        </div>
                                                    </div>

                                                    <div class="mb-2 mt-1.5">
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
                                                                <button
                                                                    type="button"
                                                                    class="inline-flex w-full cursor-pointer appearance-none items-center justify-between gap-x-2 rounded-md border bg-white px-2.5 py-1.5 text-center leading-6 text-gray-600 transition-all marker:shadow hover:border-gray-400 focus:border-gray-400 dark:border-gray-800 dark:bg-gray-900 dark:text-gray-300 dark:hover:border-gray-400 dark:focus:border-gray-400"
                                                                >
                                                                    <!-- If Allow Multiple Values -->
                                                                    <span
                                                                        class="text-sm text-gray-400 dark:text-gray-400"
                                                                        v-text="'<?php echo app('translator')->get('admin::app.components.datagrid.filters.select'); ?>'"
                                                                        v-if="column.allow_multiple_values"
                                                                    >
                                                                    </span>

                                                                    <!-- If Allow Single Value -->
                                                                    <span
                                                                        class="text-sm text-gray-400 dark:text-gray-400"
                                                                        v-text="column.filterable_options.find((option => option.value === getAppliedColumnValues(column.index)))?.label ?? '<?php echo app('translator')->get('admin::app.components.datagrid.filters.select'); ?>'"
                                                                        v-else
                                                                    >
                                                                    </span>

                                                                    <span class="icon-down-arrow text-2xl"></span>
                                                                </button>
                                                             <?php $__env->endSlot(); ?>

                                                             <?php $__env->slot('menu', null, []); ?> 
                                                                <?php if (isset($component)) { $__componentOriginal0223c8534d6a243be608c3a65289c4d0 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal0223c8534d6a243be608c3a65289c4d0 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'admin::components.dropdown.menu.item','data' => ['vFor' => 'option in column.filterable_options','vText' => 'option.label','@click' => 'addFilter(option.value, column)']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('admin::dropdown.menu.item'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(Illuminate\View\AnonymousComponent::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes(['v-for' => 'option in column.filterable_options','v-text' => 'option.label','@click' => 'addFilter(option.value, column)']); ?>
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
                                                    </div>

                                                    <div class="mb-4 flex flex-wrap gap-2">
                                                        <!-- If Allow Multiple Values -->
                                                        <template v-if="column.allow_multiple_values">
                                                            <p
                                                                class="flex items-center rounded bg-gray-600 px-2 py-1 font-semibold text-white"
                                                                v-for="appliedColumnValue in getAppliedColumnValues(column.index)"
                                                            >
                                                                <!-- Retrieving the label from the options based on the applied column value. -->
                                                                <span v-text="column.filterable_options.find((option => option.value == appliedColumnValue)).label"></span>

                                                                <span
                                                                    class="icon-cross-large cursor-pointer text-lg text-white ltr:ml-1.5 rtl:mr-1.5"
                                                                    @click="removeAppliedColumnValue(column.index, appliedColumnValue)"
                                                                >
                                                                </span>
                                                            </p>
                                                        </template>
                                                    </div>
                                                </template>

                                                <template v-else-if="column.filterable_type === 'searchable_dropdown'">
                                                    <div class="flex items-center justify-between">
                                                        <p
                                                            class="text-xs font-medium text-gray-800 dark:text-white"
                                                            v-text="column.label"
                                                        >
                                                        </p>
                                    
                                                        <div
                                                            class="flex items-center gap-x-1.5"
                                                            @click="removeAppliedColumnAllValues(column.index)"
                                                        >
                                                            <p
                                                                class="cursor-pointer text-xs font-medium leading-6 text-blue-600"
                                                                v-if="hasAnyAppliedColumnValues(column.index)"
                                                            >
                                                                <?php echo app('translator')->get('admin::app.components.datagrid.filters.custom-filters.clear-all'); ?>
                                                            </p>
                                                        </div>
                                                    </div>
                                    
                                                    <div class="mb-2 mt-1.5">
                                                        <v-datagrid-searchable-dropdown
                                                            :datagrid-id="available.id"
                                                            :column="column"
                                                            @select-option="addFilter($event.target.value, column)"
                                                        >
                                                        </v-datagrid-searchable-dropdown>
                                                    </div>
                                    
                                                    <div class="mb-4 flex flex-wrap gap-2">
                                                        <p
                                                            class="flex items-center rounded bg-gray-600 px-2 py-1 font-semibold text-white"
                                                            v-for="appliedColumnValue in getAppliedColumnValues(column.index)"
                                                        >
                                                            <span v-text="appliedColumnValue"></span>
                                    
                                                            <span
                                                                class="icon-cross-large cursor-pointer text-lg text-white ltr:ml-1.5 rtl:mr-1.5"
                                                                @click="removeAppliedColumnValue(column.index, appliedColumnValue)"
                                                            >
                                                            </span>
                                                        </p>
                                                    </div>
                                                </template>

                                                <!-- Basic -->
                                                <template v-else>
                                                    <div class="flex items-center justify-between">
                                                        <p
                                                            class="text-xs font-medium text-gray-800 dark:text-white"
                                                            v-text="column.label"
                                                        >
                                                        </p>

                                                        <div
                                                            class="flex items-center gap-x-1.5"
                                                            @click="removeAppliedColumnAllValues(column.index)"
                                                        >
                                                            <p
                                                                class="cursor-pointer text-xs font-medium leading-6 text-brandColor"
                                                                v-if="hasAnyAppliedColumnValues(column.index)"
                                                            >
                                                                <?php echo app('translator')->get('admin::app.components.datagrid.filters.custom-filters.clear-all'); ?>
                                                            </p>
                                                        </div>
                                                    </div>

                                                    <div class="mb-2 mt-1.5 grid gap-1.5">
                                                        <input
                                                            type="text"
                                                            class="block w-full rounded-md border bg-white px-2 py-1.5 text-sm leading-6 text-gray-600 transition-all hover:border-gray-400 focus:border-gray-400 dark:border-gray-800 dark:bg-gray-900 dark:text-gray-300 dark:hover:border-gray-400 dark:focus:border-gray-400"
                                                            :name="column.index"
                                                            :placeholder="column.label"
                                                            @change="addFilter($event, column)"
                                                        />
                                                    </div>

                                                    <div class="mb-4 flex flex-wrap gap-2">
                                                        <!-- If Allow Multiple Values -->
                                                        <template v-if="column.allow_multiple_values">
                                                            <p
                                                                class="flex items-center rounded bg-gray-600 px-2 py-1 font-semibold text-white"
                                                                v-for="appliedColumnValue in getAppliedColumnValues(column.index)"
                                                            >
                                                                <span v-text="appliedColumnValue"></span>

                                                                <span
                                                                    class="icon-cross-large cursor-pointer text-lg text-white ltr:ml-1.5 rtl:mr-1.5"
                                                                    @click="removeAppliedColumnValue(column.index, appliedColumnValue)"
                                                                >
                                                                </span>
                                                            </p>
                                                        </template>

                                                        <!-- If Allow Single Value -->
                                                        <template v-else>
                                                            <p
                                                                class="flex items-center rounded bg-gray-600 px-2 py-1 font-semibold text-white"
                                                                v-if="getAppliedColumnValues(column.index) !== ''"
                                                            >
                                                                <span v-text="getAppliedColumnValues(column.index)"></span>

                                                                <span
                                                                    class="icon-cross-large cursor-pointer text-lg text-white ltr:ml-1.5 rtl:mr-1.5"
                                                                    @click="removeAppliedColumnValue(column.index, getAppliedColumnValues(column.index))"
                                                                >
                                                                </span>
                                                            </p>
                                                        </template>
                                                    </div>
                                                </template>
                                            </div>
                                        </div>
                                    </div>
                                 <?php $__env->endSlot(); ?>
                             <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginale6717d929d3edd1e7d9927d6c11ccc02)): ?>
<?php $attributes = $__attributesOriginale6717d929d3edd1e7d9927d6c11ccc02; ?>
<?php unset($__attributesOriginale6717d929d3edd1e7d9927d6c11ccc02); ?>
<?php endif; ?>
<?php if (isset($__componentOriginale6717d929d3edd1e7d9927d6c11ccc02)): ?>
<?php $component = $__componentOriginale6717d929d3edd1e7d9927d6c11ccc02; ?>
<?php unset($__componentOriginale6717d929d3edd1e7d9927d6c11ccc02); ?>
<?php endif; ?>
                        </template>

                        <!-- Save Filter Section -->
                        <template v-else>
                            <div class="flex items-center justify-between px-4 py-4">
                                <p class="text-base font-semibold text-gray-800 dark:text-white">
                                    {{ applied.savedFilterId ? '<?php echo app('translator')->get('admin::app.components.datagrid.toolbar.filter.update-filter'); ?>' : '<?php echo app('translator')->get('admin::app.components.datagrid.toolbar.filter.create-new-filter'); ?>' }}
                                </p>
                            </div>

                            <div v-if="hasAnyColumn">
                                <!-- Save Filter Form -->
                                <?php if (isset($component)) { $__componentOriginal81b4d293d9113446bb908fc8aef5c8f6 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal81b4d293d9113446bb908fc8aef5c8f6 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'admin::components.form.index','data' => ['vSlot' => '{ meta, errors, handleSubmit }','as' => 'div']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('admin::form'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(Illuminate\View\AnonymousComponent::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes(['v-slot' => '{ meta, errors, handleSubmit }','as' => 'div']); ?>
                                    <form @submit="handleSubmit($event, createOrUpdateFilter)">
                                        <div class="flex flex-col gap-4">
                                            <!-- Save Filter Name Input Field -->
                                            <div class="flex flex-col gap-2 border-b px-4 dark:border-gray-800">
                                                <?php if (isset($component)) { $__componentOriginal7b1bc76a00ab5e7f1bf2c6429dae85a3 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal7b1bc76a00ab5e7f1bf2c6429dae85a3 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'admin::components.form.control-group.index','data' => []] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('admin::form.control-group'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(Illuminate\View\AnonymousComponent::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
                                                    <?php if (isset($component)) { $__componentOriginal8378211f70f8c39b16d47cecdac9c7c8 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal8378211f70f8c39b16d47cecdac9c7c8 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'admin::components.form.control-group.label','data' => ['class' => 'required']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('admin::form.control-group.label'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(Illuminate\View\AnonymousComponent::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'required']); ?>
                                                        <?php echo app('translator')->get('admin::app.components.datagrid.toolbar.filter.name'); ?>
                                                     <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal8378211f70f8c39b16d47cecdac9c7c8)): ?>
<?php $attributes = $__attributesOriginal8378211f70f8c39b16d47cecdac9c7c8; ?>
<?php unset($__attributesOriginal8378211f70f8c39b16d47cecdac9c7c8); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal8378211f70f8c39b16d47cecdac9c7c8)): ?>
<?php $component = $__componentOriginal8378211f70f8c39b16d47cecdac9c7c8; ?>
<?php unset($__componentOriginal8378211f70f8c39b16d47cecdac9c7c8); ?>
<?php endif; ?>

                                                    <?php if (isset($component)) { $__componentOriginal53af403f6b2179a3039d488b8ab2a267 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal53af403f6b2179a3039d488b8ab2a267 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'admin::components.form.control-group.control','data' => ['type' => 'hidden','name' => 'id',':value' => 'applied.savedFilterId']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('admin::form.control-group.control'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(Illuminate\View\AnonymousComponent::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes(['type' => 'hidden','name' => 'id',':value' => 'applied.savedFilterId']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal53af403f6b2179a3039d488b8ab2a267)): ?>
<?php $attributes = $__attributesOriginal53af403f6b2179a3039d488b8ab2a267; ?>
<?php unset($__attributesOriginal53af403f6b2179a3039d488b8ab2a267); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal53af403f6b2179a3039d488b8ab2a267)): ?>
<?php $component = $__componentOriginal53af403f6b2179a3039d488b8ab2a267; ?>
<?php unset($__componentOriginal53af403f6b2179a3039d488b8ab2a267); ?>
<?php endif; ?>

                                                    <?php if (isset($component)) { $__componentOriginal53af403f6b2179a3039d488b8ab2a267 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal53af403f6b2179a3039d488b8ab2a267 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'admin::components.form.control-group.control','data' => ['type' => 'text','name' => 'name','id' => 'name',':value' => 'getAppliedSavedFilter?.name','rules' => 'required','label' => trans('admin::app.components.datagrid.toolbar.filter.name'),'placeholder' => trans('admin::app.components.datagrid.toolbar.filter.name')]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('admin::form.control-group.control'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(Illuminate\View\AnonymousComponent::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes(['type' => 'text','name' => 'name','id' => 'name',':value' => 'getAppliedSavedFilter?.name','rules' => 'required','label' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(trans('admin::app.components.datagrid.toolbar.filter.name')),'placeholder' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(trans('admin::app.components.datagrid.toolbar.filter.name'))]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal53af403f6b2179a3039d488b8ab2a267)): ?>
<?php $attributes = $__attributesOriginal53af403f6b2179a3039d488b8ab2a267; ?>
<?php unset($__attributesOriginal53af403f6b2179a3039d488b8ab2a267); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal53af403f6b2179a3039d488b8ab2a267)): ?>
<?php $component = $__componentOriginal53af403f6b2179a3039d488b8ab2a267; ?>
<?php unset($__componentOriginal53af403f6b2179a3039d488b8ab2a267); ?>
<?php endif; ?>

                                                    <?php if (isset($component)) { $__componentOriginal8da25fb6534e2ef288914e35c32417f8 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal8da25fb6534e2ef288914e35c32417f8 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'admin::components.form.control-group.error','data' => ['controlName' => 'name']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('admin::form.control-group.error'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(Illuminate\View\AnonymousComponent::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes(['control-name' => 'name']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal8da25fb6534e2ef288914e35c32417f8)): ?>
<?php $attributes = $__attributesOriginal8da25fb6534e2ef288914e35c32417f8; ?>
<?php unset($__attributesOriginal8da25fb6534e2ef288914e35c32417f8); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal8da25fb6534e2ef288914e35c32417f8)): ?>
<?php $component = $__componentOriginal8da25fb6534e2ef288914e35c32417f8; ?>
<?php unset($__componentOriginal8da25fb6534e2ef288914e35c32417f8); ?>
<?php endif; ?>

                                                    <button
                                                        type="submit"
                                                        class="hidden"
                                                        :disabled="savedFilters.params.filters.columns.every(column => column.value.length === 0)"
                                                        id="save-filter"
                                                    ></button>
                                                 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal7b1bc76a00ab5e7f1bf2c6429dae85a3)): ?>
<?php $attributes = $__attributesOriginal7b1bc76a00ab5e7f1bf2c6429dae85a3; ?>
<?php unset($__attributesOriginal7b1bc76a00ab5e7f1bf2c6429dae85a3); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal7b1bc76a00ab5e7f1bf2c6429dae85a3)): ?>
<?php $component = $__componentOriginal7b1bc76a00ab5e7f1bf2c6429dae85a3; ?>
<?php unset($__componentOriginal7b1bc76a00ab5e7f1bf2c6429dae85a3); ?>
<?php endif; ?>
                                            </div>

                                            <div class="flex flex-col gap-4 px-4">
                                                <p class="text-base font-semibold text-gray-800 dark:text-white">
                                                    <?php echo app('translator')->get('admin::app.components.datagrid.toolbar.filter.selected-filters'); ?>
                                                </p>

                                                <div v-if="! savedFilters.params.filters.columns.every(column => column.value.length === 0)">
                                                    <!-- Applied filters label and value listing for saving custom filter. -->
                                                    <div v-for="column in savedFilters.params.filters.columns">
                                                        <div
                                                            class="flex flex-col gap-2"
                                                            v-if="hasAnyValue(column)"
                                                        >
                                                            <p class="text-xs font-medium text-gray-800 dark:text-white">
                                                                {{ column.label }}
                                                            </p>

                                                            <div class="mb-4 flex flex-wrap gap-2">
                                                                <!-- Date & Date Time Case -->
                                                                <template v-if="column.type === 'date' || column.type === 'datetime'">
                                                                    <p class="flex items-center rounded bg-gray-600 px-2 py-1 font-semibold text-white">
                                                                        <span>
                                                                            {{ getFormattedDates(column) }}
                                                                        </span>

                                                                        <span
                                                                            class="icon-cross-large cursor-pointer text-lg text-white ltr:ml-1.5 rtl:mr-1.5"
                                                                            @click="removeSavedFilterColumnValue(column, appliedColumnValue)"
                                                                        >
                                                                        </span>
                                                                    </p>
                                                                </template>

                                                                <!-- Rest Case -->
                                                                <template v-else>
                                                                    <!-- If Allow Multiple Values -->
                                                                    <template v-if="column.allow_multiple_values">
                                                                        <p
                                                                            v-for="appliedColumnValue in column.value"
                                                                            class="flex items-center rounded bg-gray-600 px-2 py-1 font-semibold text-white"
                                                                        >
                                                                            <span>
                                                                                {{ appliedColumnValue }}
                                                                            </span>

                                                                            <span
                                                                                class="icon-cross-large cursor-pointer text-lg text-white ltr:ml-1.5 rtl:mr-1.5"
                                                                                @click="removeSavedFilterColumnValue(column, appliedColumnValue)"
                                                                            >
                                                                            </span>
                                                                        </p>
                                                                    </template>

                                                                    <!-- If Allow Single Value -->
                                                                    <template v-else>
                                                                        <p class="flex items-center rounded bg-gray-600 px-2 py-1 font-semibold text-white">
                                                                            <span>
                                                                                {{ column.value }}
                                                                            </span>

                                                                            <span
                                                                                class="icon-cross-large cursor-pointer text-lg text-white ltr:ml-1.5 rtl:mr-1.5"
                                                                                @click="removeSavedFilterColumnValue(column, column.value)"
                                                                            >
                                                                            </span>
                                                                        </p>
                                                                    </template>
                                                                </template>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                                <!-- Save Filter Empty Value Placeholder -->
                                                <div v-else>
                                                    <div class="mb-4 flex content-end items-center justify-end">
                                                        <div class="grid">
                                                            <div class="flex items-center gap-5 py-2.5">
                                                                <img
                                                                    src="<?php echo e(vite()->asset('images/empty-placeholders/products.svg')); ?>"
                                                                    class="h-20 w-20 dark:border-gray-800 dark:mix-blend-exclusion dark:invert"
                                                                >

                                                                <div class="flex flex-col gap-1.5">
                                                                    <p class="text-base font-semibold text-gray-400">
                                                                        <?php echo app('translator')->get('admin::app.components.datagrid.toolbar.filter.empty-title'); ?>
                                                                    </p>

                                                                    <p class="text-gray-400">
                                                                        <?php echo app('translator')->get('admin::app.components.datagrid.toolbar.filter.empty-description'); ?>
                                                                    </p>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </form>
                                 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal81b4d293d9113446bb908fc8aef5c8f6)): ?>
<?php $attributes = $__attributesOriginal81b4d293d9113446bb908fc8aef5c8f6; ?>
<?php unset($__attributesOriginal81b4d293d9113446bb908fc8aef5c8f6); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal81b4d293d9113446bb908fc8aef5c8f6)): ?>
<?php $component = $__componentOriginal81b4d293d9113446bb908fc8aef5c8f6; ?>
<?php unset($__componentOriginal81b4d293d9113446bb908fc8aef5c8f6); ?>
<?php endif; ?>
                            </div>
                        </template>
                     <?php $__env->endSlot(); ?>

                     <?php $__env->slot('footer', null, ['class' => '!pb-3']); ?> 
                        <!-- Buttons Panel -->
                        <div
                            class="flex justify-end gap-2 px-2 pt-3"
                            v-if="! isShowSavedFilters"
                        >
                            <!-- Save Filter Button -->
                            <button
                                type="button"
                                v-if="hasAnyColumn"
                                @click="isShowSavedFilters = ! isShowSavedFilters"
                                class="text-gray-900 dark:text-white"
                                :disabled="isFilterDirty || ! filters.columns.length > 0"
                            >
                                {{ applied.savedFilterId ? '<?php echo app('translator')->get('admin::app.components.datagrid.toolbar.filter.update-filter'); ?>' : '<?php echo app('translator')->get('admin::app.components.datagrid.toolbar.filter.save-filter'); ?>' }}
                            </button>
                            
                            <!-- Apply Filter Button -->
                            <button
                                type="button"
                                class="primary-button"
                                @click="applyFilters"
                                :disabled="! isFilterDirty"
                            >
                                <?php echo app('translator')->get('admin::app.components.datagrid.toolbar.filter.apply-filters-btn'); ?>
                            </button>
                        </div>

                        <!-- Save Filter Form Submit Button -->
                        <div
                            v-else
                            class="flex justify-end gap-1 px-2 pt-3"
                        >
                            <button
                                type="button"
                                class="primary-button"
                                aria-label="<?php echo app('translator')->get('admin::app.components.datagrid.toolbar.filter.save-btn'); ?>"
                                :disabled="savedFilters.params.filters.columns.every(column => column.value.length === 0)"
                                @click="handleSaveFilterForm"
                            >
                                {{ applied.savedFilterId ? '<?php echo app('translator')->get('admin::app.components.datagrid.toolbar.filter.update-filter'); ?>' : '<?php echo app('translator')->get('admin::app.components.datagrid.toolbar.filter.save-filter'); ?>' }}
                            </button>
                        </div>
                     <?php $__env->endSlot(); ?>
                 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal9bfb526197f1d7304e7fade44c26fbb8)): ?>
<?php $attributes = $__attributesOriginal9bfb526197f1d7304e7fade44c26fbb8; ?>
<?php unset($__attributesOriginal9bfb526197f1d7304e7fade44c26fbb8); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal9bfb526197f1d7304e7fade44c26fbb8)): ?>
<?php $component = $__componentOriginal9bfb526197f1d7304e7fade44c26fbb8; ?>
<?php unset($__componentOriginal9bfb526197f1d7304e7fade44c26fbb8); ?>
<?php endif; ?>
            </template>
        </slot>
    </script>

    <script type="module">
        app.component('v-datagrid-filter', {
            template: '#v-datagrid-filter-template',

            props: ['isLoading', 'available', 'applied', 'src'],

            emits: ['applyFilters', 'applySavedFilter'],

            data() {
                return {
                    savedFilters: {
                        available: [],

                        applied: null,

                        params: {
                            filters: {
                                columns: [],
                            },
                        },
                    },

                    filters: {
                        columns: [],
                    },

                    isShowSavedFilters: false,

                    isFilterDirty: false,
                };
            },

            mounted() {
                this.filters.columns = this.getAppliedColumns();

                this.savedFilters.params.filters.columns = JSON.parse(JSON.stringify(this.filters.columns));

                this.getSavedFilters();
            },

            computed: {
                getAppliedSavedFilter() {
                    return this.savedFilters.available.find((filter) => filter.id == this.applied.savedFilterId);
                },
            },

            methods: {
                /**
                 * Has any column.
                 *
                 * @returns {boolean}
                 */
                hasAnyColumn() {
                    return filters.columns.length;
                },

                /**
                 * Get applied columns.
                 *
                 * @returns {object}
                 */
                getAppliedColumns() {
                    return this.applied.filters.columns.filter((column) => column.index !== 'all');
                },

                /**
                 * Has any applied column.
                 *
                 * @returns {boolean}
                 */
                hasAnyAppliedColumn() {
                    return this.getAppliedColumns().length > 0;
                },

                /**
                 * Go back to filters.
                 *
                 * @returns {void}
                 */
                backToFilters() {
                    this.savedFilters.params.filters.columns = JSON.parse(JSON.stringify(this.filters.columns));

                    this.isShowSavedFilters = ! this.isShowSavedFilters;
                },

                /**
                 * Applies the saved filter.
                 *
                 * @param {Object} filter - The filter to be applied.
                 */
                applySavedFilter(filter) {
                    this.$emit('applySavedFilter', filter);
                },

                /**
                 * Remove all applied filters.
                 *
                 * @returns {void}
                 */
                removeAllAppliedFilters() {
                    this.filters = {
                        columns: [],
                    };

                    this.isFilterDirty = true;
                },

                /**
                 * Remove filter option from save filters screen.
                 *
                 * @returns {void}
                 */
                removeSavedFilterColumnValue(column, value) {
                    if (column.allow_multiple_values) {
                        column.value = column.value.filter((columnValue) => columnValue !== value);
                    } else {
                        column.value = '';
                    }
                },

                handleSaveFilterForm() {
                    const saveFilterForm = document.getElementById('save-filter');

                    if (saveFilterForm) {
                        saveFilterForm.click();
                    }
                },

                /**
                 * Save filters to the database.
                 *
                 * @returns {void}
                 */
                createOrUpdateFilter(params, { setErrors }) {
                    let applied = JSON.parse(JSON.stringify(this.applied));

                    applied.filters.columns = this.savedFilters.params.filters.columns.filter((column) => this.hasAnyValue(column));

                    if (params.id) {
                        params._method = 'PUT';
                    }

                    this.$axios.post(params.id ? `<?php echo e(route('admin.datagrid.saved_filters.update', '')); ?>/${params.id}` : "<?php echo e(route('admin.datagrid.saved_filters.store')); ?>", {
                        src: this.src,
                        applied,
                        ...params,
                    })
                        .then(response => {
                            if (! params.id) {
                                this.savedFilters.available.push(response.data.data);
                            } else {
                                this.savedFilters.available = this.savedFilters.available.map((filter) => {
                                    if (filter.id == response.data.data.id) {
                                        return response.data.data;
                                    }

                                    return filter;
                                });
                            }

                            this.savedFilters.name = '';

                            this.$emitter.emit('add-flash', { type: 'success', message: response.data.message });

                            this.isShowSavedFilters = false;
                        })
                        .catch(error => {
                            if (error.response.status == 422) {
                                setErrors(error.response.data.errors);
                            } else {
                                this.$emitter.emit('add-flash', { type: 'error',  message: response.data.message });
                            }
                        });
                },

                /**
                 * Retrieves the saved filters.
                 *
                 * @returns {void}
                 */
                getSavedFilters() {
                    this.$axios
                        .get('<?php echo e(route('admin.datagrid.saved_filters.index')); ?>', {
                            params: { src: this.src }
                        })
                        .then(response => {
                            this.savedFilters.available = response.data.data ?? [];
                        })
                        .catch(error => {});
                },

                /**
                 * Delete the saved filter.
                 *
                 * @returns {void}
                 */
                deleteSavedFilter(filter) {
                    this.$emitter.emit('open-confirm-modal', {
                        agree: () => {
                            this.$axios.delete(`<?php echo e(route('admin.datagrid.saved_filters.destroy', '')); ?>/${filter.id}`)
                                .then(response => {
                                    this.applySavedFilter(null);

                                    this.savedFilters.available = this.savedFilters.available.filter((savedFilter) => savedFilter.id !== filter.id);

                                    this.$emitter.emit('add-flash', { type: 'success', message: response.data.message });
                                })
                                .catch(error => {
                                    this.$emitter.emit('add-flash', { type: 'error', message: response.data.message });
                                });
                        }
                    });
                },

                /**
                 * Apply all added filters.
                 *
                 * @returns {void}
                 */
                applyFilters() {
                    this.$emit('applyFilters', this.filters);

                    this.$refs.filterDrawer.close();
                },

                /**
                 * Add filter.
                 *
                 * @param {Event} $event
                 * @param {object} column
                 * @param {object} additional
                 * @returns {void}
                 */
                addFilter($event, column = null, additional = {}) {
                    let quickFilter = additional?.quickFilter;

                    if (quickFilter?.isActive) {
                        let options = quickFilter.selectedFilter;

                        switch (column.type) {
                            case 'date':
                            case 'datetime':
                                this.applyColumnValues(column, options.name);

                                break;

                            default:
                                break;
                        }
                    } else {
                        /**
                         * Here, either a real event will come or a string value. If a string value is present, then
                         * we create a similar event-like structure to avoid any breakage and make it easy to use.
                         */
                        if ($event?.target?.value === undefined) {
                            $event = {
                                target: {
                                    value: $event,
                                }
                            };
                        }

                        this.applyColumnValues(column, $event.target.value, additional);

                        if (column) {
                            $event.target.value = '';
                        }
                    }
                },

                /**
                 * Apply column values.
                 *
                 * @param {object} column
                 * @param {string} requestedValue
                 * @param {object} additional
                 * @returns {void}
                 */
                applyColumnValues(column, requestedValue, additional = {}) {
                    let appliedColumn = this.findAppliedColumn(column?.index);

                    if (
                        requestedValue === undefined ||
                        requestedValue === '' ||
                        (appliedColumn?.allow_multiple_values && appliedColumn?.value.includes(requestedValue)) ||
                        (! appliedColumn?.allow_multiple_values && appliedColumn?.value === requestedValue)
                    ) {
                        return;
                    }

                    switch (column.type) {
                        case 'date':
                        case 'datetime':
                            let { range } = additional;

                            if (appliedColumn) {
                                if (range) {
                                    let appliedRanges = ['', ''];

                                    if (typeof appliedColumn.value !== 'string') {
                                        appliedRanges = appliedColumn.value[0];
                                    }

                                    if (range.name == 'from') {
                                        appliedRanges[0] = requestedValue;
                                    }

                                    if (range.name == 'to') {
                                        appliedRanges[1] = requestedValue;
                                    }

                                    appliedColumn.value = [appliedRanges];
                                } else {
                                    appliedColumn.value = requestedValue;
                                }
                            } else {
                                if (range) {
                                    let appliedRanges = ['', ''];

                                    if (range.name == 'from') {
                                        appliedRanges[0] = requestedValue;
                                    }

                                    if (range.name == 'to') {
                                        appliedRanges[1] = requestedValue;
                                    }

                                    this.filters.columns.push({
                                        index: column.index,
                                        label: column.label,
                                        type: column.type,
                                        value: [appliedRanges]
                                    });
                                } else {
                                    this.filters.columns.push({
                                        index: column.index,
                                        label: column.label,
                                        type: column.type,
                                        value: requestedValue
                                    });
                                }
                            }

                            break;

                        default:
                            if (appliedColumn) {
                                if (appliedColumn.allow_multiple_values) {
                                    appliedColumn.value.push(requestedValue);
                                } else {
                                    appliedColumn.value = requestedValue;
                                }
                            } else {
                                this.filters.columns.push({
                                    index: column.index,
                                    label: column.label,
                                    type: column.type,
                                    value: column.allow_multiple_values ? [requestedValue] : requestedValue,
                                    allow_multiple_values: column.allow_multiple_values,
                                });
                            }

                            break;
                    }

                    this.isFilterDirty = true;
                },

                /**
                 * Get formatted dates.
                 *
                 * @param {object} appliedColumn
                 * @returns {string}
                 */
                getFormattedDates(appliedColumn)
                {
                    if (! appliedColumn) {
                        return '';
                    }

                    if (typeof appliedColumn.value === 'string') {
                        const availableColumn = this.available.columns.find(column => column.index === appliedColumn.index);

                        if (availableColumn.filterable_type === 'date_range' || availableColumn.filterable_type === 'datetime_range') {
                            const option = availableColumn.filterable_options.find(option => option.name === appliedColumn.value);

                            return option.label;
                        }

                        return appliedColumn.value;
                    }

                    if (! appliedColumn.value.length) {
                        return '';
                    }

                    return appliedColumn.value[0].join(' to ');
                },

                /**
                 * Check if any values are applied for the specified column.
                 *
                 * @param {object} column
                 * @returns {boolean}
                 */
                hasAnyValue(column) {
                    if (column.allow_multiple_values) {
                        return column.value.length > 0;
                    }

                    return column.value !== '';
                },

                /**
                 * Find applied column.
                 *
                 * @param {string} columnIndex
                 * @returns {object}
                 */
                findAppliedColumn(columnIndex) {
                    return this.filters.columns.find(column => column.index === columnIndex);
                },

                /**
                 * Check if any values are applied for the specified column.
                 *
                 * @param {string} columnIndex
                 * @returns {boolean}
                 */
                hasAnyAppliedColumnValues(columnIndex) {
                    let appliedColumn = this.findAppliedColumn(columnIndex);

                    if (! appliedColumn) {
                        return false;
                    }

                    return this.hasAnyValue(appliedColumn);
                },

                /**
                 * Get applied values for the specified column.
                 *
                 * @param {string} columnIndex
                 * @returns {Array}
                 */
                getAppliedColumnValues(columnIndex) {
                    const appliedColumn = this.findAppliedColumn(columnIndex);

                    if (appliedColumn?.allow_multiple_values) {
                        return appliedColumn?.value ?? [];
                    }

                    return appliedColumn?.value ?? '';
                },

                /**
                 * Remove a specific value from the applied values of the specified column.
                 *
                 * @param {string} columnIndex
                 * @param {any} appliedColumnValue
                 * @returns {void}
                 */
                removeAppliedColumnValue(columnIndex, appliedColumnValue) {
                    let appliedColumn = this.findAppliedColumn(columnIndex);

                    if (appliedColumn?.type === 'date' || appliedColumn?.type === 'datetime') {
                        appliedColumn.value = [];
                    } else {
                        if (appliedColumn.allow_multiple_values) {
                            appliedColumn.value = appliedColumn?.value.filter(value => value !== appliedColumnValue);
                        } else {
                            appliedColumn.value = '';
                        }
                    }

                    /**
                     * Clean up is done here. If there are no applied values present, there is no point in including the applied column as well.
                     */
                    if (! appliedColumn.value.length) {
                        this.filters.columns = this.filters.columns.filter(column => column.index !== columnIndex);
                    }

                    this.isFilterDirty = true;
                },

                /**
                 * Remove all values from the applied values of the specified column.
                 *
                 * @param {string} columnIndex
                 * @returns {void}
                 */
                removeAppliedColumnAllValues(columnIndex) {
                    this.filters.columns = this.filters.columns.filter(column => column.index !== columnIndex);

                    this.isFilterDirty = true;
                },
            },
        });
    </script>

    <script 
        type="text/x-template"
        id="v-datagrid-searchable-dropdown-template"
    >
        <?php if (isset($component)) { $__componentOriginalaf937e0ec72fa678d3a0c6dc6c0ac5f2 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalaf937e0ec72fa678d3a0c6dc6c0ac5f2 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'admin::components.dropdown.index','data' => [':closeOnClick' => 'false']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('admin::dropdown'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(Illuminate\View\AnonymousComponent::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes([':close-on-click' => 'false']); ?>
            <!-- Dropdown Toggler -->
             <?php $__env->slot('toggle', null, []); ?> 
                <button
                    type="button"
                    class="inline-flex h-[38px] w-full cursor-pointer appearance-none items-center justify-between gap-x-2 rounded-md border bg-white px-2.5 py-1.5 text-center leading-6 text-gray-600 transition-all marker:shadow hover:border-gray-400 focus:border-gray-400 dark:border-gray-800 dark:bg-gray-900 dark:text-gray-300 dark:hover:border-gray-400 dark:focus:border-gray-400"
                >
                    <span
                        class="text-sm text-gray-400 dark:text-gray-400" 
                        v-text="'<?php echo app('translator')->get('admin::app.components.datagrid.filters.select'); ?>'"
                    >
                    </span>

                    <span class="icon-down-arrow text-2xl"></span>
                </button>
             <?php $__env->endSlot(); ?>

            <!-- Dropdown Content -->
             <?php $__env->slot('menu', null, ['class' => 'mt-1 rounded-md border border-gray-200 dark:border-gray-800']); ?> 
                <div class="relative">
                    <div class="relative rounded">
                        <ul class="list-reset">
                            <li class="p-2">
                                <input
                                    class="block w-full rounded-md border bg-white px-2 py-1.5 text-sm leading-6 text-gray-600 transition-all hover:border-gray-400 focus:border-gray-400 dark:border-gray-800 dark:bg-gray-900 dark:text-gray-300 dark:hover:border-gray-400 dark:focus:border-gray-400"
                                    @keyup="lookUp($event)"
                                >
                            </li>

                            <ul class="p-2">
                                <li v-if="!isMinimumCharacters">
                                    <p
                                        class="block p-2 text-gray-600 dark:text-gray-300"
                                        v-text="'<?php echo app('translator')->get('admin::app.components.datagrid.filters.dropdown.searchable.at-least-two-chars'); ?>'"
                                    >
                                    </p>
                                </li>

                                <li v-else-if="!searchedOptions.length">
                                    <p
                                        class="block p-2 text-gray-600 dark:text-gray-300"
                                        v-text="'<?php echo app('translator')->get('admin::app.components.datagrid.filters.dropdown.searchable.no-results'); ?>'"
                                    >
                                    </p>
                                </li>

                                <li
                                    v-for="option in searchedOptions"
                                    v-else
                                >
                                    <p
                                        class="cursor-pointer p-2 text-sm text-gray-600 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-950"
                                        v-text="option.label"
                                        @click="selectOption(option)"
                                    >
                                    </p>
                                </li>
                            </ul>
                        </ul>
                    </div>
                </div>
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
    </script>

    <script type="module">
        app.component('v-datagrid-searchable-dropdown', {
            template: '#v-datagrid-searchable-dropdown-template',

            props: ['datagridId', 'column'],

            data() {
                return {
                    isMinimumCharacters: false,

                    searchedOptions: [],
                };
            },

            methods: {
                lookUp($event) {
                    let params = {
                        datagrid_id: this.datagridId,
                        column: this.column.index,
                        search: $event.target.value,
                    };

                    if (! (params['search'].length > 1)) {
                        this.searchedOptions = [];

                        this.isMinimumCharacters = false;

                        return;
                    }

                    this.$axios
                        .get('<?php echo e(route('admin.datagrid.look_up')); ?>', {
                            params
                        })
                        .then(({
                            data
                        }) => {
                            this.isMinimumCharacters = true;

                            this.searchedOptions = data;
                        });
                },

                selectOption(option) {
                    this.searchedOptions = [];

                    this.$emit('select-option', {
                        target: {
                            value: option.value
                        }
                    });
                },
            }
        });
    </script>
<?php $__env->stopPush(); endif; ?>
<?php /**PATH C:\laragon\www\adv-crm\packages\Webkul\Admin\src/resources/views/components/datagrid/toolbar/filter.blade.php ENDPATH**/ ?>