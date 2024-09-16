{!! view_render_event('admin.leads.index.kanban.toolbar.before') !!}

<div class="flex justify-between">
    <div class="flex w-full items-center gap-x-1.5">
        {!! view_render_event('admin.leads.index.kanban.toolbar.search.before') !!}

        <!-- Search Panel -->
        @include('admin::leads.index.kanban.search')

        {!! view_render_event('admin.leads.index.kanban.toolbar.search.after') !!}

        {!! view_render_event('admin.leads.index.kanban.toolbar.filter.before') !!}

        <!-- Filter -->
        @include('admin::leads.index.kanban.filter')

        {!! view_render_event('admin.leads.index.kanban.toolbar.filter.after') !!}

        <div class="z-10 hidden w-full divide-y divide-gray-100 rounded bg-white shadow dark:bg-gray-900"></div>
    </div>

    {!! view_render_event('admin.leads.index.kanban.toolbar.switcher.before') !!}

    <!-- View Switcher -->
    @include('admin::leads.index.view-switcher')

    {!! view_render_event('admin.leads.index.kanban.toolbar.switcher.after') !!}
</div>

{!! view_render_event('admin.leads.index.kanban.toolbar.after') !!}
