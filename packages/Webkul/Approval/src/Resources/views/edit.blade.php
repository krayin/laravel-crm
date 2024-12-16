
<x-admin::layouts>
    <!-- Page Title -->
    <x-slot:title>
        @lang('approval::app.approvals.edit.title')
    </x-slot>

    {!! view_render_event('admin.approvals.edit.form.before') !!}

    <x-admin::form
        :action="route('admin.approval.update', $approval->id)"
        enctype="multipart/form-data"
        method="PUT"
    >
        <div class="flex flex-col gap-4">
            <div class="flex items-center justify-between rounded-lg border border-gray-200 bg-white px-4 py-2 text-sm dark:border-gray-800 dark:bg-gray-900 dark:text-gray-300">
                <div class="flex flex-col gap-2">
                    <div class="flex cursor-pointer items-center">
                        <!-- Breadcrumbs -->
                        <x-admin::breadcrumbs
                            name="approval.edit"
                            :entity="$approval"
                        />
                    </div>

                    <div class="text-xl font-bold dark:text-white">
                        @lang('approval::app.approvals.edit.title')
                    </div>
                </div>

                <div class="flex items-center gap-x-2.5">
                    <div class="flex items-center gap-x-2.5">
                        {!! view_render_event('admin.approvals.edit.create_button.before', ['approval' => $approval]) !!}

                        <!-- Edit button for approval -->
                        <button
                            type="submit"
                            class="primary-button"
                        >
                            @lang('approval::app.approvals.create.save-btn')
                        </button>

                        {!! view_render_event('admin.approvals.edit.create_button.after', ['approval' => $approval]) !!}
                    </div>
                </div>
            </div>

            <div class="flex gap-2.5 max-xl:flex-wrap">
                <!-- Left sub-component -->
                <div class="flex flex-1 flex-col gap-2 max-xl:flex-auto">
                    <div class="box-shadow rounded-lg border border-gray-200 bg-white p-4 dark:border-gray-800 dark:bg-gray-900">
                        <p class="mb-4 text-base font-semibold text-gray-800 dark:text-white">
                            @lang('approval::app.approvals.create.general')
                        </p>

                        {!! view_render_event('admin.approvals.edit.attributes.before', ['approval' => $approval]) !!}

                        <x-admin::attributes
                            :custom-attributes="app('Webkul\Attribute\Repositories\AttributeRepository')->findWhere([
                                'entity_type' => 'approvals',

                            ])"
                            :entity="$approval"
                        />

                        {!! view_render_event('admin.approvals.edit.attributes.after', ['approval' => $approval]) !!}
                    </div>
                </div>


            </div>
        </div>
    </x-admin::form>

    {!! view_render_event('admin.approvals.edit.form.after') !!}
</x-admin::layouts>
