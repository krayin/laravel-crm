{!! view_render_event('admin.leads.index.kanban.toolbar.before') !!}

<div class="flex justify-between">
    <div class="flex w-full items-center gap-x-1.5">
        <!-- Search Panel -->
        @include('admin::leads.index.kanban.search')

        <!-- Filter -->
        @include('admin::leads.index.kanban.filter')

        <div class="z-10 hidden w-full divide-y divide-gray-100 rounded bg-white shadow dark:bg-gray-900"></div>
    </div>

    <!-- View Switcher -->
    @include('admin::leads.index.view-switcher')
</div>

{!! view_render_event('admin.leads.index.kanban.toolbar.after') !!}
