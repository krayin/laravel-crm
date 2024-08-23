<div class="flex gap-4 overflow-auto max-xl:flex-wrap">
    @for ($i = 1; $i <= 6; $i++)
        <div class="flex h-[380px] min-w-[275px] max-w-[275px] flex-col justify-between rounded-lg border border-gray-200 bg-white dark:border-gray-800 dark:bg-gray-900">
            <!-- Stage Header -->
            <div class="flex flex-col gap-6 px-4 py-3">
                <!-- Stage Title and Action -->
                <div class="flex items-center justify-between">
                    <div class="shimmer h-4 w-20"></div>
                    <div class="shimmer h-[28px] w-[28px] rounded-md"></div>
                </div>
                
                <!-- Attribute Name -->
                <div class="flex flex-col gap-4">
                    <div class="flex flex-col gap-2">
                        <div class="shimmer h-5 w-20"></div>
                        <div class="shimmer h-[38px] w-full rounded"></div>
                    </div>
                    
                    <div class="flex flex-col gap-2">
                        <div class="shimmer h-5 w-20"></div>
                        <div class="shimmer h-[38px] w-full rounded"></div>
                    </div>
                </div>
            </div>
            
            <div class="flex items-center gap-2 p-2">
                <div class="shimmer h-[26px] w-[26px] rounded-md"></div>

                <div class="shimmer h-5 w-20"></div>
            </div>
        </div>
    @endfor
</div>