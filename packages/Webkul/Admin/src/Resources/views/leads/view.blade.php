<x-admin::layouts>
    <x-slot:title>
        @lang('admin::app.leads.index.title')
    </x-slot>

    <!-- Content -->
    <div class="flex gap-4">
        <!-- Left Panel -->
        <div class="flex min-w-96 max-w-96 flex-col rounded-lg border border-gray-200 bg-white">
            <!-- Lead Information -->
            <div class="flex w-full flex-col gap-2 border-b border-gray-200 p-4">
                <!-- Breadcrums -->
                <div class="flex items-center justify-between">
                    <x-admin::breadcrumbs name="leads" />

                    <div class="flex gap-1">
                        <button class="icon-left-arrow rtl:icon-right-arrow rounded-md p-1 text-2xl transition-all hover:bg-gray-200"></button>
                        <button class="icon-right-arrow rtl:icon-right-arrow rounded-md p-1 text-2xl transition-all hover:bg-gray-200"></button>
                    </div>
                </div>

                <!-- Tags -->
                <div class="flex items-center gap-1">
                    <span class="rounded-md bg-rose-100 px-3 py-1 text-xs font-medium text-rose-700">Urgent</span>
                    <span class="rounded-md bg-sky-100 px-3 py-1 text-xs font-medium text-sky-600">Hot Lead</span>
                </div>

                <!-- Title -->
                <h3 class="text-lg font-bold">
                    Urgent Requirement: 1,000 High-Performance Motherboards for Rapchika
                </h1>

                <!-- Activity Actions -->
                <div class="flex gap-2">
                    <!-- Mail Button -->
                    <button
                        class="flex h-[74px] w-[84px] flex-col items-center justify-center gap-1 rounded-lg bg-green-200 text-green-900"
                        @click="$refs.mailActivityModal.open()"
                    >
                        <span class="icon-mail text-2xl"></span>

                        Mail
                    </button>

                    <!-- Call Button -->
                    <button class="flex h-[74px] w-[84px] flex-col items-center justify-center gap-1 rounded-lg bg-sky-200 text-sky-900">
                        <span class="icon-call text-2xl"></span>

                        Call
                    </button>

                    <!-- Note Button -->
                    <button class="flex h-[74px] w-[84px] flex-col items-center justify-center gap-1 rounded-lg bg-orange-200 text-orange-800">
                        <span class="icon-note text-2xl"></span>

                        Note
                    </button>

                    <!-- Activity Button -->
                    <button class="flex h-[74px] w-[84px] flex-col items-center justify-center gap-1 rounded-lg bg-blue-200 text-blue-800">
                        <span class="icon-activity text-2xl"></span>

                        Activity
                    </button>
                </div>
            </div>
            
            <!-- Lead Attributes -->
            <div class="flex w-full flex-col gap-4 border-b border-gray-200 p-4">
                <h4 class="font-semibold">About Lead</h4>

                <div class="flex flex-col gap-1">
                    <div class="grid grid-cols-[1fr_2fr]">
                        <div class="">Name</div>
                        <div class="font-medium">Jitendra</div>
                    </div>
                    
                    <div class="grid grid-cols-[1fr_2fr]">
                        <div class="">Lead Value</div>
                        <div class="font-medium">$10000</div>
                    </div>
                </div>
            </div>
            
            <!-- Contact Person -->
            <div class="flex w-full flex-col gap-4 border-b border-gray-200 p-4">
                <h4 class="font-semibold">About Contact Person</h4>

                <div class="flex flex-col gap-1">
                    <div class="grid grid-cols-[1fr_2fr]">
                        <div class="">Name</div>
                        <div class="font-medium">Jitendra</div>
                    </div>
                    
                    <div class="grid grid-cols-[1fr_2fr]">
                        <div class="">Lead Value</div>
                        <div class="font-medium">$10000</div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Right Panel -->
        <div class="flex w-full flex-col gap-4 rounded-lg bg-white">
            <x-admin::tabs>
                <x-admin::tabs.item title="Tab 1" is-selected="true">
                    Tab 1 Content
                </x-admin::tabs.item>

                <x-admin::tabs.item title="Tab 2">
                    Tab 2 Content
                </x-admin::tabs.item>
            </x-admin::tabs>
        </div>

        <x-admin::modal ref="mailActivityModal" position="bottom-right">
            <x-slot:header>
                Header
            </x-slot>

            <x-slot:content>
                Content
            </x-slot>
        </x-admin::modal>
    </div>    
</x-admin::layouts>