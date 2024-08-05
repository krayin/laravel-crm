<x-admin::layouts>
    <!-- Title of the page. -->
    <x-slot:title>
        @lang('admin::app.configuration.title')
    </x-slot>

    <!-- Heading of the page -->
    <div class="mb-7 flex items-center justify-between">
        <p class="py-3 text-xl font-bold text-gray-800 dark:text-white">
            @lang('admin::app.configuration.title')
        </p>
    </div>

    <!-- Page Content -->
    <div class="grid gap-y-8">
        @foreach (system_config()->getItems() as $item)
            <div>
                <div class="grid gap-1">
                    <!-- Title of the Main Card -->
                    <p class="font-semibold text-gray-600 dark:text-gray-300">
                        {{ $item->getName() }}
                    </p>

                    <!-- Info of the Main Card -->
                    <p class="text-gray-600 dark:text-gray-300">
                        {{ $item->getInfo() }}
                    </p>
                </div>

                <div class="box-shadow max-1580:grid-cols-3 mt-2 grid grid-cols-4 flex-wrap justify-between gap-12 rounded bg-white p-4 dark:bg-gray-900 max-xl:grid-cols-2 max-sm:grid-cols-1">
                    <!-- Menus cards -->
                    @foreach ($item->getChildren() as $key => $child)
                        <a 
                            class="flex max-w-[360px] items-center gap-2 rounded-lg p-2 transition-all hover:bg-gray-100 dark:hover:bg-gray-950"
                            href="{{ route('admin.configuration.index', ($item->getKey() . '/' . $key)) }}"
                        >
                            @if ($icon = $child->getIcon())
                                <img
                                    class="h-[60px] w-[60px] dark:mix-blend-exclusion dark:invert"
                                    {{-- src="{{ admin_vite()->set('images/' . $icon) }}" --}}
                                >
                            @endif

                            <div class="grid">
                                <p class="mb-1.5 text-base font-semibold text-gray-800 dark:text-white">
                                    {{ $child->getName() }}
                                </p>
                                
                                <p class="text-xs text-gray-600 dark:text-gray-300">
                                    {{ $child->getInfo() }}
                                </p>
                            </div>
                        </a>
                    @endforeach
                </div>
            </div>
        @endforeach
    </div>
</x-admin::layouts>
