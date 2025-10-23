@for ($i = 0; $i < 3; $i++)
    <div class="flex justify-between gap-2.5 border-b border-slate-300 p-4 dark:border-gray-800">
        <!-- Left Information -->
        <div class="flex gap-2.5">
            <!-- Details -->
            <div class="grid place-content-start gap-1.5">
                <p class="shimmer h-[17px] w-[150px]"></p>
                <p class="shimmer h-[17px] w-[350px]"></p>
            </div>
        </div>

        <!-- Right Information -->
        <div class="grid place-content-center gap-1 text-right">
            <p class="shimmer h-[17px] w-[50px]"></p>
        </div>
    </div>
@endfor
