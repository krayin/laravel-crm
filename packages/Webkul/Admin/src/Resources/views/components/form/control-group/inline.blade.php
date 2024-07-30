@props([
    'name'  => '',
    'value' => '',
])

<v-inline-edit
    name="{{ $name }}"
    value="{{ $value }}"
     {{ $attributes }}
></v-inline-edit>

@pushOnce('scripts')
    <script
        type="text/x-template"
        id="v-inline-edit-template"
    >
        <div class="group w-full max-w-full hover:bg-gray-50 hover:rounded-sm">
            <!-- Non-editing view -->
            <div
                v-if="! isEditing"
                class="flex items-center rounded-xs h-[38px] space-x-2 cursor-pointer"
                :style="textPositionStyle"
            >
                <x-admin::form.control-group.control
                    type="hidden"
                    ::id="name"
                    ::name="name"
                    v-model="inputValue"
                />

                <span class="font-normal text-sm pl-[2px]">@{{ inputValue }}</span>
        
                <i
                    @click="isEditing = true"
                    class="icon-edit hidden text-xl pr-2 group-hover:block"
                ></i>
            </div>
        
            <!-- Editing view -->
            <div v-else class="relative flex items-center w-full">
                <x-admin::form.control-group.control
                    type="text"
                    ::id="name"
                    ::name="name"
                    class="px-2 py-1 pr-16 text-normal"
                    ::style="inputPositionStyle"
                    v-model="inputValue"
                />

                <div
                    class="absolute right-2 top-1/2 transform -translate-y-1/2 flex gap-[1px] bg-white"
                >
                    <button
                        @click="save"
                        class="flex items-center justify-center rounded-l-md p-1 bg-green-100 hover:bg-green-200"
                    >
                        <svg
                            xmlns="http://www.w3.org/2000/svg"
                            fill="none"
                            viewBox="0 0 24 24"
                            stroke-width="2"
                            stroke="currentColor"
                            class="w-4 h-4 text-green-600"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                d="M5 13l4 4L19 7"
                            />
                        </svg>
                    </button>
                
                    <button
                        @click="cancel"
                        class="flex items-center justify-center rounded-r-md p-1 ml-[1px] bg-red-100 hover:bg-red-200"
                    >
                        <svg
                            xmlns="http://www.w3.org/2000/svg"
                            fill="none"
                            viewBox="0 0 24 24"
                            stroke-width="2"
                            stroke="currentColor"
                            class="w-4 h-4 text-red-600"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                d="M6 18L18 6M6 6l12 12"
                            />
                        </svg>
                    </button>
                </div>
            </div>
        </div>
    </script>

    <script type="module">
        app.component('v-inline-edit', {
            template: '#v-inline-edit-template',

            emits: ['on-change', 'on-cancelled'],

            props: {
                name: {
                    type: String,
                    required: true,
                },
                value: {
                    type: String,
                    required: true,
                },
                position: {
                    type: String,
                    default: 'left',
                },
            },

            data() {
                return {
                    inputValue: this.value,

                    isEditing: false,
                };
            },

            computed: {
                /**
                 * Get the input position style.
                 * 
                 * @return {String}
                 */
                inputPositionStyle() {
                    return this.position === 'left' ? 'text-align: left' : 'text-align: right';
                },

                /**
                 * Get the text position style.
                 * 
                 * @return {String}
                 */
                textPositionStyle() {
                    return this.position === 'left' ? 'justify-content: space-between' : 'justify-content: end';
                },
            },

            methods: {
                /**
                 * Save the input value.
                 * 
                 * @return {void}
                 */
                save() {
                    this.isEditing = false;

                    this.$emit('on-change', this.inputValue);
                },

                /**
                 * Cancel the input value.
                 * 
                 * @return {void}
                 */
                cancel() {
                    this.inputValue = this.value;

                    this.isEditing = false;

                    this.$emit('on-cancelled', this.inputValue);
                },
            },
        });
    </script>
@endPushOnce