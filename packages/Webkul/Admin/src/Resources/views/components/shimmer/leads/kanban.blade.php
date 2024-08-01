<x-admin::shimmer.leads.kanban.toolbar />

<div class="flex gap-4 overflow-x-auto">
    <!-- Stages -->
    @for ($i = 1; $i <= 6; $i++)
        <div class="flex min-w-[275px] flex-col gap-1 rounded-lg bg-white">
            <!-- Stage Header -->
            <div class="flex flex-col px-2 py-3">
                <div class="flex items-center justify-between">
                    <div class="shimmer h-4 w-20"></div>
                    <div class="shimmer h-[26px] w-[26px] rounded-md"></div>
                </div>

                <div class="flex items-center justify-between gap-2">
                    <div class="shimmer h-4 w-14"></div>
                    <div class="shimmer h-1 w-36"></div>
                </div>
            </div>

            <!-- Stage Lead Cards -->
            <div class="flex h-[calc(100vh-315px)] flex-col gap-2 overflow-y-auto p-2">
                @for ($j = 1; $j <= 3; $j++)
                    <!-- Card -->
                    <div class="shimmer min-h-[163px] w-full rounded-md"></div>
                @endfor
            </div>
        </div>
    @endfor
</div>