<x-admin::layouts>
    <x-slot:title>
        Brand Kit
    </x-slot>

    <div class="flex items-center justify-between gap-4 max-sm:flex-wrap">
        <p class="text-xl font-bold text-gray-800 dark:text-white">
            Brand Kit
        </p>
    </div>

    <div class="mt-7 flex flex-col gap-4">
        {{-- Overrides Section --}}
        <div class="box-shadow rounded-lg bg-white p-4 dark:bg-gray-900">
            <h3 class="mb-4 text-lg font-semibold text-gray-800 dark:text-white">
                Overrides
            </h3>
            <p class="text-sm text-gray-600 dark:text-gray-300">
                Configure theme overrides (colors, logos, etc.)
            </p>

            <div class="mt-4 rounded-lg border border-gray-200 bg-gray-50 p-4 dark:border-gray-700 dark:bg-gray-800">
                <p class="text-xs text-gray-500 dark:text-gray-400">
                    API Endpoints:
                </p>
                <ul class="mt-2 list-inside list-disc text-xs text-gray-600 dark:text-gray-300">
                    <li>POST /admin/settings/brand-kit/override - Set override</li>
                    <li>POST /admin/settings/brand-kit/override/reset - Reset single</li>
                    <li>POST /admin/settings/brand-kit/override/reset-all - Reset all</li>
                </ul>
            </div>
        </div>

        {{-- Custom CSS Section --}}
        <div class="box-shadow rounded-lg bg-white p-4 dark:bg-gray-900">
            <h3 class="mb-4 text-lg font-semibold text-gray-800 dark:text-white">
                Custom CSS
            </h3>
            <p class="text-sm text-gray-600 dark:text-gray-300">
                Add custom CSS for admin or login pages.
            </p>

            <div class="mt-4 rounded-lg border border-gray-200 bg-gray-50 p-4 dark:border-gray-700 dark:bg-gray-800">
                <p class="text-xs text-gray-500 dark:text-gray-400">
                    API Endpoints:
                </p>
                <ul class="mt-2 list-inside list-disc text-xs text-gray-600 dark:text-gray-300">
                    <li>POST /admin/settings/brand-kit/css - Add CSS</li>
                    <li>POST /admin/settings/brand-kit/css/{id}/toggle - Toggle CSS</li>
                    <li>DELETE /admin/settings/brand-kit/css/{id} - Delete CSS</li>
                </ul>
            </div>
        </div>

        {{-- Snapshots Section --}}
        <div class="box-shadow rounded-lg bg-white p-4 dark:bg-gray-900">
            <h3 class="mb-4 text-lg font-semibold text-gray-800 dark:text-white">
                Snapshots
            </h3>
            <p class="text-sm text-gray-600 dark:text-gray-300">
                Create and restore backups of your theme configuration.
            </p>

            <div class="mt-4 rounded-lg border border-gray-200 bg-gray-50 p-4 dark:border-gray-700 dark:bg-gray-800">
                <p class="text-xs text-gray-500 dark:text-gray-400">
                    API Endpoints:
                </p>
                <ul class="mt-2 list-inside list-disc text-xs text-gray-600 dark:text-gray-300">
                    <li>POST /admin/settings/brand-kit/snapshots - Create snapshot</li>
                    <li>POST /admin/settings/brand-kit/snapshots/{id}/restore - Restore</li>
                </ul>
            </div>
        </div>

        {{-- Status --}}
        <div class="box-shadow rounded-lg bg-green-50 p-4 dark:bg-green-900/20">
            <div class="flex items-center gap-2">
                <span class="icon-done rounded-full bg-green-500 p-1 text-white"></span>
                <span class="text-sm font-medium text-green-800 dark:text-green-200">
                    Brand Kit API is ready. UI components coming soon.
                </span>
            </div>
        </div>
    </div>
</x-admin::layouts>
