<div class="grid grid-cols-3 gap-4">
    @for ($i = 1; $i <= 6; $i++)
        <div class="flex flex-col gap-2 rounded-lg border border-gray-200 bg-white px-4 py-5 dark:border-gray-800 dark:bg-gray-900">
            <div class="shimmer h-[17px] w-40"></div>

            <div class="flex gap-2">
                <div class="shimmer h-[17px] w-24"></div>

                <div class="shimmer h-[17px] w-8"></div>
            </div>
        </div>
    @endfor
</div>