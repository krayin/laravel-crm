@props([
    'count' => 1,
])

<div class="mt-3.5 flex gap-2.5 max-xl:flex-wrap">
    <div class="flex flex-1 flex-col gap-2 max-xl:flex-auto">
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

    <div class="flex w-[360px] max-w-full flex-col gap-2 max-sm:w-full">
        <div class="box-shadow rounded bg-white dark:bg-gray-900">
            <div class="flex flex-col gap-8 p-4">
                <div class="flex gap-4 w-full items-center justify-between">
                    <div class="flex flex-col gap-4">
                        <div class="shimmer h-4 w-20"></div>

                        <div class="flex gap-4">
                            <div class="shimmer flex h-9 w-9 items-center justify-center rounded-full bg-green-200 text-xs font-medium"></div>

                            <div class="flex flex-col gap-1">
                                <div class="shimmer h-4 w-32"></div>
                                <div class="shimmer h-4 w-28"></div>
                                <div class="shimmer h-4 w-48"></div>
                                <div class="shimmer h-4 w-24"></div>
                            </div>
                        </div>
                    </div>

                    <div class="flex gap-2 items-center justify-center">
                        <div class="flex gap-2 items-center">
                            <div class="shimmer h-5 w-5"></div>
                            <div class="shimmer h-5 w-5"></div>
                        </div>
                    </div>
                </div>

                <div class="flex w-full flex-col gap-5 rounded-md dark:border-gray-400">
                    <div class="shimmer h-4 w-20"></div>

                    <!-- Header -->
                    <div class="flex items-start justify-between">
                        <div class="flex items-center gap-1">
                            <!-- Avatar -->
                            <div class="shimmer h-9 w-9 rounded-full"></div>

                            <!-- Name and Organization -->
                            <div class="flex flex-col gap-0.5">
                                <div class="shimmer h-4 w-20"></div>
                                <div class="shimmer h-[15px] w-12"></div>
                            </div>
                        </div>

                        <div class="flex gap-2 items-center justify-center">
                            <div class="flex gap-2 items-center">
                                <div class="shimmer h-5 w-5"></div>
                                <div class="shimmer h-5 w-5"></div>
                                <div class="shimmer h-5 w-5"></div>
                            </div>
                        </div>
                    </div>

                    <!-- Body -->
                    <div class="flex flex-col gap-0.5">
                        <div class="shimmer h-4 w-full"></div>
                        <div class="shimmer h-4 w-1/2"></div>
                    </div>

                    <!-- Footer -->
                    <div class="flex flex-wrap gap-1">
                        <div class="shimmer h-6 w-16 rounded-xl"></div>
                        <div class="shimmer h-6 w-16 rounded-xl"></div>
                        <div class="shimmer h-6 w-16 rounded-xl"></div>
                        <div class="shimmer h-6 w-16 rounded-xl"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>