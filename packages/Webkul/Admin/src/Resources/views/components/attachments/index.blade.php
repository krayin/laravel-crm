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
            <div
                v-if="attachments.length"
                class="flex flex-wrap gap-2"
            >
                <div 
                    v-for="(file, index) in attachments" 
                    :key="index" 
                    class="flex gap-2 items-center justify-center bg-gray-100 rounded-md px-4 py-2"
                >
                    <div class="flex gap-1 items-center justify-center">
                        <span class="mr-2 truncate max-w-xs">@{{ file.name }}</span>

                        <i 
                            @click="removeAttachment(index)"
                            class="icon-cross-large !font-semibold cursor-pointer"
                        ></i>
                    </div>
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

                    <div class="flex gap-2 items-center justify-center">
                        <i class="icon-attachmetent text-xl font-medium"></i>

                        <span class="text-base font-medium text-gray-800 my-2">@lang('Add Attachments')</span>
                    </div>
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
                 *
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