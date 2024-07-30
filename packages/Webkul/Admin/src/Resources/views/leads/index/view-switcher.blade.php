<div class="flex gap-4">
    <x-admin::dropdown>
        <x-slot:toggle>
            <button
                type="button"
                class="flex cursor-pointer appearance-none items-center justify-between gap-x-2 rounded-md border bg-white px-2.5 py-[7px] text-center leading-6 text-gray-600 transition-all marker:shadow hover:border-gray-400 focus:border-gray-400 dark:border-gray-800 dark:bg-gray-900 dark:text-gray-300 dark:hover:border-gray-400 dark:focus:border-gray-400"
            >
                <span class="whitespace-nowrap">
                    {{ $pipeline->name }}
                </span>
                
                <span class="icon-down-arrow text-2xl"></span>
            </button>
        </x-slot>

        <x-slot:content class="!p-0">
            <!-- Header -->
            <div class="flex items-center justify-between px-3 py-2.5">
                <span class="text-sm font-medium text-gray-400">All Pipelines</span>
            </div>
            
            <!-- Pipeline Links -->
            @foreach (app('Webkul\Lead\Repositories\PipelineRepository')->all() as $tempPipeline)
                <a
                    href="{{ route('admin.leads.index', [
                        'pipeline_id' => $tempPipeline->id,
                        'view_type'   => request('view_type')
                    ]) }}"
                    class="block px-3 py-2.5 pl-4 text-sm text-gray-600 transition-all hover:bg-gray-100 {{ $pipeline->id == $tempPipeline->id ? 'bg-gray-100' : '' }}"
                >
                    {{ $tempPipeline->name }}
                </a>
            @endforeach

            <!-- Footer -->
            <a
                href="{{ route('admin.settings.pipelines.create') }}"
                target="_blank"
                class="flex items-center justify-between border-t border-gray-300 px-3 py-2.5 text-brandColor"
            >
                <span class="text-sm font-medium">Create New Pipeline</span>
            </a>
        </x-slot>
    </x-admin::dropdown>

    <div class="flex items-center gap-0.5">
        @if (request('view_type'))
            <a href="{{ route('admin.leads.index') }}">
                <span class="icon-kanban p-2 text-2xl"></span>
            </a>

            <span class="icon-list rounded-md bg-white p-2 text-2xl"></span>
        @else
            <span class="icon-kanban rounded-md bg-white p-2 text-2xl"></span>

            <a
                href="{{ route('admin.leads.index', ['view_type' => 'table']) }}"
                class="flex"
            >
                <span class="icon-list p-2 text-2xl"></span>
            </a>
        @endif
    </div>
</div>