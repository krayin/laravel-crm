@props(['count' => 5])

<div class="flex items-center gap-1">
    @for ($i = 0; $i < $count; $i++)
        <div class="shimmer h-7 w-16 rounded-md"></div>
    @endfor

    <div class="shimmer h-7 w-7 rounded-md"></div>
</div>