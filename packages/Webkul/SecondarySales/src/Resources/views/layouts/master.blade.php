<x-admin::layouts>
    <!-- Title -->
    <x-slot:title>
        @yield('page_title')
    </x-slot>

    <!-- Body -->
    <div class="flex flex-col gap-4">
        <div class="flex items-center justify-between rounded-lg border border-gray-200 bg-white px-4 py-2 text-sm dark:border-gray-800 dark:bg-gray-900 dark:text-gray-300">
            <div class="flex flex-col gap-2">
                <div class="flex cursor-pointer items-center">
                    <!-- Breadcrumbs -->
                        
                </div>

                <div class="text-xl font-bold dark:text-white">
                    @yield('content')
                </div>
            </div>
        </div>

    </div>



</x-admin::layouts>
