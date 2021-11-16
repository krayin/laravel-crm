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

@push('scripts')
    <script>
        /**
         * Update status for `is_done`.
         *
         * @param {!object} event
         * @param {!string} href
         */
        function updateStatus({ target }, href) {
            const isDone = parseInt(target.value);

            window.app.pageLoaded = false;

            axios
                .post(href, {
                    _token: '{{ csrf_token() }}',
                    _method: 'PUT',
                    is_done: isDone,
                })
                .then(response => {
                    target.value = isDone ? '0' : '1';

                    window.app.pageLoaded = true;

                    window.addFlashMessages({
                        type: "success",
                        message : response.data.message
                    });
                })
                .catch(error => {
                    $(target).prop("checked", ! isDone);

                    window.app.pageLoaded = true;

                    window.addFlashMessages({
                        type: "error",
                        message : error.response.data.message
                    });
                });
        }
    </script>
@endpush
