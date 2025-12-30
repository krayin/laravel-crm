<div>
    <?php if(
            bouncer()->hasPermission('leads.create')
            || bouncer()->hasPermission('quotes.create')
            || bouncer()->hasPermission('mail.create')
            || bouncer()->hasPermission('contacts.persons.create')
            || bouncer()->hasPermission('contacts.organizations.create')
            || bouncer()->hasPermission('products.create')
            || bouncer()->hasPermission('settings.automation.attributes.create')
            || bouncer()->hasPermission('settings.user.roles.create')
            || bouncer()->hasPermission('settings.user.users.create')
        ): ?>
        <?php if (isset($component)) { $__componentOriginalaf937e0ec72fa678d3a0c6dc6c0ac5f2 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalaf937e0ec72fa678d3a0c6dc6c0ac5f2 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'admin::components.dropdown.index','data' => ['position' => 'bottom-right']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('admin::dropdown'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(Illuminate\View\AnonymousComponent::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes(['position' => 'bottom-right']); ?>
             <?php $__env->slot('toggle', null, []); ?> 
                <!-- Toggle Button -->
                <button
                    class="flex h-9 w-9 cursor-pointer items-center justify-center rounded-full bg-brandColor text-white">
                    <i class="icon-add text-2xl"></i>
                </button>
                 <?php $__env->endSlot(); ?>

                <!-- Dropdown Content -->
                 <?php $__env->slot('content', null, ['class' => 'mt-2 !p-0']); ?> 
                    <div class="relative px-2 py-4">
                        <div class="grid grid-cols-3 gap-2 text-center max-sm:grid-cols-2">
                            <!-- Link to create new Lead -->
                            <?php if(bouncer()->hasPermission('leads.create')): ?>
                                <div class="rounded-lg bg-white p-2 hover:bg-gray-100 dark:bg-gray-800 dark:hover:bg-gray-950">
                                    <a href="<?php echo e(route('admin.leads.create')); ?>">
                                        <div class="flex flex-col gap-1">
                                            <i class="icon-leads text-2xl text-gray-600"></i>

                                            <span class="font-medium dark:text-gray-300"><?php echo app('translator')->get('admin::app.layouts.lead'); ?></span>
                                        </div>
                                    </a>
                                </div>
                            <?php endif; ?>

                            <!-- Link to create new Quotes -->
                            <?php if(bouncer()->hasPermission('quotes.create')): ?>
                                <div class="rounded-lg bg-white p-2 hover:bg-gray-100 dark:bg-gray-800 dark:hover:bg-gray-950">
                                    <a href="<?php echo e(route('admin.quotes.create')); ?>">
                                        <div class="flex flex-col gap-1">
                                            <i class="icon-quote text-2xl text-gray-600"></i>

                                            <span
                                                class="font-medium dark:text-gray-300"><?php echo app('translator')->get('admin::app.layouts.quote'); ?></span>
                                        </div>
                                    </a>
                                </div>
                            <?php endif; ?>

                            <!-- Link to send new Mail-->
                            <?php if(bouncer()->hasPermission('mail.create')): ?>
                                <div class="rounded-lg bg-white p-2 hover:bg-gray-100 dark:bg-gray-800 dark:hover:bg-gray-950">
                                    <a href="<?php echo e(route('admin.mail.index', ['route' => 'inbox', 'openModal' => 'true'])); ?>">
                                        <div class="flex flex-col gap-1">
                                            <i class="icon-mail text-2xl text-gray-600"></i>

                                            <span
                                                class="font-medium dark:text-gray-300"><?php echo app('translator')->get('admin::app.layouts.email'); ?></span>
                                        </div>
                                    </a>
                                </div>
                            <?php endif; ?>

                            <!-- Link to create new Person-->
                            <?php if(bouncer()->hasPermission('contacts.persons.create')): ?>
                                <div class="rounded-lg bg-white p-2 hover:bg-gray-100 dark:bg-gray-800 dark:hover:bg-gray-950">
                                    <a href="<?php echo e(route('admin.contacts.persons.create')); ?>">
                                        <div class="flex flex-col gap-1">
                                            <i class="icon-settings-user text-2xl text-gray-600"></i>

                                            <span
                                                class="font-medium dark:text-gray-300"><?php echo app('translator')->get('admin::app.layouts.person'); ?></span>
                                        </div>
                                    </a>
                                </div>
                            <?php endif; ?>

                            <!-- Link to create new Organizations -->
                            <?php if(bouncer()->hasPermission('contacts.organizations.create')): ?>
                                <div class="rounded-lg bg-white p-2 hover:bg-gray-100 dark:bg-gray-800 dark:hover:bg-gray-950">
                                    <a href="<?php echo e(route('admin.contacts.organizations.create')); ?>">
                                        <div class="flex flex-col gap-1">
                                            <i class="icon-organization text-2xl text-gray-600"></i>

                                            <span
                                                class="font-medium dark:text-gray-300"><?php echo app('translator')->get('admin::app.layouts.organization'); ?></span>
                                        </div>
                                    </a>
                                </div>
                            <?php endif; ?>

                            <!-- Link to create new Products -->
                            <?php if(bouncer()->hasPermission('products.create')): ?>
                                <div class="rounded-lg bg-white p-2 hover:bg-gray-100 dark:bg-gray-800 dark:hover:bg-gray-950">
                                    <a href="<?php echo e(route('admin.products.create')); ?>">
                                        <div class="flex flex-col gap-1">
                                            <i class="icon-product text-2xl text-gray-600"></i>

                                            <span
                                                class="font-medium dark:text-gray-300"><?php echo app('translator')->get('admin::app.layouts.product'); ?></span>
                                        </div>
                                    </a>
                                </div>
                            <?php endif; ?>

                            <!-- Link to create new Attributes -->
                            <?php if(bouncer()->hasPermission('settings.automation.attributes.create')): ?>
                                <div class="rounded-lg bg-white p-2 hover:bg-gray-100 dark:bg-gray-800 dark:hover:bg-gray-950">
                                    <a href="<?php echo e(route('admin.settings.attributes.create')); ?>">
                                        <div class="flex flex-col gap-1">
                                            <i class="icon-attribute text-2xl text-gray-600"></i>

                                            <span
                                                class="font-medium dark:text-gray-300"><?php echo app('translator')->get('admin::app.layouts.attribute'); ?></span>
                                        </div>
                                    </a>
                                </div>
                            <?php endif; ?>

                            <!-- Link to create new Role -->
                            <?php if(bouncer()->hasPermission('settings.user.roles.create')): ?>
                                <div class="rounded-lg bg-white p-2 hover:bg-gray-100 dark:bg-gray-800 dark:hover:bg-gray-950">
                                    <a href="<?php echo e(route('admin.settings.roles.create')); ?>">
                                        <div class="flex flex-col gap-1">
                                            <i class="icon-role text-2xl text-gray-600"></i>

                                            <span class="font-medium dark:text-gray-300"><?php echo app('translator')->get('admin::app.layouts.role'); ?></span>
                                        </div>
                                    </a>
                                </div>
                            <?php endif; ?>

                            <!-- Link to create new User-->
                            <?php if(bouncer()->hasPermission('settings.user.users.create')): ?>
                                <div class="rounded-lg bg-white p-2 hover:bg-gray-100 dark:bg-gray-800 dark:hover:bg-gray-950">
                                    <a href="<?php echo e(route('admin.settings.users.index', ['action' => 'create'])); ?>">
                                        <div class="flex flex-col gap-1">
                                            <i class="icon-user text-2xl text-gray-600"></i>

                                            <span class="font-medium dark:text-gray-300"><?php echo app('translator')->get('admin::app.layouts.user'); ?></span>
                                        </div>
                                    </a>
                                </div>
                            <?php endif; ?>
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
    <?php endif; ?>
</div><?php /**PATH C:\laragon\www\adv-crm\packages\Webkul\Admin\src/resources/views/components/layouts/header/quick-creation.blade.php ENDPATH**/ ?>