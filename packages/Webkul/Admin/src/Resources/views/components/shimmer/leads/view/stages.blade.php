@props(['count' => 5])

<div class="flex">
    @for ($i = 0; $i < $count; $i++)
        <div class="stage relative flex h-7 min-w-24 cursor-pointer items-center justify-center bg-white pl-7 pr-4 first:rounded-l-lg">
            <div class="shimmer h-5 w-[68px]"></div>
        </div>
    @endfor
</div>