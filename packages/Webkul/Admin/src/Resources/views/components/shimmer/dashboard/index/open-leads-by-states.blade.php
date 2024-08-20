<div class="grid gap-4 rounded-lg border border-gray-200 bg-white p-4 dark:border-gray-800 dark:bg-gray-900">
    <div class="flex items-center justify-between">
        <div class="shimmer h-[17px] w-28"></div>
    </div>

    <div class="flex w-full max-w-full flex-col gap-4">
        <div class="flex w-full flex-col">
            @for ($i = 0; $i < 4; $i++)
                <div class="flex w-full flex-col gap-1 border-b border-gray-200 pb-[9px] pt-2.5 last:border-none dark:border-gray-800">
                    <div class="shimmer h-[18px] w-[76px] rounded-sm"></div>
                    <div class="shimmer h-[18px] w-[76px] rounded-sm"></div>
                </div>
            @endfor
        </div>
    </div>
</div>