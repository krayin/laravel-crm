@aware(['product' => null])

<div class="box-shadow rounded-lg border border-gray-200 bg-white p-4 dark:border-gray-800 dark:bg-gray-900">
    <p class="mb-4 text-base font-semibold text-gray-800 dark:text-white">
        @lang('admin::app.products.create.tabs.media')
    </p>

    {!! view_render_event('admin.products.tabs.media.before', ['product' => $product]) !!}

    {{-- Product Images Section --}}
    <div class="mb-6">
        <h4 class="mb-4 text-sm font-semibold text-gray-800 dark:text-white">
            @lang('admin::app.products.create.product_images')
        </h4>

        <div class="image-upload-area border-2 border-dashed border-gray-300 dark:border-gray-600 rounded-lg p-6 text-center">
            <div id="image-gallery" class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-4">
                {{-- Existing images will be rendered here --}}
                @if($product && $product->images)
                    @foreach($product->images as $index => $image)
                        <div class="relative group image-item" data-index="{{ $index }}">
                            <img src="{{ asset('storage/' . $image) }}" 
                                 alt="Product Image {{ $index + 1 }}" 
                                 class="w-full h-32 object-cover rounded-lg">
                            
                            <div class="absolute inset-0 bg-black bg-opacity-50 opacity-0 group-hover:opacity-100 transition-opacity rounded-lg flex items-center justify-center">
                                <div class="flex space-x-2">
                                    <button type="button" class="text-white hover:text-yellow-400" onclick="setMainImage(this)">
                                        <i class="fas fa-star"></i>
                                    </button>
                                    <button type="button" class="text-white hover:text-blue-400" onclick="editImage(this)">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button type="button" class="text-white hover:text-red-400" onclick="removeImage(this)">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </div>
                            
                            @if($index === 0)
                                <div class="absolute top-2 left-2 bg-yellow-500 text-white text-xs px-2 py-1 rounded">
                                    Main
                                </div>
                            @endif
                        </div>
                    @endforeach
                @endif
            </div>

            <div class="upload-placeholder">
                <div class="flex flex-col items-center">
                    <i class="fas fa-cloud-upload-alt text-4xl text-gray-400 mb-2"></i>
                    <p class="text-gray-600 dark:text-gray-400 mb-2">
                        @lang('admin::app.products.create.image_upload_hint')
                    </p>
                    <button type="button" 
                            onclick="document.getElementById('product-images').click()" 
                            class="secondary-button">
                        @lang('admin::app.products.create.browse_images')
                    </button>
                </div>
            </div>

            <input type="file" 
                   id="product-images" 
                   name="images[]" 
                   multiple 
                   accept="image/*" 
                   class="hidden" 
                   onchange="handleImageUpload(this)">
        </div>

        <p class="mt-2 text-xs text-gray-500 dark:text-gray-400">
            @lang('admin::app.products.create.image_upload_help')
        </p>
    </div>

    {{-- Product Documents Section --}}
    <div class="mb-6">
        <h4 class="mb-4 text-sm font-semibold text-gray-800 dark:text-white">
            @lang('admin::app.products.create.product_documents')
        </h4>

        <div class="document-upload-area border-2 border-dashed border-gray-300 dark:border-gray-600 rounded-lg p-6">
            <div id="document-list" class="mb-4">
                {{-- Existing documents will be rendered here --}}
                @if($product && $product->documents)
                    @foreach($product->documents as $document)
                        <div class="flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-800 rounded-lg mb-2">
                            <div class="flex items-center">
                                <i class="fas fa-file-alt text-gray-600 mr-3"></i>
                                <span class="text-sm text-gray-800 dark:text-gray-200">{{ $document->name }}</span>
                                <span class="text-xs text-gray-500 ml-2">({{ $document->size }})</span>
                            </div>
                            <button type="button" 
                                    class="text-red-600 hover:text-red-800" 
                                    onclick="removeDocument('{{ $document->id }}')">
                                <i class="fas fa-trash text-sm"></i>
                            </button>
                        </div>
                    @endforeach
                @endif
            </div>

            <div class="text-center">
                <div class="flex flex-col items-center">
                    <i class="fas fa-file-upload text-3xl text-gray-400 mb-2"></i>
                    <p class="text-gray-600 dark:text-gray-400 mb-2">
                        @lang('admin::app.products.create.document_upload_hint')
                    </p>
                    <button type="button" 
                            onclick="document.getElementById('product-documents').click()" 
                            class="secondary-button">
                        @lang('admin::app.products.create.browse_documents')
                    </button>
                </div>
            </div>

            <input type="file" 
                   id="product-documents" 
                   name="documents[]" 
                   multiple 
                   accept=".pdf,.doc,.docx,.xls,.xlsx,.txt" 
                   class="hidden" 
                   onchange="handleDocumentUpload(this)">
        </div>

        <p class="mt-2 text-xs text-gray-500 dark:text-gray-400">
            @lang('admin::app.products.create.document_upload_help')
        </p>
    </div>

    {{-- Custom Attributes Section --}}
    <div class="mb-6">
        <h4 class="mb-4 text-sm font-semibold text-gray-800 dark:text-white">
            @lang('admin::app.products.create.additional_attributes')
        </h4>

        {{-- This will render dynamic attributes from the attribute system --}}
        <x-admin::attributes
            :custom-attributes="app('Webkul\Attribute\Repositories\AttributeRepository')->findWhere([
                'entity_type' => 'products',
                ['code', 'NOTIN', ['name', 'sku', 'description', 'price', 'quantity']],
            ])"
            :entity="$product"
        />
    </div>

    {{-- Rich Text Content Section --}}
    <div class="mb-6">
        <h4 class="mb-4 text-sm font-semibold text-gray-800 dark:text-white">
            @lang('admin::app.products.create.detailed_description')
        </h4>

        {{-- Rich Text Editor --}}
        <x-admin::form.control-group class="w-full">
            <x-admin::form.control-group.label>
                @lang('admin::app.products.create.full_description')
            </x-admin::form.control-group.label>

            <x-admin::tinymce>
                <x-admin::form.control-group.control
                    type="textarea"
                    name="full_description"
                    :value="old('full_description', $product->full_description ?? '')"
                    :label="trans('admin::app.products.create.full_description')"
                    :placeholder="trans('admin::app.products.create.full_description_placeholder')"
                    rows="8"
                />
            </x-admin::tinymce>

            <x-admin::form.control-group.error control-name="full_description" />
        </x-admin::form.control-group>
    </div>

    {{-- Video Section --}}
    <div class="mb-6">
        <h4 class="mb-4 text-sm font-semibold text-gray-800 dark:text-white">
            @lang('admin::app.products.create.product_videos')
        </h4>

        <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
            {{-- YouTube Video URL --}}
            <x-admin::form.control-group class="w-full">
                <x-admin::form.control-group.label>
                    @lang('admin::app.products.create.youtube_url')
                </x-admin::form.control-group.label>

                <x-admin::form.control-group.control
                    type="url"
                    name="youtube_url"
                    :value="old('youtube_url', $product->youtube_url ?? '')"
                    :label="trans('admin::app.products.create.youtube_url')"
                    :placeholder="trans('admin::app.products.create.youtube_url_placeholder')"
                />

                <x-admin::form.control-group.error control-name="youtube_url" />
            </x-admin::form.control-group>

            {{-- Vimeo Video URL --}}
            <x-admin::form.control-group class="w-full">
                <x-admin::form.control-group.label>
                    @lang('admin::app.products.create.vimeo_url')
                </x-admin::form.control-group.label>

                <x-admin::form.control-group.control
                    type="url"
                    name="vimeo_url"
                    :value="old('vimeo_url', $product->vimeo_url ?? '')"
                    :label="trans('admin::app.products.create.vimeo_url')"
                    :placeholder="trans('admin::app.products.create.vimeo_url_placeholder')"
                />

                <x-admin::form.control-group.error control-name="vimeo_url" />
            </x-admin::form.control-group>
        </div>
    </div>

    {!! view_render_event('admin.products.tabs.media.after', ['product' => $product]) !!}
</div>

@pushOnce('scripts')
<script>
    function handleImageUpload(input) {
        const files = input.files;
        const gallery = document.getElementById('image-gallery');
        
        for (let i = 0; i < files.length; i++) {
            const file = files[i];
            const reader = new FileReader();
            
            reader.onload = function(e) {
                const imageDiv = document.createElement('div');
                imageDiv.className = 'relative group image-item';
                imageDiv.innerHTML = `
                    <img src="${e.target.result}" alt="New Image" class="w-full h-32 object-cover rounded-lg">
                    <div class="absolute inset-0 bg-black bg-opacity-50 opacity-0 group-hover:opacity-100 transition-opacity rounded-lg flex items-center justify-center">
                        <div class="flex space-x-2">
                            <button type="button" class="text-white hover:text-yellow-400" onclick="setMainImage(this)">
                                <i class="fas fa-star"></i>
                            </button>
                            <button type="button" class="text-white hover:text-red-400" onclick="removeImage(this)">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </div>
                `;
                gallery.appendChild(imageDiv);
            };
            
            reader.readAsDataURL(file);
        }
    }

    function handleDocumentUpload(input) {
        const files = input.files;
        const documentList = document.getElementById('document-list');
        
        for (let i = 0; i < files.length; i++) {
            const file = files[i];
            const documentDiv = document.createElement('div');
            documentDiv.className = 'flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-800 rounded-lg mb-2';
            documentDiv.innerHTML = `
                <div class="flex items-center">
                    <i class="fas fa-file-alt text-gray-600 mr-3"></i>
                    <span class="text-sm text-gray-800 dark:text-gray-200">${file.name}</span>
                    <span class="text-xs text-gray-500 ml-2">(${formatFileSize(file.size)})</span>
                </div>
                <button type="button" class="text-red-600 hover:text-red-800" onclick="removeDocument(this)">
                    <i class="fas fa-trash text-sm"></i>
                </button>
            `;
            documentList.appendChild(documentDiv);
        }
    }

    function setMainImage(element) {
        // Remove main badge from all images
        document.querySelectorAll('.image-item .bg-yellow-500').forEach(badge => badge.remove());
        
        // Add main badge to clicked image
        const imageItem = element.closest('.image-item');
        const mainBadge = document.createElement('div');
        mainBadge.className = 'absolute top-2 left-2 bg-yellow-500 text-white text-xs px-2 py-1 rounded';
        mainBadge.textContent = 'Main';
        imageItem.appendChild(mainBadge);
    }

    function removeImage(element) {
        const imageItem = element.closest('.image-item');
        imageItem.remove();
    }

    function removeDocument(element) {
        const documentItem = element.closest('.flex');
        documentItem.remove();
    }

    function formatFileSize(bytes) {
        if (bytes === 0) return '0 Bytes';
        const k = 1024;
        const sizes = ['Bytes', 'KB', 'MB', 'GB'];
        const i = Math.floor(Math.log(bytes) / Math.log(k));
        return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
    }
</script>
@endPushOnce