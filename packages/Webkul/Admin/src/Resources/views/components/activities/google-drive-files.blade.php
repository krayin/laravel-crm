{{-- Google Drive Files Section for Activities Tab --}}
@if($activity->type === 'file' && $activity->lead && $activity->lead->google_drive_folder_id)
    <div class="mt-4 border-t border-gray-200 pt-4 dark:border-gray-700" id="google-drive-files-{{ $activity->id }}">
        <div class="mb-2 flex items-center justify-between">
            <h4 class="text-sm font-semibold text-gray-700 dark:text-gray-300">
                <span class="icon-google-drive mr-1"></span>
                Google Drive Files
            </h4>
            <button 
                type="button"
                onclick="loadGoogleDriveFilesForActivity{{ $activity->id }}()"
                class="text-xs text-blue-600 hover:text-blue-700"
            >
                Load from Drive
            </button>
        </div>

        <div id="drive-files-loading-{{ $activity->id }}" class="hidden py-2 text-center text-sm text-gray-500">
            Loading...
        </div>

        <div id="drive-files-list-{{ $activity->id }}" class="hidden flex flex-wrap gap-2">
            {{-- Files will be loaded here --}}
        </div>

        <div id="drive-files-empty-{{ $activity->id }}" class="hidden py-2 text-sm text-gray-500">
            No files in Google Drive
        </div>
    </div>

    <script>
    window.loadGoogleDriveFilesForActivity{{ $activity->id }} = function() {
        const leadId = {{ $activity->lead->id }};
        const activityId = {{ $activity->id }};
        const loadingEl = document.getElementById(`drive-files-loading-${activityId}`);
        const listEl = document.getElementById(`drive-files-list-${activityId}`);
        const emptyEl = document.getElementById(`drive-files-empty-${activityId}`);

        loadingEl.classList.remove('hidden');
        listEl.classList.add('hidden');
        emptyEl.classList.add('hidden');

        fetch(`/admin/leads/${leadId}/drive/files`, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            }
        })
        .then(response => response.json())
        .then(data => {
            loadingEl.classList.add('hidden');
            
            if (data.success && data.files && data.files.length > 0) {
                renderDriveFiles${activityId}(data.files);
                listEl.classList.remove('hidden');
            } else {
                emptyEl.classList.remove('hidden');
            }
        })
        .catch(error => {
            console.error('Error loading Google Drive files:', error);
            loadingEl.classList.add('hidden');
            emptyEl.classList.remove('hidden');
        });
    };

    function renderDriveFiles{{ $activity->id }}(files) {
        const listEl = document.getElementById('drive-files-list-{{ $activity->id }}');
        
        const html = files.map(file => {
            const icon = file.mimeType.includes('folder') ? 'icon-folder' :
                        file.mimeType.includes('image') ? 'icon-image' :
                        file.mimeType.includes('pdf') ? 'icon-file-text' :
                        file.mimeType.includes('document') ? 'icon-file-text' :
                        file.mimeType.includes('spreadsheet') ? 'icon-table' :
                        file.mimeType.includes('presentation') ? 'icon-presentation' : 'icon-file';
            
            return `
                <a 
                    href="${file.url}" 
                    target="_blank"
                    class="flex cursor-pointer items-center gap-1 rounded-md p-1.5 hover:bg-gray-100 dark:hover:bg-gray-800"
                    title="${file.name}"
                >
                    <span class="${icon} text-xl text-gray-400"></span>
                    <span class="font-medium text-brandColor">${file.name}</span>
                </a>
            `;
        }).join('');
        
        listEl.innerHTML = html;
    }
    </script>
@endif
