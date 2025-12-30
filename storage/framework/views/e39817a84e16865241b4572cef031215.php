<v-sidebar-drawer>
    <i class="icon-menu lg:hidden cursor-pointer rounded-md p-1.5 text-2xl hover:bg-gray-100 dark:hover:bg-gray-950 max-lg:block"></i>
</v-sidebar-drawer>

<?php if (! $__env->hasRenderedOnce('113a6e79-cfac-4d6b-ba3d-f91e522ab309')): $__env->markAsRenderedOnce('113a6e79-cfac-4d6b-ba3d-f91e522ab309');
$__env->startPush('scripts'); ?>
    <script
        type="text/x-template"
        id="v-sidebar-drawer-template"
    >
        <?php if (isset($component)) { $__componentOriginal9bfb526197f1d7304e7fade44c26fbb8 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal9bfb526197f1d7304e7fade44c26fbb8 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'admin::components.drawer.index','data' => ['position' => 'left','width' => '280px','class' => 'lg:hidden [&>:nth-child(3)]:!m-0 [&>:nth-child(3)]:!rounded-l-none [&>:nth-child(3)]:max-sm:!w-[80%]']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('admin::drawer'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(Illuminate\View\AnonymousComponent::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes(['position' => 'left','width' => '280px','class' => 'lg:hidden [&>:nth-child(3)]:!m-0 [&>:nth-child(3)]:!rounded-l-none [&>:nth-child(3)]:max-sm:!w-[80%]']); ?>
             <?php $__env->slot('toggle', null, []); ?> 
                <i class="icon-menu lg:hidden cursor-pointer rounded-md p-1.5 text-2xl hover:bg-gray-100 dark:hover:bg-gray-950 max-lg:block"></i>
             <?php $__env->endSlot(); ?>

             <?php $__env->slot('header', null, []); ?> 
                <?php if($logo = core()->getConfigData('general.design.admin_logo.logo_image')): ?>
                    <img
                        class="h-10"
                        src="<?php echo e(Storage::url($logo)); ?>"
                        alt="<?php echo e(config('app.name')); ?>"
                    />
                <?php else: ?>
                    <img
                        class="h-10"
                        src="<?php echo e(request()->cookie('dark_mode') ? vite()->asset('images/dark-logo.svg') : vite()->asset('images/logo.svg')); ?>"
                        id="logo-image"
                        alt="<?php echo e(config('app.name')); ?>"
                    />
                <?php endif; ?>
             <?php $__env->endSlot(); ?>

             <?php $__env->slot('content', null, ['class' => 'p-4']); ?> 
                <div class="journal-scroll h-[calc(100vh-100px)] overflow-auto">
                    <nav class="grid w-full gap-2">
                        <?php $__currentLoopData = menu()->getItems('admin'); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $menuItem): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <?php
                                $hasActiveChild = $menuItem->haveChildren() && collect($menuItem->getChildren())->contains(fn($child) => $child->isActive());

                                $isMenuActive = $menuItem->isActive() == 'active' || $hasActiveChild;

                                $menuKey = $menuItem->getKey();
                            ?>

                            <div
                                class="menu-item relative"
                                data-menu-key="<?php echo e($menuKey); ?>"
                            >
                                <a
                                    href="<?php echo e(! in_array($menuItem->getKey(), ['settings', 'configuration']) && $menuItem->haveChildren() ? 'javascript:void(0)' : $menuItem->getUrl()); ?>"
                                    class="menu-link flex items-center justify-between rounded-lg p-2 transition-colors duration-200"
                                    <?php if($menuItem->haveChildren() && !in_array($menuKey, ['settings', 'configuration'])): ?>
                                        @click.prevent="toggleMenu('<?php echo e($menuKey); ?>')"
                                    <?php endif; ?>
                                    :class="{ 'bg-brandColor text-white': activeMenu === '<?php echo e($menuKey); ?>' || <?php echo e($isMenuActive ? 'true' : 'false'); ?>, 'text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-950': !(activeMenu === '<?php echo e($menuKey); ?>' || <?php echo e($isMenuActive ? 'true' : 'false'); ?>) }"
                                >
                                    <div class="flex items-center gap-3">
                                        <span class="<?php echo e($menuItem->getIcon()); ?> text-2xl"></span>

                                        <p class="whitespace-nowrap font-semibold"><?php echo e($menuItem->getName()); ?></p>
                                    </div>

                                    <?php if($menuItem->haveChildren()): ?>
                                        <span
                                            class="transform text-lg transition-transform duration-300"
                                            :class="{ 'icon-arrow-up': activeMenu === '<?php echo e($menuKey); ?>', 'icon-arrow-down': activeMenu !== '<?php echo e($menuKey); ?>' }"
                                        ></span>
                                    <?php endif; ?>
                                </a>

                                <?php if($menuItem->haveChildren() && !in_array($menuKey, ['settings', 'configuration'])): ?>
                                    <div
                                        class="submenu ml-1 mt-1 overflow-hidden rounded-b-lg border-l-2 transition-all duration-300 dark:border-gray-700"
                                        :class="{ 'max-h-[500px] py-2 border-l-brandColor bg-gray-50 dark:bg-gray-900': activeMenu === '<?php echo e($menuKey); ?>' || <?php echo e($hasActiveChild ? 'true' : 'false'); ?>, 'max-h-0 py-0 border-transparent bg-transparent': activeMenu !== '<?php echo e($menuKey); ?>' && !<?php echo e($hasActiveChild ? 'true' : 'false'); ?> }"
                                    >
                                        <?php $__currentLoopData = $menuItem->getChildren(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $subMenuItem): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <a
                                                href="<?php echo e($subMenuItem->getUrl()); ?>"
                                                class="submenu-link block whitespace-nowrap p-2 pl-10 text-sm transition-colors duration-200"
                                                :class="{ 'text-brandColor font-medium bg-gray-100 dark:bg-gray-800': '<?php echo e($subMenuItem->isActive()); ?>' === '1', 'text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-800': '<?php echo e($subMenuItem->isActive()); ?>' !== '1' }">
                                                <?php echo e($subMenuItem->getName()); ?>

                                            </a>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </nav>
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
    </script>

    <script type="module">
        app.component('v-sidebar-drawer', {
            template: '#v-sidebar-drawer-template',

            data() {
                return { activeMenu: null };
            },

            mounted() {
                const activeElement = document.querySelector('.menu-item .menu-link.bg-brandColor');

                if (activeElement) {
                    this.activeMenu = activeElement.closest('.menu-item').getAttribute('data-menu-key');
                }
            },

            methods: {
                toggleMenu(menuKey) {
                    this.activeMenu = this.activeMenu === menuKey ? null : menuKey;
                }
            },
        });
    </script>
<?php $__env->stopPush(); endif; ?>
<?php /**PATH C:\laragon\www\adv-crm\packages\Webkul\Admin\src/resources/views/components/layouts/sidebar/mobile/index.blade.php ENDPATH**/ ?>