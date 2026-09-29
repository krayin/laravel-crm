@props([
    'lead',
])

{!! view_render_event('admin.leads.view.google_drive_files.before', ['lead' => $lead]) !!}

<!-- Google Drive Files Vue Component -->
<v-google-drive-files
    lead-id="{{ $lead->id }}"
    :has-drive-folder="{{ $lead->google_drive_folder_id ? 'true' : 'false' }}"
    drive-folder-url="{{ $lead->google_drive_folder_url ?? '' }}"
    ref="googleDriveFiles"
>
    <!-- Shimmer/Loading -->
    <div class="flex items-center justify-center py-12">
        <div class="flex flex-col items-center gap-2">
            <div class="h-8 w-8 animate-spin rounded-full border-4 border-gray-200 border-t-blue-600"></div>
            <p class="text-sm text-gray-600 dark:text-gray-400">Loading files...</p>
        </div>
    </div>
</v-google-drive-files>

{!! view_render_event('admin.leads.view.google_drive_files.after', ['lead' => $lead]) !!}

@pushOnce('scripts')
    <script type="text/x-template" id="v-google-drive-files-template">
        <div class="w-full">
            <!-- No Drive Folder Message -->
            <div v-if="!hasDriveFolder" class="flex flex-col items-center justify-center py-12">
                <span class="icon-google-drive text-6xl text-gray-300 dark:text-gray-600"></span>
                <p class="mt-4 text-gray-600 dark:text-gray-400">
                    Google Drive folder not created yet
                </p>
                <p class="text-sm text-gray-500 dark:text-gray-500">
                    Create a Drive folder from the left panel to see files here
                </p>
            </div>

            <!-- Files List -->
            <div v-else>
                <!-- Loading State -->
                <div v-if="isLoading" class="flex items-center justify-center py-12">
                    <div class="flex flex-col items-center gap-2">
                        <div class="h-8 w-8 animate-spin rounded-full border-4 border-gray-200 border-t-blue-600"></div>
                        <p class="text-sm text-gray-600 dark:text-gray-400">Loading files from Google Drive...</p>
                    </div>
                </div>

                <!-- Files Grid -->
                <div v-else-if="files.length > 0" class="p-4">
                    <!-- Header -->
                    <div class="mb-4 flex items-center justify-between border-b border-gray-200 pb-4 dark:border-gray-700">
                        <h3 class="text-lg font-semibold dark:text-white">
                            Google Drive Files (@{{ files.length }})
                        </h3>
                        <div class="flex gap-2">
                            <button
                                @click="loadFiles"
                                class="inline-flex items-center gap-2 rounded bg-blue-600 px-3 py-1.5 text-sm text-white hover:bg-blue-700"
                            >
                                <span class="icon-refresh"></span>
                                Refresh
                            </button>
                            <a 
                                :href="driveFolderUrl" 
                                target="_blank"
                                class="inline-flex items-center gap-2 rounded bg-gray-100 px-3 py-1.5 text-sm text-gray-700 hover:bg-gray-200 dark:bg-gray-800 dark:text-gray-300"
                            >
                                <span class="icon-external-link"></span>
                                Open in Drive
                            </a>
                        </div>
                    </div>

                    <!-- Files List -->
                    <div class="divide-y divide-gray-200 dark:divide-gray-700">
                        <div
                            v-for="(file, index) in files"
                            :key="file.id"
                            class="flex items-center justify-between py-3"
                        >
                            <div class="flex items-center gap-3 flex-1">
                                <span :class="getFileIcon(file.mimeType)" class="text-2xl text-gray-400"></span>
                                <div class="flex-1">
                                    <p class="font-medium dark:text-white">@{{ file.name }}</p>
                                    <p class="text-xs text-gray-500 dark:text-gray-400">
                                        @{{ formatFileSize(file.size) }} • Modified @{{ formatDate(file.modifiedTime) }}
                                    </p>
                                </div>
                            </div>
                            <a 
                                :href="file.url" 
                                target="_blank"
                                class="rounded px-3 py-1.5 text-sm text-blue-600 hover:bg-blue-50 dark:text-blue-400 dark:hover:bg-gray-800"
                            >
                                Open
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Empty State -->
                <div v-else class="flex flex-col items-center justify-center py-12">
                    <span class="icon-file text-6xl text-gray-300 dark:text-gray-600"></span>
                    <p class="mt-4 text-gray-600 dark:text-gray-400">
                        No files in this folder yet
                    </p>
                    <p class="text-sm text-gray-500 dark:text-gray-500">
                        Upload files to Google Drive to see them here
                    </p>
                    <a 
                        :href="driveFolderUrl" 
                        target="_blank"
                        class="mt-4 inline-flex items-center gap-2 rounded bg-blue-600 px-4 py-2 text-sm text-white hover:bg-blue-700"
                    >
                        <span class="icon-external-link"></span>
                        Open Folder in Google Drive
                    </a>
                </div>
            </div>
        </div>
    </script>

    <script type="module">
        app.component('v-google-drive-files', {
            template: '#v-google-drive-files-template',

            props: {
                leadId: {
                    type: String,
                    required: true,
                },

                hasDriveFolder: {
                    type: Boolean,
                    default: false,
                },

                driveFolderUrl: {
                    type: String,
                    default: '',
                },
            },

            data() {
                return {
                    isLoading: false,
                    files: [],
                }
            },

            mounted() {
                console.log('🚀 Vue component mounted', {
                    leadId: this.leadId,
                    hasDriveFolder: this.hasDriveFolder,
                    driveFolderUrl: this.driveFolderUrl
                });
                
                if (this.hasDriveFolder) {
                    console.log('✅ Has drive folder, loading files...');
                    this.loadFiles();
                } else {
                    console.log('❌ No drive folder');
                }
            },

            methods: {
                loadFiles() {
                    console.log('📂 Loading files for lead:', this.leadId);
                    this.isLoading = true;

                    this.$axios.get(`/admin/leads/${this.leadId}/drive/files`)
                        .then(response => {
                            console.log('📥 Response received:', response.data);
                            if (response.data.success) {
                                this.files = response.data.files || [];
                                console.log('✅ Files loaded:', this.files.length);
                            }
                            this.isLoading = false;
                        })
                        .catch(error => {
                            console.error('❌ Error loading Google Drive files:', error);
                            this.isLoading = false;
                        });
                },

                getFileIcon(mimeType) {
                    if (mimeType.includes('folder')) return 'icon-folder';
                    if (mimeType.includes('image')) return 'icon-image';
                    if (mimeType.includes('pdf')) return 'icon-file-text';
                    if (mimeType.includes('document')) return 'icon-file-text';
                    if (mimeType.includes('spreadsheet')) return 'icon-table';
                    if (mimeType.includes('presentation')) return 'icon-presentation';
                    return 'icon-file';
                },

                formatFileSize(bytes) {
                    if (!bytes) return 'Unknown size';
                    const kb = bytes / 1024;
                    const mb = kb / 1024;
                    if (mb >= 1) return `${mb.toFixed(1)} MB`;
                    if (kb >= 1) return `${kb.toFixed(0)} KB`;
                    return `${bytes} bytes`;
                },

                formatDate(dateString) {
                    if (!dateString) return 'Unknown date';
                    const date = new Date(dateString);
                    const now = new Date();
                    const diffMs = now - date;
                    const diffDays = Math.floor(diffMs / (1000 * 60 * 60 * 24));
                    
                    if (diffDays === 0) return 'Today';
                    if (diffDays === 1) return 'Yesterday';
                    if (diffDays < 7) return `${diffDays} days ago`;
                    
                    return date.toLocaleDateString();
                },
            },
        });
    </script>
@endPushOnce
