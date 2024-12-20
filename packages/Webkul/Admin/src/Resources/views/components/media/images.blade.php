@props([
    'name'             => 'images',
    'allowMultiple'    => false,
    'showPlaceholders' => false,
    'uploadedImages'   => [],
    'width'            => '120px',
    'height'           => '120px'
])

<v-media-images
    name="{{ $name }}"
    v-bind:allow-multiple="{{ $allowMultiple ? 'true' : 'false' }}"
    v-bind:show-placeholders="{{ $showPlaceholders ? 'true' : 'false' }}"
    :uploaded-images='{{ json_encode($uploadedImages) }}'
    width="{{ $width }}"
    height="{{ $height }}"
    :errors="errors"
>
    <x-admin::shimmer.image class="h-[120px] w-[120px] rounded" />
</v-media-images>

@pushOnce('scripts')
    <script
        type="text/x-template"
        id="v-media-images-template"
    >
        <!-- Panel Content -->
        <div class="grid">
            <div class="flex flex-wrap gap-1">
                <!-- Upload Image Button -->
                <template v-if="allowMultiple || images.length == 0">
                    <!-- Upload Image Button -->
                    <label
                        class="grid h-[120px] max-h-[120px] min-h-[110px] w-full min-w-[110px] max-w-[120px] cursor-pointer items-center justify-items-center rounded border border-dashed border-gray-300 transition-all hover:border-gray-400 dark:border-gray-800 dark:mix-blend-exclusion dark:invert"
                        :class="[(errors?.['images.files[0]'] ?? false) ? 'border border-red-500' : 'border-gray-300']"
                        :style="{'max-width': this.width, 'max-height': this.height}"
                        :for="$.uid + '_imageInput'"
                    >
                        <div class="flex flex-col items-center">
                            <span class="icon-image text-2xl"></span>

                            <p class="grid text-center text-sm font-semibold text-gray-600 dark:text-gray-300">
                                @lang('admin::app.components.media.images.add-image-btn')
                                
                                <span class="text-xs">
                                    @lang('admin::app.components.media.images.allowed-types')
                                </span>
                            </p>

                            <input
                                type="file"
                                class="hidden"
                                :id="$.uid + '_imageInput'"
                                accept="image/*"
                                :multiple="allowMultiple"
                                :ref="$.uid + '_imageInput'"
                                @change="add"
                            />
                        </div>
                    </label>
                </template>

                <!-- Uploaded Images -->
                <draggable
                    class="flex flex-wrap gap-1"
                    ghost-class="draggable-ghost"
                    v-bind="{animation: 200}"
                    :list="images"
                    item-key="id"
                >
                    <template #item="{ element, index }">
                        <v-media-image-item
                            :name="name"
                            :index="index"
                            :image="element"
                            :width="width"
                            :height="height"
                            @onRemove="remove($event)"
                        >
                        </v-media-image-item>
                    </template>
                </draggable>

                <!-- Placeholders -->
                <template v-if="showPlaceholders && ! images.length">
                    <!-- Front Placeholder -->
                    <div
                        class="relative h-[120px] max-h-[120px] w-full min-w-[120px] max-w-[120px] rounded border border-dashed border-gray-300 dark:border-gray-800 dark:mix-blend-exclusion dark:invert"
                        v-for="placeholder in placeholders"
                    >
                        <img :src="placeholder.image">

                        <p class="absolute bottom-4 w-full text-center text-xs font-semibold text-gray-400">
                            @{{ placeholder.label }}
                        </p>
                    </div>
                </template>
            </div>
        </div>  
    </script>

    <script type="text/x-template" id="v-media-image-item-template">
        <div class="group relative grid max-h-[120px] min-w-[120px] justify-items-center overflow-hidden rounded transition-all hover:border-gray-400">
            <!-- Image Preview -->
            <img
                :src="image.url"
                :style="{'width': this.width, 'height': this.height}"
            />

            <div class="invisible absolute bottom-0 top-0 flex w-full flex-col justify-between bg-white p-3 opacity-80 transition-all group-hover:visible dark:bg-gray-900">
                <!-- Image Name -->
                <p class="break-all text-xs font-semibold text-gray-600 dark:text-gray-300"></p>

                <!-- Actions -->
                <div class="flex justify-between">
                    <span
                        class="icon-delete cursor-pointer rounded-md p-1.5 text-2xl hover:bg-gray-200 dark:hover:bg-gray-800"
                        @click="remove"
                    ></span>

                    <label
                        class="icon-edit cursor-pointer rounded-md p-1.5 text-2xl hover:bg-gray-200 dark:hover:bg-gray-800"
                        :for="$.uid + '_imageInput_' + index"
                    ></label>

                    <input
                        type="hidden"
                        :name="name + '[' + image.id + ']'"
                        v-if="! image.is_new"
                    />

                    <input
                        type="file"
                        :name="name + '[]'"
                        class="hidden"
                        accept="image/*"
                        :id="$.uid + '_imageInput_' + index"
                        :ref="$.uid + '_imageInput_' + index"
                        @change="edit"
                    />
                </div>
            </div>
        </div>
    </script>

    <script type="module">
        app.component('v-media-images', {
            template: '#v-media-images-template',

            props: {
                name: {
                    type: String, 
                    default: 'images',
                },

                allowMultiple: {
                    type: Boolean,
                    default: false,
                },

                showPlaceholders: {
                    type: Boolean,
                    default: false,
                },

                uploadedImages: {
                    type: Array,
                    default: () => []
                },

                width: {
                    type: String,
                    default: '120px'
                },

                height: {
                    type: String,
                    default: '120px'
                },

                errors: {
                    type: Object,
                    default: () => {}
                }
            },

            data() {
                return {
                    images: [],

                    placeholders: [
                        {
                            label: "@lang('admin::app.components.media.images.placeholders.front')",
                            image: "{{ asset('images/product-placeholders/front.svg') }}"
                        }, {
                            label: "@lang('admin::app.components.media.images.placeholders.next')",
                            image: "{{ asset('images/product-placeholders/next-1.svg') }}"
                        }, {
                            label: "@lang('admin::app.components.media.images.placeholders.next')",
                            image: "{{ asset('images/product-placeholders/next-2.svg') }}"
                        }, {
                            label: "@lang('admin::app.components.media.images.placeholders.zoom')",
                            image: "{{ asset('images/product-placeholders/zoom.svg') }}"
                        }, {
                            label: "@lang('admin::app.components.media.images.placeholders.use-cases')",
                            image: "{{ asset('images/product-placeholders/use-cases.svg') }}"
                        }, {
                            label: "@lang('admin::app.components.media.images.placeholders.size')",
                            image: "{{ asset('images/product-placeholders/size.svg') }}"
                        }
                    ],

                    isLoading: false,
                }
            },

            mounted() {
                this.images = this.uploadedImages;
            },

            methods: {
                add() {
                    let imageInput = this.$refs[this.$.uid + '_imageInput'];

                    if (imageInput.files == undefined) {
                        return;
                    }

                    const validFiles = Array.from(imageInput.files).every(file => file.type.includes('image/'));

                    if (! validFiles) {
                        this.$emitter.emit('add-flash', {
                            type: 'warning',
                            message: "@lang('admin::app.components.media.images.not-allowed-error')"
                        });

                        return;
                    }

                    imageInput.files.forEach((file, index) => {
                        this.images.push({
                            id: 'image_' + this.images.length,
                            url: '',
                            file: file
                        });
                    });
                },

                remove(image) {
                    let index = this.images.indexOf(image);

                    this.images.splice(index, 1);
                },

                getBase64ToFile(base64, filename) {
                    var arr = base64.split(','),
                        mime = arr[0].match(/:(.*?);/)[1],
                        bstr = atob(arr[arr.length - 1]), 
                        n = bstr.length, 
                        u8arr = new Uint8Array(n);

                    while (n--) {
                        u8arr[n] = bstr.charCodeAt(n);
                    }

                    return new File([u8arr], filename, {type:mime});
                },
            }
        });

        app.component('v-media-image-item', {
            template: '#v-media-image-item-template',

            props: ['index', 'image', 'name', 'width', 'height'],

            mounted() {
                if (this.image.file instanceof File) {
                    this.setFile(this.image.file);

                    this.readFile(this.image.file);
                }
            },

            methods: {
                edit() {
                    let imageInput = this.$refs[this.$.uid + '_imageInput_' + this.index];

                    if (imageInput.files == undefined) {
                        return;
                    }

                    const validFiles = Array.from(imageInput.files).every(file => file.type.includes('image/'));

                    if (! validFiles) {
                        this.$emitter.emit('add-flash', {
                            type: 'warning',
                            message: "@lang('admin::app.components.media.images.not-allowed-error')"
                        });

                        return;
                    }

                    this.setFile(imageInput.files[0]);

                    this.readFile(imageInput.files[0]);
                },

                remove() {
                    this.$emit('onRemove', this.image)
                },

                setFile(file) {
                    this.image.is_new = 1;

                    const dataTransfer = new DataTransfer();

                    dataTransfer.items.add(file);

                    this.$refs[this.$.uid + '_imageInput_' + this.index].files = dataTransfer.files;
                },

                readFile(file) {
                    let reader = new FileReader();

                    reader.onload = (e) => {
                        this.image.url = e.target.result;
                    }

                    reader.readAsDataURL(file);
                },
            }
        });
    </script>
@endPushOnce