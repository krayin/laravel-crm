@props([
    'name'          => null,
    'allowMultiple' => false,
])

<v-attachment
    name="{{ $name }}"
    :allow-multiple="{{ $allowMultiple }}"
></v-attachment>

@pushOnce('scripts')
    <script
        type="text/x-template"
        id="v-attachment-template"
    >
        <div class="w-full max-w-md mx-auto">
            <!-- Attachment List -->
            <div v-if="attachments.length" class="flex flex-wrap gap-2">
                <div 
                    v-for="(file, index) in attachments" 
                    :key="index" 
                    class="flex items-center bg-blue-100 text-blue-700 rounded-full px-4 py-1 text-sm"
                >
                    <span class="mr-2 truncate max-w-xs">@{{ file.name }}</span>

                    <button @click="removeAttachment(index)">
                        <svg 
                            xmlns="http://www.w3.org/2000/svg"
                            class="h-4 w-4 text-red-500"
                            fill="none"
                            viewBox="0 0 24 24"
                            stroke="currentColor"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M6 18L18 6M6 6l12 12"
                            />
                        </svg>
                    </button>
                </div>
            </div>

            <!-- File Attachment Input -->
            <div class="mb-4">
                <div class="relative flex items-center border-gray-300 rounded-lg pt-4 hover:border-gray-400 cursor-pointer">
                    <input 
                        id="file-upload" 
                        type="file" 
                        :name="name"
                        class="absolute inset-0 opacity-0 cursor-pointer" 
                        @change="handleFileUpload" 
                        ref="fileInput"
                        :multiple="allowMultiple"
                    />

                    <i class="icon-attachmetent text-2xl"></i>

                    <span class="text-gray-800 text-sm my-2">@lang('Click to attach files')</span>
                </div>
            </div>
        
        </div>
    </script>

    <script type="module">
        app.component('v-attachment', {
            template: '#v-attachment-template',

            props: {
                name: {
                    type: String,
                    default: 'attachments[]',
                },

                allowMultiple: {
                    type: Boolean,
                    default: false,
                },
            },

            data() {
                return {
                    attachments: [],
                };
            },

            methods: {
                /**
                 * Handle file upload event.
                 * 
                 * @param {Event} event
                 * @return {void}
                 */
                handleFileUpload(event) {
                    const files = Array.from(event.target.files);

                    if (this.allowMultiple) {
                        this.attachments.push(...files);
                    } else {
                        this.attachments = [files[0]];
                    }

                    this.updateFileInput();
                },

                /**
                 * Remove attachment from the list.
                 * 
                 * @param {Number} index
                 * @return {void}
                 */
                removeAttachment(index) {
                    this.attachments.splice(index, 1);

                    this.updateFileInput();
                },

                /**
                 * Update file input with the selected files.
                 * @return {void}
                 */
                updateFileInput() {
                    const dataTransfer = new DataTransfer();

                    this.attachments.forEach(file => dataTransfer.items.add(file));

                    this.$refs.fileInput.files = dataTransfer.files;
                },
            },
        });
    </script>
@endPushOnce