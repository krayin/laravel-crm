@php
    $icons = [
        'cloud'      => '<path stroke-linecap="round" stroke-linejoin="round" d="M2.25 15a4.5 4.5 0 0 0 4.5 4.5H18a3.75 3.75 0 0 0 1.332-7.257 3 3 0 0 0-3.758-3.848 5.25 5.25 0 0 0-10.233 2.33A4.502 4.502 0 0 0 2.25 15Z"/>',
        'support'    => '<path stroke-linecap="round" stroke-linejoin="round" d="M16.712 4.33a9.027 9.027 0 0 1 1.652 1.306c.51.51.944 1.064 1.306 1.652M16.712 4.33l-3.448 4.138m3.448-4.138a9.014 9.014 0 0 0-9.424 0M19.67 7.288l-4.138 3.448m4.138-3.448a9.014 9.014 0 0 1 0 9.424m-4.138-5.976a3.736 3.736 0 0 0-.88-1.388 3.737 3.737 0 0 0-1.388-.88m2.268 2.268a3.765 3.765 0 0 1 0 2.528m-2.268-4.796a3.765 3.765 0 0 0-2.528 0m4.796 4.796c-.181.506-.475.982-.88 1.388a3.736 3.736 0 0 1-1.388.88m2.268-2.268 4.138 3.448m0 0a9.027 9.027 0 0 1-1.306 1.652c-.51.51-1.064.944-1.652 1.306m0 0-3.448-4.138m3.448 4.138a9.014 9.014 0 0 1-9.424 0m5.976-4.138a3.765 3.765 0 0 1-2.528 0m0 0a3.736 3.736 0 0 1-1.388-.88 3.737 3.737 0 0 1-.88-1.388m2.268 2.268L7.288 19.67m0 0a9.024 9.024 0 0 1-1.652-1.306 9.027 9.027 0 0 1-1.306-1.652m0 0 4.138-3.448M4.33 16.712a9.014 9.014 0 0 1 0-9.424m4.138 5.976a3.765 3.765 0 0 1 0-2.528m0 0c.181-.506.475-.982.88-1.388a3.736 3.736 0 0 1 1.388-.88m-2.268 2.268L4.33 7.288m6.406 1.18L7.288 4.33m0 0a9.024 9.024 0 0 0-1.652 1.306A9.025 9.025 0 0 0 4.33 7.288"/>',
        'services'   => '<path stroke-linecap="round" stroke-linejoin="round" d="M11.42 15.17 17.25 21A2.652 2.652 0 0 0 21 17.25l-5.877-5.877M11.42 15.17l2.496-3.03c.317-.384.74-.626 1.208-.766M11.42 15.17l-4.655 5.653a2.548 2.548 0 1 1-3.586-3.586l6.837-5.63m5.108-.233c.55-.164 1.163-.188 1.743-.14a4.5 4.5 0 0 0 4.486-6.336l-3.276 3.277a3.004 3.004 0 0 1-2.25-2.25l3.276-3.276a4.5 4.5 0 0 0-6.336 4.486c.091 1.076-.071 2.264-.904 2.95l-.102.085m-1.745 1.437L5.909 7.5H4.5L2.25 3.75l1.5-1.5L7.5 4.5v1.409l4.26 4.26m-1.745 1.437 1.745-1.437m6.615 8.206L15.75 15.75M4.867 19.125h.008v.008h-.008v-.008Z"/>',
        'extensions' => '<path stroke-linecap="round" stroke-linejoin="round" d="M14.25 6.087c0-.355.186-.676.401-.959.221-.29.349-.634.349-1.003 0-1.036-1.007-1.875-2.25-1.875s-2.25.84-2.25 1.875c0 .369.128.713.349 1.003.215.283.401.604.401.959v0a.64.64 0 0 1-.657.643 48.39 48.39 0 0 1-4.163-.3c.186 1.613.293 3.25.315 4.907a.656.656 0 0 1-.658.663v0c-.355 0-.676-.186-.959-.401a1.647 1.647 0 0 0-1.003-.349c-1.036 0-1.875 1.007-1.875 2.25s.84 2.25 1.875 2.25c.369 0 .713-.128 1.003-.349.283-.215.604-.401.959-.401v0c.31 0 .555.26.532.57a48.039 48.039 0 0 1-.642 5.056c1.518.19 3.058.309 4.616.354a.64.64 0 0 0 .657-.643v0c0-.355-.186-.676-.401-.959a1.647 1.647 0 0 1-.349-1.003c0-1.035 1.008-1.875 2.25-1.875 1.243 0 2.25.84 2.25 1.875 0 .369-.128.713-.349 1.003-.215.283-.4.604-.4.959v0c0 .333.277.599.61.58a48.1 48.1 0 0 0 5.427-.63 48.05 48.05 0 0 0 .582-4.717.532.532 0 0 0-.533-.57v0c-.355 0-.676.186-.959.401-.29.221-.634.349-1.003.349-1.035 0-1.875-1.007-1.875-2.25s.84-2.25 1.875-2.25c.37 0 .713.128 1.003.349.283.215.604.401.96.401v0a.656.656 0 0 0 .658-.663 48.422 48.422 0 0 0-.37-5.36c-1.886.342-3.81.574-5.766.689a.578.578 0 0 1-.61-.58v0Z"/>',
        'docs'       => '<path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 0 0 6 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 0 1 6 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 0 1 6-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0 0 18 18a8.967 8.967 0 0 0-6 2.292m0-14.25v14.25"/>',
        'api'        => '<path stroke-linecap="round" stroke-linejoin="round" d="M17.25 6.75 22.5 12l-5.25 5.25m-10.5 0L1.5 12l5.25-5.25m7.5-3-4.5 16.5"/>',
        'community'  => '<path stroke-linecap="round" stroke-linejoin="round" d="M18 18.72a9.094 9.094 0 0 0 3.741-.479 3 3 0 0 0-4.682-2.72m.94 3.198.001.031c0 .225-.012.447-.037.666A11.944 11.944 0 0 1 12 21c-2.17 0-4.207-.576-5.963-1.584A6.062 6.062 0 0 1 6 18.719m12 0a5.971 5.971 0 0 0-.941-3.197m0 0A5.995 5.995 0 0 0 12 12.75a5.995 5.995 0 0 0-5.058 2.772m0 0a3 3 0 0 0-4.681 2.72 8.986 8.986 0 0 0 3.74.477m.94-3.197a5.971 5.971 0 0 0-.94 3.197M15 6.75a3 3 0 1 1-6 0 3 3 0 0 1 6 0Zm6 3a2.25 2.25 0 1 1-4.5 0 2.25 2.25 0 0 1 4.5 0Zm-13.5 0a2.25 2.25 0 1 1-4.5 0 2.25 2.25 0 0 1 4.5 0Z"/>',
        'video'      => '<path stroke-linecap="round" stroke-linejoin="round" d="M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15.91 11.672a.375.375 0 0 1 0 .656l-5.603 3.113a.375.375 0 0 1-.557-.328V8.887c0-.286.307-.466.557-.327l5.603 3.112Z"/>',
    ];
@endphp

<x-admin::layouts>
    <x-slot:title>
        @lang('admin::app.help.index.title')
    </x-slot>

    {!! view_render_event('admin.help.index.before') !!}

    <div class="flex flex-col gap-8 min-h-[calc(100vh-62px)] max-w-full bg-gray-100 pt-3 transition-all duration-300 dark:bg-gray-950">
        <!-- Hero -->
        {!! view_render_event('admin.help.index.hero.before') !!}

        <div class="flex flex-wrap items-center justify-between gap-4">
            <div class="grid max-w-3xl gap-2">
                <p class="text-2xl font-bold text-gray-800 dark:text-white">
                    @lang('admin::app.help.index.title')
                </p>

                <p class="text-gray-600 dark:text-gray-300">
                    @lang('admin::app.help.index.description')
                </p>
            </div>

            <a
                href="{{ $support['contact_url'] }}"
                target="_blank"
                class="primary-button"
            >
                @lang('admin::app.help.index.contact-us')
            </a>
        </div>

        {!! view_render_event('admin.help.index.hero.after') !!}

        <!-- Services -->
        {!! view_render_event('admin.help.index.services.before') !!}

        <div class="flex flex-col gap-4">
            <p class="text-sm font-semibold uppercase tracking-wider text-gray-400 dark:text-gray-400">
                @lang('admin::app.help.index.services.title')
            </p>

            <div class="grid grid-cols-1 gap-5 md:grid-cols-2 lg:grid-cols-3">
                @foreach ($support['services'] as $card)
                    @include('admin::help.card', ['card' => $card, 'icons' => $icons])
                @endforeach
            </div>
        </div>

        {!! view_render_event('admin.help.index.services.after') !!}

        <!-- Resources -->
        {!! view_render_event('admin.help.index.resources.before') !!}

        <div class="flex flex-col gap-4">
            <p class="text-sm font-semibold uppercase tracking-wider text-gray-400 dark:text-gray-400">
                @lang('admin::app.help.index.resources.title')
            </p>

            <div class="grid grid-cols-1 gap-5 md:grid-cols-2 lg:grid-cols-3">
                @foreach ($support['resources'] as $card)
                    @include('admin::help.card', ['card' => $card, 'icons' => $icons])
                @endforeach
            </div>
        </div>

        {!! view_render_event('admin.help.index.resources.after') !!}

        <!-- Still need a hand CTA -->
        <div class="flex flex-col items-center justify-between gap-6 rounded-2xl bg-brandColor p-8 md:flex-row">
            <div class="flex items-center gap-6">
                <span class="flex h-16 w-16 shrink-0 items-center justify-center rounded-full bg-white/20 text-white">
                    <svg class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M8.625 9.75a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Zm0 0H8.25m4.125 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Zm0 0H12m4.125 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Zm0 0h-.375M21 12c0 4.556-4.03 8.25-9 8.25a9.764 9.764 0 0 1-2.555-.337A5.972 5.972 0 0 1 5.41 20.97a5.969 5.969 0 0 1-.474-.065 4.48 4.48 0 0 0 .978-2.025c.09-.457-.133-.901-.467-1.226C3.93 16.178 3 14.189 3 12c0-4.556 4.03-8.25 9-8.25s9 3.694 9 8.25Z"/>
                    </svg>
                </span>

                <div class="text-white">
                    <p class="text-xl font-bold">@lang('admin::app.help.index.still-need-help-title')</p>
                    <p class="opacity-90">@lang('admin::app.help.index.still-need-help-description')</p>
                </div>
            </div>

            <a href="{{ $support['contact_url'] }}" target="_blank" class="shrink-0 rounded-lg bg-white px-6 py-3 font-semibold text-brandColor transition-opacity hover:opacity-90">
                @lang('admin::app.help.index.contact-us')
            </a>
        </div>

        <!-- Community & Tutorials -->
        <div class="grid grid-cols-1 gap-5 md:grid-cols-2">
            @foreach ($support['community'] as $card)
                <div class="flex items-start gap-5 rounded-xl border border-gray-200 bg-white p-6 dark:border-gray-800 dark:bg-gray-900">
                    <span class="flex h-12 w-12 shrink-0 items-center justify-center rounded-lg bg-violet-50 text-brandColor dark:bg-gray-800">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            {!! $icons[$card['icon']] !!}
                        </svg>
                    </span>

                    <div>
                        <p class="mb-2 text-lg font-semibold text-gray-800 dark:text-white">@lang($card['title'])</p>
                        <p class="mb-4 text-sm text-gray-600 dark:text-gray-300">@lang($card['description'])</p>
                        <a href="{{ $card['url'] }}" target="_blank" class="text-sm font-semibold text-brandColor hover:underline">
                            @lang($card['link_label'])
                        </a>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    {!! view_render_event('admin.help.index.after') !!}
</x-admin::layouts>
