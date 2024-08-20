@props([
    'count' => 1,
])

<div class="flex flex-col gap-4">
    @for ($i = 0; $i < $count; $i++)
        <div class="flex gap-2.5 box-shadow rounded bg-white p-4 dark:bg-gray-900 max-xl:flex-wrap">
            <div class="flex flex-col gap-4 w-full">
                <div class="flex gap-4 w-full items-center justify-between">
                    <div class="flex gap-4">
                        <div class="shimmer flex h-9 w-9 items-center justify-center rounded-full bg-green-200 text-xs font-medium"></div>

                        <div class="flex flex-col gap-1">
                            <div class="shimmer h-4 w-32"></div>
                            <div class="shimmer h-4 w-48"></div>
                        </div>
                    </div>

                    <div class="flex gap-2 items-center justify-center">
                        <div class="flex gap-2 items-center">
                            <div class="shimmer h-4 w-20"></div>
                            <div class="shimmer h-5 w-5"></div>
                        </div>
                    </div>
                </div>

                <div class="shimmer h-40 w-full"></div>

                <div class="flex gap-2 items-center justify-between py-2">
                    <div class="flex gap-6 items-center">
                        <div class="shimmer h-5 w-20"></div>
                        <div class="shimmer h-5 w-20"></div>
                        <div class="shimmer h-5 w-20"></div>
                    </div>
                </div>
            </div>
        </div>    
    @endfor
</div>