<v-flash-group ref='flashes'></v-flash-group>

@pushOnce('scripts')
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
            <x-admin::flash-group.item />
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
                @foreach (['success', 'warning', 'error', 'info'] as $key)
                    @if (session()->has($key))
                        this.flashes.push({'type': '{{ $key }}', 'message': "{{ session($key) }}", 'uid':  this.uid++});
                    @endif
                @endforeach

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
@endpushOnce