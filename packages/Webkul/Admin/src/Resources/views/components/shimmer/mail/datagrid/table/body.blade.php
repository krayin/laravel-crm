@for ($i = 0; $i < 10; $i++)
    <div class="flex cursor-pointer items-center justify-between border-b px-8 py-4 text-gray-600 hover:bg-gray-50 dark:border-gray-800 dark:text-gray-300 dark:hover:bg-gray-950">
        <div class="flex w-full items-center justify-start gap-[100px]">
            <div class="flex items-center gap-6">
                <div class="relative flex items-center">
                    <div class="flex items-center justify-center gap-1.5">
                        <div class="shimmer absolute right-8 h-1.5 w-1.5 rounded-full bg-sky-600 dark:bg-white"></div>

                        <div class="shimmer h-6 w-6"></div>
                    </div>
                </div>
            
                <div class="flex items-center gap-2">
                    <div class="shimmer flex h-9 min-w-9 rounded-full"></div>

                    <div class="shimmer h-[17px] w-[125px]"></div>
                </div>
            </div>

            <div class="flex w-full items-center justify-between gap-4">
                <div class="flex-frow flex items-center gap-2">
                    <div class="shimmer h-6 w-[650px]"></div>
                </div>
            
                <div class="min-w-[80px] flex-shrink-0 text-right">
                    <div class="shimmer h-[17px] w-[100px]"></div>
                </div>
            </div>
        </div>
    </div>
@endfor