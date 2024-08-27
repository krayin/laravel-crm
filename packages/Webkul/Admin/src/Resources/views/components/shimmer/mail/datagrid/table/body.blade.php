@for ($i = 0; $i < 10; $i++)
    <div class="flex justify-between items-center border-b px-8 py-4 text-gray-600 cursor-pointer hover:bg-gray-50 dark:border-gray-800 dark:text-gray-300 dark:hover:bg-gray-950">
        <div class="flex gap-32 items-center justify-start w-full">
            <div class="flex items-center gap-6">
                <div class="relative flex items-center">
                    <div class="flex gap-1.5 items-center justify-center">
                        <div class="shimmer absolute right-8 h-1.5 w-1.5 rounded-full bg-sky-600 dark:bg-white"></div>

                        <div class="shimmer h-6 w-6"></div>
                    </div>
                </div>
            
                <div class="shimmer h-[17px] w-[150px]"></div>
            </div>

            <div class="flex gap-4 items-center justify-between w-full">
                <div class="flex gap-2 items-center flex-frow">
                    <div class="shimmer h-6 w-[850px]"></div>
                </div>
            
                <div class="flex-shrink-0 min-w-[80px] text-right">
                    <div class="shimmer h-[17px] w-[100px]"></div>
                </div>
            </div>
        </div>
    </div>
@endfor