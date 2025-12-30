<header class="sticky top-0 z-[10001] flex items-center justify-between gap-1 border-b border-gray-200 bg-white px-4 py-2.5 transition-all dark:border-gray-800 dark:bg-gray-900">  
    <!-- logo -->
    <div class="flex items-center gap-1.5">
        <!-- Sidebar Menu -->
        <?php if (isset($component)) { $__componentOriginal036663dd41c1380e7f35defb05a5d94d = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal036663dd41c1380e7f35defb05a5d94d = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'admin::components.layouts.sidebar.mobile.index','data' => []] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('admin::layouts.sidebar.mobile'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(Illuminate\View\AnonymousComponent::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal036663dd41c1380e7f35defb05a5d94d)): ?>
<?php $attributes = $__attributesOriginal036663dd41c1380e7f35defb05a5d94d; ?>
<?php unset($__attributesOriginal036663dd41c1380e7f35defb05a5d94d); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal036663dd41c1380e7f35defb05a5d94d)): ?>
<?php $component = $__componentOriginal036663dd41c1380e7f35defb05a5d94d; ?>
<?php unset($__componentOriginal036663dd41c1380e7f35defb05a5d94d); ?>
<?php endif; ?>
        
        <a href="<?php echo e(route('admin.dashboard.index')); ?>">
            <?php if($logo = core()->getConfigData('general.general.admin_logo.logo_image')): ?>
                <img
                    class="h-10"
                    src="<?php echo e(Storage::url($logo)); ?>"
                    alt="<?php echo e(config('app.name')); ?>"
                />
            <?php else: ?>
                <img
                    class="h-10 max-sm:hidden"
                    src="<?php echo e(request()->cookie('dark_mode') ? vite()->asset('images/dark-logo.svg') : vite()->asset('images/logo.svg')); ?>"
                    id="logo-image"
                    alt="<?php echo e(config('app.name')); ?>"
                />

                <img
                    class="h-10 sm:hidden"
                    src="<?php echo e(request()->cookie('dark_mode') ? vite()->asset('images/mobile-dark-logo.svg') : vite()->asset('images/mobile-light-logo.svg')); ?>"
                    id="logo-image"
                    alt="<?php echo e(config('app.name')); ?>"
                />
            <?php endif; ?>
        </a>
    </div>

    <div class="flex items-center gap-1.5 max-md:hidden">
        <!-- Mega Search Bar -->
        <?php echo $__env->make('admin::components.layouts.header.desktop.mega-search', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

        <!-- Quick Creation Bar -->
        <?php echo $__env->make('admin::components.layouts.header.quick-creation', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
    </div>

    <div class="flex items-center gap-2.5">
        <div class="md:hidden">
            <!-- Mega Search Bar -->
            <?php echo $__env->make('admin::components.layouts.header.mobile.mega-search', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
        </div>
        
        <!-- Dark mode -->
        <v-dark>
            <div class="flex">
                <span
                    class="<?php echo e(request()->cookie('dark_mode') ? 'icon-light' : 'icon-dark'); ?> p-1.5 rounded-md text-2xl cursor-pointer transition-all hover:bg-gray-100 dark:hover:bg-gray-950"
                ></span>
            </div>
        </v-dark>

        <div class="md:hidden">
            <!-- Quick Creation Bar -->
            <?php echo $__env->make('admin::components.layouts.header.quick-creation', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
        </div>
        
        <!-- Admin profile -->
        <?php if (isset($component)) { $__componentOriginalaf937e0ec72fa678d3a0c6dc6c0ac5f2 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalaf937e0ec72fa678d3a0c6dc6c0ac5f2 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'admin::components.dropdown.index','data' => ['position' => 'bottom-'.e(in_array(app()->getLocale(), ['fa', 'ar']) ? 'left' : 'right').'']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('admin::dropdown'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(Illuminate\View\AnonymousComponent::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes(['position' => 'bottom-'.e(in_array(app()->getLocale(), ['fa', 'ar']) ? 'left' : 'right').'']); ?>
             <?php $__env->slot('toggle', null, []); ?> 
                <?php ($user = auth()->guard('user')->user()); ?>

                <?php if($user->image): ?>
                    <button class="flex h-9 w-9 cursor-pointer overflow-hidden rounded-full hover:opacity-80 focus:opacity-80">
                        <img
                            src="<?php echo e($user->image_url); ?>"
                            class="h-full w-full object-cover"
                        />
                    </button>
                <?php else: ?>
                    <button class="flex h-9 w-9 cursor-pointer items-center justify-center rounded-full bg-pink-400 font-semibold leading-6 text-white">
                        <?php echo e(substr($user->name, 0, 1)); ?>

                    </button>
                <?php endif; ?>
             <?php $__env->endSlot(); ?>

            <!-- Admin Dropdown -->
             <?php $__env->slot('content', null, ['class' => 'mt-2 border-t-0 !p-0']); ?> 
                <div class="flex items-center gap-1.5 border border-x-0 border-b-gray-300 px-5 py-2.5 dark:border-gray-800">
                    <img
                        src="<?php echo e(url('cache/logo.png')); ?>"
                        width="24"
                        height="24"
                    />

                    <!-- Version -->
                    <p class="text-gray-400">
                        <?php echo app('translator')->get('admin::app.layouts.app-version', ['version' => core()->version()]); ?>
                    </p>
                </div>

                <div class="grid gap-1 pb-2.5">
                    <a
                        class="cursor-pointer px-5 py-2 text-base text-gray-800 hover:bg-gray-100 dark:text-white dark:hover:bg-gray-950"
                        href="<?php echo e(route('admin.user.account.edit')); ?>"
                    >
                        <?php echo app('translator')->get('admin::app.layouts.my-account'); ?>
                    </a>

                    <!--Admin logout-->
                    <?php if (isset($component)) { $__componentOriginal81b4d293d9113446bb908fc8aef5c8f6 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal81b4d293d9113446bb908fc8aef5c8f6 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'admin::components.form.index','data' => ['method' => 'DELETE','action' => ''.e(route('admin.session.destroy')).'','id' => 'adminLogout']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('admin::form'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(Illuminate\View\AnonymousComponent::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes(['method' => 'DELETE','action' => ''.e(route('admin.session.destroy')).'','id' => 'adminLogout']); ?>
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

                    <a
                        class="cursor-pointer px-5 py-2 text-base text-gray-800 hover:bg-gray-100 dark:text-white dark:hover:bg-gray-950"
                        href="<?php echo e(route('admin.session.destroy')); ?>"
                        onclick="event.preventDefault(); document.getElementById('adminLogout').submit();"
                    >
                        <?php echo app('translator')->get('admin::app.layouts.sign-out'); ?>
                    </a>
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
    </div>
</header>

<?php if (! $__env->hasRenderedOnce('6e614a88-97af-433c-86dd-e09e682d0b18')): $__env->markAsRenderedOnce('6e614a88-97af-433c-86dd-e09e682d0b18');
$__env->startPush('scripts'); ?>
    <script
        type="text/x-template"
        id="v-dark-template"
    >
        <div class="flex">
            <span
                class="cursor-pointer rounded-md p-1.5 text-2xl transition-all hover:bg-gray-100 dark:hover:bg-gray-950"
                :class="[isDarkMode ? 'icon-light' : 'icon-dark']"
                @click="toggle"
            ></span>
        </div>
    </script>

    <script type="module">
        app.component('v-dark', {
            template: '#v-dark-template',

            data() {
                return {
                    isDarkMode: <?php echo e(request()->cookie('dark_mode') ?? 0); ?>,

                    logo: "<?php echo e(vite()->asset('images/logo.svg')); ?>",

                    dark_logo: "<?php echo e(vite()->asset('images/dark-logo.svg')); ?>",
                };
            },

            methods: {
                toggle() {
                    this.isDarkMode = parseInt(this.isDarkModeCookie()) ? 0 : 1;

                    var expiryDate = new Date();

                    expiryDate.setMonth(expiryDate.getMonth() + 1);

                    document.cookie = 'dark_mode=' + this.isDarkMode + '; path=/; expires=' + expiryDate.toGMTString();

                    document.documentElement.classList.toggle('dark', this.isDarkMode === 1);

                    if (this.isDarkMode) {
                        this.$emitter.emit('change-theme', 'dark');

                        document.getElementById('logo-image').src = this.dark_logo;
                    } else {
                        this.$emitter.emit('change-theme', 'light');

                        document.getElementById('logo-image').src = this.logo;
                    }
                },

                isDarkModeCookie() {
                    const cookies = document.cookie.split(';');

                    for (const cookie of cookies) {
                        const [name, value] = cookie.trim().split('=');

                        if (name === 'dark_mode') {
                            return value;
                        }
                    }

                    return 0;
                },
            },
        });
    </script>
<?php $__env->stopPush(); endif; ?>
<?php /**PATH C:\laragon\www\adv-crm\packages\Webkul\Admin\src/resources/views/components/layouts/header/index.blade.php ENDPATH**/ ?>