<v-flash-group ref='flashes'></v-flash-group>

<?php if (! $__env->hasRenderedOnce('12748298-117b-4941-81b1-d93ed87fecfd')): $__env->markAsRenderedOnce('12748298-117b-4941-81b1-d93ed87fecfd');
$__env->startPush('scripts'); ?>
    <script
        type="text/x-template"
        id="v-flash-group-template"
    >
        <transition-group
            tag='div'
            name="flash-group"
            enter-from-class="ltr:translate-y-full rtl:-translate-y-full"
            enter-active-class="transform transition duration-300 ease-[cubic-bezier(.4,0,.2,1)]"
            enter-to-class="ltr:translate-y-0 rtl:-translate-y-0"
            leave-from-class="ltr:translate-y-0 rtl:-translate-y-0"
            leave-active-class="transform transition duration-300 ease-[cubic-bezier(.4,0,.2,1)]"
            leave-to-class="ltr:translate-y-full rtl:-translate-y-full"
            class='fixed bottom-5 left-1/2 z-[10003] grid -translate-x-1/2 justify-items-end gap-2.5'
        >
            <?php if (isset($component)) { $__componentOriginal61c07debb335a63a383982eca0b85db9 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal61c07debb335a63a383982eca0b85db9 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'admin::components.flash-group.item','data' => []] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('admin::flash-group.item'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(Illuminate\View\AnonymousComponent::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal61c07debb335a63a383982eca0b85db9)): ?>
<?php $attributes = $__attributesOriginal61c07debb335a63a383982eca0b85db9; ?>
<?php unset($__attributesOriginal61c07debb335a63a383982eca0b85db9); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal61c07debb335a63a383982eca0b85db9)): ?>
<?php $component = $__componentOriginal61c07debb335a63a383982eca0b85db9; ?>
<?php unset($__componentOriginal61c07debb335a63a383982eca0b85db9); ?>
<?php endif; ?>
        </transition-group>
    </script>

    <script type="module">
        app.component('v-flash-group', {
            template: '#v-flash-group-template',

            data() {
                return {
                    uid: 0,

                    flashes: []
                }
            },

            created() {
                <?php $__currentLoopData = ['success', 'warning', 'error', 'info']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <?php if(session()->has($key)): ?>
                        this.flashes.push({'type': '<?php echo e($key); ?>', 'message': "<?php echo e(session($key)); ?>", 'uid':  this.uid++});
                    <?php endif; ?>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

                this.registerGlobalEvents();
            },

            methods: {
                add(flash) {
                    flash.uid = this.uid++;

                    this.flashes.push(flash);
                },

                remove(flash) {
                    let index = this.flashes.indexOf(flash);

                    this.flashes.splice(index, 1);
                },

                registerGlobalEvents() {
                    this.$emitter.on('add-flash', this.add);
                },
            }
        });
    </script>
<?php $__env->stopPush(); endif; ?><?php /**PATH C:\laragon\www\adv-crm\packages\Webkul\Admin\src/resources/views/components/flash-group/index.blade.php ENDPATH**/ ?>