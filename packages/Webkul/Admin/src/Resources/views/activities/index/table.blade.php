<div class="content full-page">
    <table-component data-src="{{ route('admin.activities.get') }}" switch-page-url="{{ route('admin.activities.index') }}">

        <template v-slot:extra-filters>
            @include('admin::activities.index.view-swither')
        </template>

        <template v-slot:table-header>
            <h1>
                {!! view_render_event('admin.activities.index.header.before') !!}

                {{ Breadcrumbs::render('activities') }}

                {{ __('admin::app.activities.title') }}

                {!! view_render_event('admin.activities.index.header.after') !!}
            </h1>
        </template>
    <table-component>
</div>
