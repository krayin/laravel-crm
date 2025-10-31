@props([
    'title'      => '',
    'isSelected' => false,
])

<v-tab-item
    title="{{ $title }}"
    is-selected="{{ $isSelected }}"
    {{ $attributes->merge(['class' => 'p-4']) }}
>
    <template v-slot>
        {{ $slot }}
    </template>
</v-tab-item>

@pushOnce('styles')
<style>
    /* Smooth transitions for tab content */
    .tab-content {
        transition: opacity 0.3s ease-in-out;
    }
    
    /* Ensure form elements maintain their state */
    .tab-content input, 
    .tab-content textarea, 
    .tab-content select {
        transition: none; /* Prevent form field transitions that might interfere */
    }
</style>
@endPushOnce

@pushOnce('scripts')
    <script 
        type="text/x-template" 
        id="v-tab-item-template"
    >
        <div
            :style="{ display: isActive ? 'block' : 'none' }"
            class="tab-content animate-[on-fade_0.3s_ease-in-out]"
        >
            <slot></slot>
        </div>
    </script>

    <script type="module">
        app.component('v-tab-item', {
            template: '#v-tab-item-template',

            props: ['title', 'isSelected'],

            data() {
                return {
                    isActive: false
                }
            },

            mounted() {
                this.isActive = this.isSelected;

                /**
                 * On mounted, pushing element to its parents component.
                 */
                this.$parent.$data.tabs.push(this);
            }
        });
    </script>
@endPushOnce
