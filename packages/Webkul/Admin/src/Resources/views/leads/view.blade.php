<x-admin::layouts>
    <x-slot:title>
        @lang('admin::app.leads.view.title', ['title' => $lead->title])
    </x-slot>

    <!-- Content -->
    <div class="flex gap-4">
        <!-- Left Panel -->
        <div class="flex min-w-[394px] max-w-[394px] flex-col self-start rounded-lg border border-gray-200 bg-white">
            <!-- Lead Information -->
            <div class="flex w-full flex-col gap-2 border-b border-gray-200 p-4">
                <!-- Breadcrums -->
                <div class="flex items-center justify-between">
                    <x-admin::breadcrumbs name="leads" />

                    <div class="flex gap-1">
                        <button class="icon-left-arrow rtl:icon-right-arrow rounded-md p-1 text-2xl transition-all hover:bg-gray-100"></button>
                        <button class="icon-right-arrow rtl:icon-right-arrow rounded-md p-1 text-2xl transition-all hover:bg-gray-100"></button>
                    </div>
                </div>

                <!-- Tags -->
                @include ('admin::leads.view.tags')

                <!-- Title -->
                <h3 class="text-lg font-bold">
                    {{ $lead->title }}
                </h1>

                <!-- Activity Actions -->
                <div class="flex flex-wrap gap-2">
                    <!-- Mail Activity Action -->
                    @include ('admin::leads.view.activities.actions.mail')

                    <!-- File Activity Action -->
                    @include ('admin::leads.view.activities.actions.file')

                    <!-- Note Activity Action -->
                    @include ('admin::leads.view.activities.actions.note')

                    <!-- Activity Action -->
                    @include ('admin::leads.view.activities.actions.activity')
                </div>
            </div>
            
            <!-- Lead Attributes -->
            @include ('admin::leads.view.attributes')

            <!-- Contact Person -->
            @include ('admin::leads.view.person')
        </div>
        
        <!-- Right Panel -->
        <div class="flex w-full flex-col gap-4 rounded-lg">
            <!-- Stages Navigation -->
            @include ('admin::leads.view.stages')

            <!-- Stages Navigation -->
            @include ('admin::leads.view.activities.index')
        </div>
    </div>    
</x-admin::layouts>