<v-mega-search>
    <div class="relative flex w-[550px] max-w-[550px] items-center max-lg:w-[400px] ltr:ml-2.5 rtl:mr-2.5">
        <i class="icon-search absolute top-2 flex items-center text-2xl ltr:left-3 rtl:right-3"></i>

        <input
            type="text"
            class="block w-full rounded-3xl border bg-white px-10 py-1.5 leading-6 text-gray-600 transition-all hover:border-gray-400 focus:border-gray-400 dark:border-gray-800 dark:bg-gray-900 dark:text-gray-300 dark:hover:border-gray-400 dark:focus:border-gray-400"
            placeholder="<?php echo app('translator')->get('admin::app.components.layouts.header.mega-search.title'); ?>"
        >
    </div>
</v-mega-search>

<?php if (! $__env->hasRenderedOnce('f05cc489-2d44-48b7-9598-c2fa565092bf')): $__env->markAsRenderedOnce('f05cc489-2d44-48b7-9598-c2fa565092bf');
$__env->startPush('scripts'); ?>
    <script
        type="text/x-template"
        id="v-mega-search-template"
    >
        <div class="relative flex w-[550px] max-w-[550px] items-center max-lg:w-[400px] ltr:ml-2.5 rtl:mr-2.5">
            <i class="icon-search absolute top-2 flex items-center text-2xl ltr:left-3 rtl:right-3"></i>

            <input
                type="text"
                class="peer block w-full rounded-3xl border bg-white px-10 py-1.5 leading-6 text-gray-600 transition-all hover:border-gray-400 focus:border-gray-400 dark:border-gray-800 dark:bg-gray-900 dark:text-gray-300 dark:hover:border-gray-400 dark:focus:border-gray-400"
                :class="{'border-gray-400': isDropdownOpen}"
                placeholder="<?php echo app('translator')->get('admin::app.components.layouts.header.mega-search.title'); ?>"
                v-model.lazy="searchTerm"
                @click="searchTerm.length >= 2 ? isDropdownOpen = true : {}"
                v-debounce="500"
            >

            <div
                class="absolute top-10 z-10 w-full rounded-lg border bg-white shadow-[0px_0px_0px_0px_rgba(0,0,0,0.10),0px_1px_3px_0px_rgba(0,0,0,0.10),0px_5px_5px_0px_rgba(0,0,0,0.09),0px_12px_7px_0px_rgba(0,0,0,0.05),0px_22px_9px_0px_rgba(0,0,0,0.01),0px_34px_9px_0px_rgba(0,0,0,0.00)] dark:border-gray-800 dark:bg-gray-900"
                v-if="isDropdownOpen"
            >
                <!-- Search Tabs -->
                <div class="flex overflow-x-auto border-b text-sm text-gray-600 dark:border-gray-800 dark:text-gray-300">
                    <div
                        class="cursor-pointer p-4 hover:bg-gray-100 dark:hover:bg-gray-950"
                        :class="{ 'border-b-2 border-brandColor': activeTab == tab.key }"
                        v-for="tab in tabs"
                        @click="activeTab = tab.key; search();"
                    >
                        {{ tab.title }}
                    </div>
                </div>

                <!-- Searched Results -->
                <template v-if="activeTab == 'products'">
                    <template v-if="isLoading">
                        <?php if (isset($component)) { $__componentOriginaleba82ad7959d0b76a0ef438c765d88d7 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginaleba82ad7959d0b76a0ef438c765d88d7 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'admin::components.shimmer.header.mega-search.products','data' => []] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('admin::shimmer.header.mega-search.products'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(Illuminate\View\AnonymousComponent::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginaleba82ad7959d0b76a0ef438c765d88d7)): ?>
<?php $attributes = $__attributesOriginaleba82ad7959d0b76a0ef438c765d88d7; ?>
<?php unset($__attributesOriginaleba82ad7959d0b76a0ef438c765d88d7); ?>
<?php endif; ?>
<?php if (isset($__componentOriginaleba82ad7959d0b76a0ef438c765d88d7)): ?>
<?php $component = $__componentOriginaleba82ad7959d0b76a0ef438c765d88d7; ?>
<?php unset($__componentOriginaleba82ad7959d0b76a0ef438c765d88d7); ?>
<?php endif; ?>
                    </template>

                    <template v-else>
                        <div class="grid max-h-[400px] overflow-y-auto">
                            <template v-for="product in searchedResults.products">
                                <a
                                    :href="'<?php echo e(route('admin.products.view', ':id')); ?>'.replace(':id', product.id)"
                                    class="flex cursor-pointer justify-between gap-2.5 border-b border-slate-300 p-4 last:border-b-0 hover:bg-gray-100 dark:border-gray-800 dark:hover:bg-gray-950"
                                >
                                    <!-- Left Information -->
                                    <div class="flex gap-2.5">
                                        <!-- Details -->
                                        <div class="grid place-content-start gap-1.5">
                                            <p class="text-base font-semibold text-gray-600 dark:text-gray-300">
                                                {{ product.name }}
                                            </p>

                                            <p class="text-gray-500">
                                                {{ "<?php echo app('translator')->get(':sku'); ?>".replace(':sku', product.sku) }}
                                            </p>
                                        </div>
                                    </div>

                                    <!-- Right Information -->
                                    <div class="grid place-content-center gap-1 text-right">
                                        <!-- Formatted Price -->
                                        <p class="font-semibold text-gray-600 dark:text-gray-300">
                                            {{ $admin.formatPrice(product.price) }}
                                        </p>
                                    </div>
                                </a>
                            </template>

                        </div>

                        <div class="flex border-t p-3 dark:border-gray-800">
                            <template v-if="searchedResults.products.length">
                                <a
                                    :href="'<?php echo e(route('admin.products.index')); ?>?search=:query'.replace(':query', searchTerm)"
                                    class="cursor-pointer text-xs font-semibold text-brandColor transition-all hover:underline"
                                >

                                    {{ `<?php echo app('translator')->get('admin::app.components.layouts.header.mega-search.explore-all-matching-products'); ?>`.replace(':query', searchTerm).replace(':count', searchedResults.products.length) }}
                                </a>
                            </template>

                            <template v-else>
                                <a
                                    href="<?php echo e(route('admin.products.index')); ?>"
                                    class="cursor-pointer text-xs font-semibold text-brandColor transition-all hover:underline"
                                >
                                    <?php echo app('translator')->get('admin::app.components.layouts.header.mega-search.explore-all-products'); ?>
                                </a>
                            </template>
                        </div>
                    </template>
                </template>

                <template v-if="activeTab == 'leads'">
                    <template v-if="isLoading">
                        <?php if (isset($component)) { $__componentOriginal2f925dcb08db4e6b27de8bea570f7017 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal2f925dcb08db4e6b27de8bea570f7017 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'admin::components.shimmer.header.mega-search.leads','data' => []] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('admin::shimmer.header.mega-search.leads'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(Illuminate\View\AnonymousComponent::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal2f925dcb08db4e6b27de8bea570f7017)): ?>
<?php $attributes = $__attributesOriginal2f925dcb08db4e6b27de8bea570f7017; ?>
<?php unset($__attributesOriginal2f925dcb08db4e6b27de8bea570f7017); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal2f925dcb08db4e6b27de8bea570f7017)): ?>
<?php $component = $__componentOriginal2f925dcb08db4e6b27de8bea570f7017; ?>
<?php unset($__componentOriginal2f925dcb08db4e6b27de8bea570f7017); ?>
<?php endif; ?>
                    </template>

                    <template v-else>
                        <div class="grid max-h-[400px] overflow-y-auto">
                            <template v-for="lead in searchedResults.leads">
                                <a
                                    :href="'<?php echo e(route('admin.leads.view', ':id')); ?>'.replace(':id', lead.id)"
                                    class="flex cursor-pointer justify-between gap-2.5 border-b border-slate-300 p-4 last:border-b-0 hover:bg-gray-100 dark:border-gray-800 dark:hover:bg-gray-950"
                                >
                                    <!-- Left Information -->
                                    <div class="flex gap-2.5">
                                        <!-- Details -->
                                        <div class="grid place-content-start gap-1.5">
                                            <p class="text-base font-semibold text-gray-600 dark:text-gray-300">
                                                {{ lead.title }}
                                            </p>

                                            <p class="text-gray-500">
                                                {{ lead.description }}
                                            </p>
                                        </div>
                                    </div>

                                    <!-- Right Information -->
                                    <div class="grid place-content-center gap-1 text-right">
                                        <!-- Formatted Price -->
                                        <p class="font-semibold text-gray-600 dark:text-gray-300">
                                            {{ $admin.formatPrice(lead.lead_value) }}
                                        </p>
                                    </div>
                                </a>
                            </template>
                        </div>

                        <div class="flex border-t p-3 dark:border-gray-800">
                            <template v-if="searchedResults.leads.length">
                                <a
                                    :href="'<?php echo e(route('admin.leads.index')); ?>?search=:query'.replace(':query', searchTerm)"
                                    class="cursor-pointer text-xs font-semibold text-brandColor transition-all hover:underline"
                                >
                                    {{ `<?php echo app('translator')->get('admin::app.components.layouts.header.mega-search.explore-all-matching-leads'); ?>`.replace(':query', searchTerm).replace(':count', searchedResults.leads.length) }}
                                </a>
                            </template>

                            <template v-else>
                                <a
                                    href="<?php echo e(route('admin.leads.index')); ?>"
                                    class="cursor-pointer text-xs font-semibold text-brandColor transition-all hover:underline"
                                >
                                    <?php echo app('translator')->get('admin::app.components.layouts.header.mega-search.explore-all-leads'); ?>
                                </a>
                            </template>
                        </div>
                    </template>
                </template>

                <template v-if="activeTab == 'persons'">
                    <template v-if="isLoading">
                        <?php if (isset($component)) { $__componentOriginal30684fc1b7874708f9dfcb75a1b97bcd = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal30684fc1b7874708f9dfcb75a1b97bcd = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'admin::components.shimmer.header.mega-search.persons','data' => []] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('admin::shimmer.header.mega-search.persons'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(Illuminate\View\AnonymousComponent::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal30684fc1b7874708f9dfcb75a1b97bcd)): ?>
<?php $attributes = $__attributesOriginal30684fc1b7874708f9dfcb75a1b97bcd; ?>
<?php unset($__attributesOriginal30684fc1b7874708f9dfcb75a1b97bcd); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal30684fc1b7874708f9dfcb75a1b97bcd)): ?>
<?php $component = $__componentOriginal30684fc1b7874708f9dfcb75a1b97bcd; ?>
<?php unset($__componentOriginal30684fc1b7874708f9dfcb75a1b97bcd); ?>
<?php endif; ?>
                    </template>

                    <template v-else>
                        <div class="grid max-h-[400px] overflow-y-auto">
                            <template v-for="person in searchedResults.persons">
                                <a
                                    :href="'<?php echo e(route('admin.contacts.persons.view', ':id')); ?>'.replace(':id', person.id)"
                                    class="flex cursor-pointer justify-between gap-2.5 border-b border-slate-300 p-4 last:border-b-0 hover:bg-gray-100 dark:border-gray-800 dark:hover:bg-gray-950"
                                >
                                    <!-- Left Information -->
                                    <div class="flex gap-2.5">
                                        <!-- Details -->
                                        <div class="grid place-content-start gap-1.5">
                                            <p class="text-base font-semibold text-gray-600 dark:text-gray-300">
                                                {{ person.name }}
                                            </p>

                                            <p class="text-gray-500">
                                                {{ person.emails.map((item) => `${item.value}(${item.label})`).join(', ') }}
                                            </p>
                                        </div>
                                    </div>
                                </a>
                            </template>
                        </div>

                        <div class="flex border-t p-3 dark:border-gray-800">
                            <template v-if="searchedResults.persons.length">
                                <a
                                    :href="'<?php echo e(route('admin.contacts.persons.index')); ?>?search=:query'.replace(':query', searchTerm)"
                                    class="cursor-pointer text-xs font-semibold text-brandColor transition-all hover:underline"
                                >
                                    {{ `<?php echo app('translator')->get('admin::app.components.layouts.header.mega-search.explore-all-matching-contacts'); ?>`.replace(':query', searchTerm).replace(':count', searchedResults.persons.length) }}
                                </a>
                            </template>

                            <template v-else>
                                <a
                                    href="<?php echo e(route('admin.contacts.persons.index')); ?>"
                                    class="cursor-pointer text-xs font-semibold text-brandColor transition-all hover:underline"
                                >
                                    <?php echo app('translator')->get('admin::app.components.layouts.header.mega-search.explore-all-contacts'); ?>
                                </a>
                            </template>
                        </div>
                    </template>
                </template>

                <template v-if="activeTab == 'quotes'">
                    <template v-if="isLoading">
                        <?php if (isset($component)) { $__componentOriginal6e06320b6437488bf17b056981c9f8ad = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal6e06320b6437488bf17b056981c9f8ad = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'admin::components.shimmer.header.mega-search.quotes','data' => []] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('admin::shimmer.header.mega-search.quotes'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(Illuminate\View\AnonymousComponent::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal6e06320b6437488bf17b056981c9f8ad)): ?>
<?php $attributes = $__attributesOriginal6e06320b6437488bf17b056981c9f8ad; ?>
<?php unset($__attributesOriginal6e06320b6437488bf17b056981c9f8ad); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal6e06320b6437488bf17b056981c9f8ad)): ?>
<?php $component = $__componentOriginal6e06320b6437488bf17b056981c9f8ad; ?>
<?php unset($__componentOriginal6e06320b6437488bf17b056981c9f8ad); ?>
<?php endif; ?>
                    </template>

                    <template v-else>
                        <div class="grid max-h-[400px] overflow-y-auto">
                            <template v-for="quote in searchedResults.quotes">
                                <a
                                    :href="'<?php echo e(route('admin.quotes.edit', ':id')); ?>'.replace(':id', quote.id)"
                                    class="flex cursor-pointer justify-between gap-2.5 border-b border-slate-300 p-4 last:border-b-0 hover:bg-gray-100 dark:border-gray-800 dark:hover:bg-gray-950"
                                >
                                    <!-- Left Information -->
                                    <div class="flex gap-2.5">
                                        <!-- Details -->
                                        <div class="grid place-content-start gap-1.5">
                                            <p class="text-base font-semibold text-gray-600 dark:text-gray-300">
                                                {{ quote.subject }}
                                            </p>

                                            <p class="text-gray-500">
                                                {{ quote.description }}
                                            </p>
                                        </div>
                                    </div>
                                </a>
                            </template>
                        </div>

                        <div class="flex border-t p-3 dark:border-gray-800">
                            <template v-if="searchedResults.quotes.length">
                                <a
                                    :href="'<?php echo e(route('admin.quotes.index')); ?>?search=:query'.replace(':query', searchTerm)"
                                    class="cursor-pointer text-xs font-semibold text-brandColor transition-all hover:underline"
                                >
                                    {{ `<?php echo app('translator')->get('admin::app.components.layouts.header.mega-search.explore-all-matching-quotes'); ?>`.replace(':query', searchTerm).replace(':count', searchedResults.quotes.length) }}
                                </a>
                            </template>

                            <template v-else>
                                <a
                                    href="<?php echo e(route('admin.quotes.index')); ?>"
                                    class="cursor-pointer text-xs font-semibold text-brandColor transition-all hover:underline"
                                >
                                    <?php echo app('translator')->get('admin::app.components.layouts.header.mega-search.explore-all-quotes'); ?>
                                </a>
                            </template>
                        </div>
                    </template>
                </template>

                <template v-if="activeTab == 'settings'">
                    <template v-if="isLoading">
                        <?php if (isset($component)) { $__componentOriginala4e0756eac537eaa75d0fef045195590 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginala4e0756eac537eaa75d0fef045195590 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'admin::components.shimmer.header.mega-search.settings','data' => []] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('admin::shimmer.header.mega-search.settings'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(Illuminate\View\AnonymousComponent::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginala4e0756eac537eaa75d0fef045195590)): ?>
<?php $attributes = $__attributesOriginala4e0756eac537eaa75d0fef045195590; ?>
<?php unset($__attributesOriginala4e0756eac537eaa75d0fef045195590); ?>
<?php endif; ?>
<?php if (isset($__componentOriginala4e0756eac537eaa75d0fef045195590)): ?>
<?php $component = $__componentOriginala4e0756eac537eaa75d0fef045195590; ?>
<?php unset($__componentOriginala4e0756eac537eaa75d0fef045195590); ?>
<?php endif; ?>
                    </template>

                    <template v-else>
                        <div class="grid max-h-[400px] overflow-y-auto">
                            <template v-for="setting in searchedResults.settings">
                                <a
                                    :href="setting.url"
                                    class="flex cursor-pointer justify-between gap-2.5 border-b border-slate-300 p-4 last:border-b-0 hover:bg-gray-100 dark:border-gray-800 dark:hover:bg-gray-950"
                                >
                                    <!-- Left Information -->
                                    <div class="flex gap-2.5">
                                        <!-- Details -->
                                        <div class="grid place-content-start gap-1.5">
                                            <p class="text-base font-semibold text-gray-600 dark:text-gray-300">
                                                {{ setting.name }}
                                            </p>
                                        </div>
                                    </div>
                                </a>
                            </template>
                        </div>

                        <template v-if="! searchedResults.settings.length">
                            <div class="flex border-t p-3 dark:border-gray-800">
                                <a
                                    href="<?php echo e(route('admin.settings.index')); ?>"
                                    class="cursor-pointer text-xs font-semibold text-brandColor transition-all hover:underline"
                                >
                                    <?php echo app('translator')->get('admin::app.components.layouts.header.mega-search.explore-all-settings'); ?>
                                </a>
                            </div>
                        </template>
                    </template>
                </template>

                <template v-if="activeTab == 'configurations'">
                    <template v-if="isLoading">
                        <?php if (isset($component)) { $__componentOriginalf06a76bdf770b8a912622db8d4d5fbc0 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalf06a76bdf770b8a912622db8d4d5fbc0 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'admin::components.shimmer.header.mega-search.configurations','data' => []] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('admin::shimmer.header.mega-search.configurations'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(Illuminate\View\AnonymousComponent::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalf06a76bdf770b8a912622db8d4d5fbc0)): ?>
<?php $attributes = $__attributesOriginalf06a76bdf770b8a912622db8d4d5fbc0; ?>
<?php unset($__attributesOriginalf06a76bdf770b8a912622db8d4d5fbc0); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalf06a76bdf770b8a912622db8d4d5fbc0)): ?>
<?php $component = $__componentOriginalf06a76bdf770b8a912622db8d4d5fbc0; ?>
<?php unset($__componentOriginalf06a76bdf770b8a912622db8d4d5fbc0); ?>
<?php endif; ?>
                    </template>

                    <template v-else>
                        <div class="grid max-h-[400px] overflow-y-auto">
                            <template v-for="configuration in searchedResults.configurations">
                                <a
                                    :href="configuration.url"
                                    class="flex cursor-pointer justify-between gap-2.5 border-b border-slate-300 p-4 last:border-b-0 hover:bg-gray-100 dark:border-gray-800 dark:hover:bg-gray-950"
                                >
                                    <!-- Left Information -->
                                    <div class="flex gap-2.5">
                                        <!-- Details -->
                                        <div class="grid place-content-start gap-1.5">
                                            <p class="text-base font-semibold text-gray-600 dark:text-gray-300">
                                                {{ configuration.title }}
                                            </p>
                                        </div>
                                    </div>
                                </a>
                            </template>
                        </div>

                        <template v-if="! searchedResults.configurations.length">
                            <div class="flex border-t p-3 dark:border-gray-800">
                                <a
                                    href="<?php echo e(route('admin.configuration.index')); ?>"
                                    class="cursor-pointer text-xs font-semibold text-brandColor transition-all hover:underline"
                                >
                                    <?php echo app('translator')->get('admin::app.components.layouts.header.mega-search.explore-all-configurations'); ?>
                                </a>
                            </div>
                        </template>
                    </template>
                </template>
            </div>
        </div>
    </script>

    <script type="module">
        app.component('v-mega-search', {
            template: '#v-mega-search-template',

            data() {
                return  {
                    activeTab: 'leads',

                    isDropdownOpen: false,

                    tabs: {
                        leads: {
                            key: 'leads',
                            title: '<?php echo app('translator')->get('admin::app.components.layouts.header.mega-search.tabs.leads'); ?>',
                            is_active: true,
                            endpoint: '<?php echo e(route('admin.leads.search')); ?>',
                            query_params: [
                                {
                                    search: 'title',
                                    searchFields: 'title:like',
                                },
                                {
                                    search: 'user.name',
                                    searchFields: 'user.name:like',
                                },
                                {
                                    search: 'person.name',
                                    searchFields: 'person.name:like',
                                },
                            ],
                        },

                        quotes: {
                            key: 'quotes',
                            title: '<?php echo app('translator')->get('admin::app.components.layouts.header.mega-search.tabs.quotes'); ?>',
                            is_active: false,
                            endpoint: '<?php echo e(route('admin.quotes.search')); ?>',
                            query_params: [
                                {
                                    search: 'subject',
                                    searchFields: 'subject:like',
                                },
                                {
                                    search: 'description',
                                    searchFields: 'description:like',
                                },
                                {
                                    search: 'user.name',
                                    searchFields: 'user.name:like',
                                },
                                {
                                    search: 'person.name',
                                    searchFields: 'person.name:like',
                                },
                            ],
                        },

                        products: {
                            key: 'products',
                            title: '<?php echo app('translator')->get('admin::app.components.layouts.header.mega-search.tabs.products'); ?>',
                            is_active: false,
                            endpoint: '<?php echo e(route('admin.products.search')); ?>',
                            query_params: [
                                {
                                    search: 'name',
                                    searchFields: 'name:like',
                                },
                                {
                                    search: 'sku',
                                    searchFields: 'sku:like',
                                },
                                {
                                    search: 'description',
                                    searchFields: 'description:like',
                                },
                            ],
                        },

                        persons: {
                            key: 'persons',
                            title: '<?php echo app('translator')->get('admin::app.components.layouts.header.mega-search.tabs.persons'); ?>',
                            is_active: false,
                            endpoint: '<?php echo e(route('admin.contacts.persons.search')); ?>',
                            query_params: [
                                {
                                    search: 'name',
                                    searchFields: 'name:like',
                                },
                                {
                                    search: 'job_title',
                                    searchFields: 'job_title:like',
                                },
                                {
                                    search: 'user.name',
                                    searchFields: 'user.name:like',
                                },
                                {
                                    search: 'organization.name',
                                    searchFields: 'organization.name:like',
                                },
                            ],
                        },

                        settings: {
                            key: 'settings',
                            title: '<?php echo app('translator')->get('Settings'); ?>',
                            is_active: false,
                            endpoint: '<?php echo e(route('admin.settings.search')); ?>',
                            query: '',
                        },

                        configurations: {
                            key: 'configurations',
                            title: '<?php echo app('translator')->get('Configurations'); ?>',
                            is_active: false,
                            endpoint: '<?php echo e(route('admin.configuration.search')); ?>',
                            query: '',
                        },
                    },

                    isLoading: false,

                    searchTerm: '',

                    searchedResults: {
                        leads: [],
                        quotes: [],
                        products: [],
                        persons: [],
                        settings: [],
                        configurations: [],
                    },

                    params: {
                        search: '',
                        searchFields: '',
                    },
                };
            },

            watch: {
                searchTerm: 'updateSearchParams',

                activeTab: 'updateSearchParams',
            },

            created() {
                window.addEventListener('click', this.handleFocusOut);
            },

            beforeDestroy() {
                window.removeEventListener('click', this.handleFocusOut);
            },

            methods: {
                search(endpoint = null) {
                    if (! endpoint) {
                        return;
                    }

                    if (this.searchTerm.length <= 1) {
                        this.searchedResults[this.activeTab] = [];

                        this.isDropdownOpen = false;

                        return;
                    }

                    this.isDropdownOpen = true;

                    this.$axios.get(endpoint, {
                            params: {
                                ...this.params,
                            },
                        })
                        .then((response) => {
                            this.searchedResults[this.activeTab] = response.data.data;
                        })
                        .catch((error) => {})
                        .finally(() => this.isLoading = false);
                },

                handleFocusOut(e) {
                    if (! this.$el.contains(e.target)) {
                        this.isDropdownOpen = false;
                    }
                },

                updateSearchParams() {
                    const newTerm = this.searchTerm;

                    this.params = {
                        search: '',
                        searchFields: '',
                    };

                    const tab = this.tabs[this.activeTab];

                    if (
                        tab.key === 'settings'
                        || tab.key === 'configurations'
                    ) {
                        this.params = null;

                        this.search(`${tab.endpoint}?query=${newTerm}`);

                        return;
                    }

                    this.params.search += tab.query_params.map((param) => `${param.search}:${newTerm};`).join('');

                    this.params.searchFields += tab.query_params.map((param) => `${param.searchFields};`).join('');

                    this.search(tab.endpoint);
                },
            },
        });
    </script>
<?php $__env->stopPush(); endif; ?>
<?php /**PATH C:\laragon\www\adv-crm\packages\Webkul\Admin\src/resources/views/components/layouts/header/desktop/mega-search.blade.php ENDPATH**/ ?>