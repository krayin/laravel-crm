{{-- Google Drive Files Tab --}}
<div class="p-4" id="google-drive-files-container-{{ $lead->id }}">
    @if(!$lead->google_drive_folder_id)
        {{-- No Drive Folder Yet --}}
        <div class="flex flex-col items-center justify-center py-12">
            <span class="icon-google-drive text-6xl text-gray-300 dark:text-gray-600"></span>
            <p class="mt-4 text-gray-600 dark:text-gray-400">
                Google Drive folder not created yet
            </p>
            <p class="text-sm text-gray-500 dark:text-gray-500">
                Create a Drive folder from the left panel to see files here
            </p>
        </div>
    @else
        {{-- Header with Reload Button --}}
        <div class="mb-4 flex items-center justify-between border-b border-gray-200 pb-4 dark:border-gray-700">
            <h3 class="text-lg font-semibold dark:text-white">
                Files from Google Drive
            </h3>
            <button
                type="button"
                onclick="window.reloadGoogleDriveFiles{{ $lead->id }}()"
                class="inline-flex items-center gap-2 rounded bg-blue-600 px-3 py-1.5 text-sm text-white hover:bg-blue-700"
            >
                <span class="icon-refresh"></span>
                Обновить файлы
            </button>
        </div>

        {{-- Loading State --}}
        <div id="files-loading-{{ $lead->id }}" class="flex items-center justify-center py-12">
            <div class="flex flex-col items-center gap-2">
                <div class="h-8 w-8 animate-spin rounded-full border-4 border-gray-200 border-t-blue-600"></div>
                <p class="text-sm text-gray-600 dark:text-gray-400">Loading files from Google Drive...</p>
            </div>
        </div>

        {{-- Files List --}}
        <div id="files-list-{{ $lead->id }}" class="hidden">
            {{-- Files will be loaded here via JavaScript --}}
        </div>

        {{-- Empty State --}}
        <div id="files-empty-{{ $lead->id }}" class="hidden flex-col items-center justify-center py-12">
            <span class="icon-file text-6xl text-gray-300 dark:text-gray-600"></span>
            <p class="mt-4 text-gray-600 dark:text-gray-400">
                No files in this folder yet
            </p>
            <p class="text-sm text-gray-500 dark:text-gray-500">
                Upload files to Google Drive to see them here
            </p>
            <a 
                href="{{ $lead->google_drive_folder_url }}" 
                target="_blank"
                class="mt-4 inline-flex items-center gap-2 rounded bg-blue-600 px-4 py-2 text-sm text-white hover:bg-blue-700"
            >
                <span class="icon-external-link"></span>
                Open Folder in Google Drive
            </a>
        </div>
    @endif
</div>

{{-- JavaScript to Load Files --}}
<script>
(function() {
    const leadId = {{ $lead->id }};
    const hasDriveFolder = {{ $lead->google_drive_folder_id ? 'true' : 'false' }};

    if (!hasDriveFolder) {
        console.log('No Drive folder for lead', leadId);
        return;
    }

    console.log('✅ Google Drive Files script loaded for lead', leadId);

    let filesLoaded = false;

    // Wait for DOM to be ready
    document.addEventListener('DOMContentLoaded', function() {
        console.log('DOM ready, setting up Google Drive files tab listener for lead', leadId);
        
        // Try to find and click on the tab to trigger loading
        setTimeout(function() {
            // Listen for any clicks on the document
            document.addEventListener('click', function(e) {
                // Check if clicked element or its parent contains "Google Drive" text
                const clickedText = e.target.textContent || '';
                if (clickedText.includes('Google Drive') && !filesLoaded) {
                    console.log('📂 Google Drive tab clicked, loading files...');
                    filesLoaded = true;
                    setTimeout(loadDriveFiles, 100); // Small delay to ensure tab content is visible
                }
            });
            
            // Also check if tab is already active on load
            const container = document.getElementById(`google-drive-files-container-${leadId}`);
            if (container && container.offsetParent !== null) {
                console.log('📂 Google Drive tab already visible, loading files...');
                filesLoaded = true;
                loadDriveFiles();
            }
        }, 500);
    });

    function loadDriveFiles() {
        console.log('🔄 Loading Drive files for lead:', leadId);
        
        const loadingEl = document.getElementById(`files-loading-${leadId}`);
        const listEl = document.getElementById(`files-list-${leadId}`);
        const emptyEl = document.getElementById(`files-empty-${leadId}`);

        if (!loadingEl) {
            console.error('❌ Loading element not found');
            return;
        }

        // Reset states
        loadingEl.classList.remove('hidden');
        listEl.classList.add('hidden');
        emptyEl.classList.add('hidden');

        fetch(`/admin/leads/${leadId}/drive/files`)
            .then(response => {
                console.log('📡 Response status:', response.status);
                if (!response.ok) {
                    throw new Error(`HTTP ${response.status}`);
                }
                return response.json();
            })
            .then(data => {
                console.log('✅ Drive files data:', data);
                loadingEl.classList.add('hidden');

                if (data.success && data.files && data.files.length > 0) {
                    console.log(`📁 Found ${data.files.length} files`);
                    listEl.classList.remove('hidden');
                    renderFiles(data.files);
                } else {
                    console.log('📭 No files found');
                    emptyEl.classList.remove('hidden');
                    emptyEl.classList.add('flex');
                }
            })
            .catch(error => {
                console.error('❌ Error loading files:', error);
                loadingEl.classList.add('hidden');
                emptyEl.classList.remove('hidden');
                emptyEl.classList.add('flex');
            });
    }

    function renderFiles(files) {
        const listEl = document.getElementById(`files-list-${leadId}`);
        
        const html = `
            <div class="divide-y divide-gray-200 dark:divide-gray-700">
                <div class="mb-4 flex items-center justify-between">
                    <h3 class="text-lg font-semibold dark:text-white">
                        Files from Google Drive (${files.length})
                    </h3>
                    <a 
                        href="{{ $lead->google_drive_folder_url }}" 
                        target="_blank"
                        class="inline-flex items-center gap-2 text-sm text-blue-600 hover:text-blue-700 dark:text-blue-400"
                    >
                        <span class="icon-external-link"></span>
                        Open in Drive
                    </a>
                </div>
                ${files.map(file => renderFile(file)).join('')}
            </div>
        `;
        
        listEl.innerHTML = html;
    }

    function renderFile(file) {
        const icon = getFileIcon(file.mimeType);
        const size = formatFileSize(file.size);
        const date = formatDate(file.modifiedTime);
        
        return `
            <div class="flex items-center justify-between py-3">
                <div class="flex items-center gap-3 flex-1">
                    <span class="${icon} text-2xl text-gray-400"></span>
                    <div class="flex-1">
                        <p class="font-medium dark:text-white">${escapeHtml(file.name)}</p>
                        <p class="text-xs text-gray-500 dark:text-gray-400">
                            ${size} • Modified ${date}
                        </p>
                    </div>
                </div>
                <a 
                    href="${file.url}" 
                    target="_blank"
                    class="rounded px-3 py-1.5 text-sm text-blue-600 hover:bg-blue-50 dark:text-blue-400 dark:hover:bg-gray-800"
                >
                    Open
                </a>
            </div>
        `;
    }

    function getFileIcon(mimeType) {
        if (mimeType.includes('folder')) return 'icon-folder';
        if (mimeType.includes('image')) return 'icon-image';
        if (mimeType.includes('pdf')) return 'icon-file-text';
        if (mimeType.includes('document')) return 'icon-file-text';
        if (mimeType.includes('spreadsheet')) return 'icon-table';
        if (mimeType.includes('presentation')) return 'icon-presentation';
        return 'icon-file';
    }

    function formatFileSize(bytes) {
        if (!bytes) return 'Unknown size';
        const kb = bytes / 1024;
        const mb = kb / 1024;
        if (mb >= 1) return `${mb.toFixed(1)} MB`;
        if (kb >= 1) return `${kb.toFixed(0)} KB`;
        return `${bytes} bytes`;
    }

    function formatDate(dateString) {
        if (!dateString) return 'Unknown date';
        const date = new Date(dateString);
        const now = new Date();
        const diffMs = now - date;
        const diffDays = Math.floor(diffMs / (1000 * 60 * 60 * 24));
        
        if (diffDays === 0) return 'Today';
        if (diffDays === 1) return 'Yesterday';
        if (diffDays < 7) return `${diffDays} days ago`;
        
        return date.toLocaleDateString();
    }

    function escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }
    
    // Expose reload function for the button
    window.reloadGoogleDriveFiles{{ $lead->id }} = function() {
        console.log('🔄 Manual reload triggered for lead', leadId);
        filesLoaded = false; // Allow reloading
        loadDriveFiles();
    };
})();
</script>
