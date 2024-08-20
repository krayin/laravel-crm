<div class="w-full rounded-lg border border-gray-200 bg-white dark:bg-gray-900 dark:border-gray-800">
    <div class="flex items-center justify-between p-4">
        <div class="shimmer h-[17px] w-28"></div>
    </div>

    <div class="flex flex-col">
        @for ($i = 1; $i <= 5; $i++)
            <div class="flex gap-2.5 border-b p-4 last:border-b-0 dark:border-gray-800">
                <!-- Product Details -->
                <div class="flex w-full flex-col gap-1.5">
                    <!-- Product Name -->
                    <div class="shimmer h-[17px] w-full"></div>

                    <div class="flex justify-between">
                        <!-- Product Price -->
                        <div class="shimmer h-[17px] w-[52px]"></div>

                        <!-- Grand Total -->
                        <div class="shimmer h-[17px] w-[72px]"></div>
                    </div>
                </div>
            </div>
        @endfor
    </div>
</div>