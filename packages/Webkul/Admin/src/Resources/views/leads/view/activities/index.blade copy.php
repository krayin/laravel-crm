<v-lead-activities></v-lead-activities>

<x-admin::tabs class="bg-white">
    <x-admin::tabs.item title="Tab 1" is-selected="true">
        <!-- Activity List -->
        <div class="flex flex-col gap-4">
            @for ($i = 0; $i < 2; $i++)
                <!-- Activity Item -->
                <div class="flex gap-2">
                    <div class="flex h-9 w-9 items-center justify-center rounded-full bg-orange-200 text-xs font-medium text-orange-800">
                        <span class="icon-note text-xl"></span>
                    </div>
                    
                    <!-- Activity Details -->
                    <div class="flex w-full justify-between px-4">
                        <div class="flex flex-col gap-2">
                            <!-- Activity Title -->
                            <p class="font-medium">
                                Contacted the client and shared pricing details
                            </p>

                            <!-- Activity Description -->
                            <p class="">
                                I am sharing the pricing details with the client. Please review the same and let me know if we can proceed further.
                            </p>
                            
                            <!-- Activity Time and User -->
                            <div class="text-gray-500">15 Jul 2024, 5:31 PM, by Profile User</div>
                        </div>

                        <button
                            class="icon-more flex h-7 w-7 cursor-pointer items-center justify-center rounded-md text-2xl transition-all hover:bg-gray-200"
                        ></button>                                
                    </div>
                </div>

                <!-- Activity Item -->
                <div class="flex gap-2">
                    <div class="mt-2 flex h-9 w-9 items-center justify-center rounded-full bg-blue-200 text-xs font-medium text-blue-800">
                        <span class="icon-activity text-xl"></span>
                    </div>
                    
                    <!-- Activity Details -->
                    <div class="flex w-full justify-between rounded-md bg-gray-100 p-4">
                        <div class="flex flex-col gap-2">
                            <!-- Activity Title -->
                            <p class="">
                                I am attaching the quotation for the requirement
                            </p>

                            <!-- Activity Description -->
                            <p class="text-gray-500">
                                I am attaching the quotation for the requirement. Please review the same and let me know if we can proceed further.
                            </p>

                            <!-- Attachments -->
                            <div class="flex gap-2">
                                <div class="flex cursor-pointer items-center gap-1 rounded-md p-1.5">
                                    <span class="icon-attachmetent text-xl"></span>
                                    <span class="font-medium text-brandColor">Quotation-1.pdf</span>
                                </div>
                                
                                <div class="flex cursor-pointer items-center gap-1 rounded-md p-1.5">
                                    <span class="icon-attachmetent text-xl"></span>
                                    <span class="font-medium text-brandColor">Quotation-2.pdf</span>
                                </div>
                            </div>

                            <!-- Activity Time and User -->
                            <div class="text-gray-500">15 Jul 2024, 5:31 PM, by Profile User</div>
                        </div>

                        <button
                            class="icon-more flex h-7 w-7 cursor-pointer items-center justify-center rounded-md text-2xl transition-all hover:bg-gray-200"
                        ></button>                                   
                    </div>
                </div>

                <!-- Activity Item -->
                <div class="flex gap-2">
                    <div class="flex h-9 w-9 items-center justify-center rounded-full bg-cyan-200 text-xs font-medium text-cyan-800">
                        <span class="icon-call text-xl"></span>
                    </div>
                    
                    <!-- Activity Details -->
                    <div class="flex w-full justify-between px-4">
                        <div class="flex flex-col gap-2">
                            <!-- Activity Title -->
                            <p class="font-medium">
                                Scheduled a call with the client
                            </p>

                            <!-- Activity Description -->
                            <p class="">
                                I have scheduled a call with the client for tomorrow at 11:00 AM. Please make sure you are available.
                            </p>
                            
                            <!-- Activity Time and User -->
                            <div class="text-gray-500">15 Jul 2024, 5:31 PM, by Profile User</div>
                        </div>

                        <button
                            class="icon-more flex h-7 w-7 cursor-pointer items-center justify-center rounded-md text-2xl transition-all hover:bg-gray-200"
                        ></button>                                  
                    </div>
                </div>

                <!-- Activity Item -->
                <div class="flex gap-2">
                    <div class="flex h-9 w-9 items-center justify-center rounded-full bg-green-200 text-xs font-medium text-green-900">
                        <span class="icon-mail text-xl"></span>
                    </div>
                    
                    <!-- Activity Details -->
                    <div class="flex w-full justify-between rounded-md bg-gray-100 p-4">
                        <div class="flex flex-col gap-2">
                            <div class="flex flex-col gap-1">
                                <!-- Activity Title -->
                                <p class="font-medium">
                                    Subject : Assistance Needed for Updating Statistics
                                </p>

                                <!-- Activity Title -->
                                <p class="">
                                    To : jitendra@webkul.in
                                </p>

                                <!-- Activity Title -->
                                <p class="">
                                    CC : jitendra@webkul.in
                                </p>
                            </div>

                            <!-- Activity Description -->
                            <p class="">
                                Dear, I hope this message finds you well?<br><br>

                                We kindly request your assistance in updating the following statistic. Could you please let us know where we need to make this update? Your prompt response will help us ensure the accuracy of our information.<br><br>

                                Thank you for your cooperation.<br>
                                Best regards, Webkul Team
                            </p>

                            <!-- Attachments -->
                            <div class="flex gap-2">
                                <div class="flex cursor-pointer items-center gap-1 rounded-md p-1.5">
                                    <span class="icon-attachmetent text-xl"></span>
                                    <span class="font-medium text-brandColor">Quotation-1.pdf</span>
                                </div>
                                
                                <div class="flex cursor-pointer items-center gap-1 rounded-md p-1.5">
                                    <span class="icon-attachmetent text-xl"></span>
                                    <span class="font-medium text-brandColor">Quotation-2.pdf</span>
                                </div>
                            </div>
                            
                            <!-- Activity Time and User -->
                            <div class="text-gray-500">15 Jul 2024, 5:31 PM, by Profile User</div>
                        </div>

                        <button
                            class="icon-more flex h-7 w-7 cursor-pointer items-center justify-center rounded-md text-2xl transition-all hover:bg-gray-200"
                        ></button>                                  
                    </div>
                </div>
            @endfor
        </div>
    </x-admin::tabs.item>

    <x-admin::tabs.item title="Tab 2">
    </x-admin::tabs.item>
</x-admin::tabs>

@pushOnce('scripts')
    <script type="text/x-template" id="v-lead-activities-tempalte">
        <x-admin::tabs class="bg-white">
            <x-admin::tabs.item
                v-for="type in types"
                ::title="type.name"
                ::is-selected="selectedType === type.name"
            >
                Testing
            </x-admin::tabs.item>
        </x-admin::tabs>
    </script>

    <script type="module">
        app.component('v-lead-activities', {
            template: '#v-lead-activities-tempalte',

            data: function () {
                return {
                    activities: [],

                    selectedType: 'all',

                    types: [
                        {
                            name: 'all',
                            label: "{{ trans('admin::app.leads.view.activities.index.all') }}",
                        }, {
                            name: 'note',
                            label: "{{ trans('admin::app.leads.view.activities.index.notes') }}",
                        }, {
                            name: 'call',
                            label: "{{ trans('admin::app.leads.view.activities.index.calls') }}",
                        }, {
                            name: 'meeting',
                            label: "{{ trans('admin::app.leads.view.activities.index.meetings') }}",
                        }, {
                            name: 'lunch',
                            label: "{{ trans('admin::app.leads.view.activities.index.lunches') }}",
                        }, {
                            name: 'file',
                            label: "{{ trans('admin::app.leads.view.activities.index.files') }}",
                        }, {
                            name: 'email',
                            label: "{{ trans('admin::app.leads.view.activities.index.emails') }}",
                        }
                    ],
                }
            },

            mounted() {
                //this.get();
            },

            methods: {
                get() {
                    this.$axios.get('admin.leads.activities.index')
                        .then(response => {
                            this.activities = response.data;
                        })
                        .catch(error => {
                            console.error(error);
                        });
                }
            }
        });
    </script>
@endPushOnce