@props([
    'name'  => '',
    'value' => '',
])

<v-inline-edit
    name="{{ $name }}"
    value="{{ $value }}"
></v-inline-edit>

@pushOnce('scripts')
    <script
        type="text/x-template"
        id="v-inline-edit-template"
    >
        <div class="flex items-end justify-end w-full max-w-full group hover:bg-gray-50 hover:rounded-sm">
            <!-- Non-editing view -->
            <div
                v-if="!isEditing"
                class="flex items-center justify-center rounded-xs h-[38px] p-1 pl-3 space-x-2 cursor-pointer"
            >
                <span class="font-normal text-sm">@{{ inputValue }}</span>
        
                <i
                    @click="isEditing = true"
                    class="icon-edit hidden text-xl ml-2 group-hover:block"
                ></i>
            </div>
        
            <!-- Editing view -->
            <div v-else class="relative flex items-center w-full">
                <x-admin::form.control-group.control
                    type="text"
                    ::id="name"
                    ::name="name"
                    rules="required"
                    class="px-2 py-1 pr-16 text-right text-normal "
                    v-model="inputValue"
                    @blur="isEditing = false"
                />


                <div
                    class="absolute right-2 top-1/2 transform -translate-y-1/2 flex space-x-1 bg-white"
                >
                    <button
                        @click="isEditing = false"
                        class="flex items-center justify-center rounded-l-sm p-1 bg-green-100 hover:bg-green-200"
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
                        @click="isEditing = false"
                        class="flex items-center justify-center rounded-r-sm p-1 ml-[1px] bg-red-100 hover:bg-red-200"
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
            props: {
                name: {
                    type: String,
                    required: true,
                },
                value: {
                    type: String,
                    required: true,
                }
            },

            data() {
                return {
                    inputValue: this.value,

                    isEditing: false,
                }
            },
        });
    </script>
@endPushOnce