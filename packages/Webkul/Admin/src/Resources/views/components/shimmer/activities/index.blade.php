<div class="w-full rounded-md border border-gray-200 bg-white dark:border-gray-800 dark:bg-gray-900">
    <!-- Tabs -->
    <div class="flex gap-2 border-b border-gray-200 dark:border-gray-800">
        @for ($i = 0; $i < 5; $i++)
            <div class="px-3 py-[11px]">
                <div class="shimmer h-5 w-24"></div>
            </div>
        @endfor
    </div>

    <!-- Tab Content -->
    <div class="p-4">
        <!-- Activity List -->
        <div class="flex flex-col gap-4">
            @for ($i = 0; $i < 5; $i++)
                <!-- Activity Item -->
                <div class="flex gap-2">
                    <!-- Icon -->
                    <div class="shimmer mt-2 flex h-9 w-9 rounded-full"></div>

                    <!-- Details -->
                    <div class="flex w-full justify-between gap-4 p-4">
                        <div class="flex w-full flex-col gap-2">
                            <div class="shimmer h-[17px] w-48"></div>

                            <div class="flex flex-col gap-1">
                                <div class="shimmer h-[17px] w-full"></div>
                                <div class="shimmer h-[17px] w-1/2"></div>
                            </div>

                            <div class="shimmer h-5 w-48"></div>
                        </div>

                        <!-- Menu -->
                        <div class="shimmer h-7 w-7 rounded-md"></div>
                    </div>
                </div>
            @endfor
        </div>
    </div>
</div>