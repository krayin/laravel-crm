<a
    href="{{ $card['url'] }}"
    target="_blank"
    class="group flex flex-col rounded-xl border border-gray-200 bg-white p-5 transition-all hover:shadow-lg dark:border-gray-800 dark:bg-gray-900"
>
    <div class="mb-5 flex items-start justify-between">
        <span class="flex h-12 w-12 items-center justify-center rounded-lg bg-violet-50 text-brandColor dark:bg-gray-800">
            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                {!! $icons[$card['icon']] !!}
            </svg>
        </span>

        <svg class="h-5 w-5 text-gray-300 transition-colors group-hover:text-brandColor" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 19.5 19.5 4.5m0 0H8.25m11.25 0v11.25"/>
        </svg>
    </div>

    <p class="mb-2 text-lg font-semibold text-gray-800 dark:text-white">
        @lang($card['title'])
    </p>

    <p class="mb-5 text-sm text-gray-600 dark:text-gray-300">
        @lang($card['description'])
    </p>

    <span class="mt-auto text-sm text-gray-400 transition-colors group-hover:text-brandColor">
        {{ $card['url_label'] }}
    </span>
</a>
