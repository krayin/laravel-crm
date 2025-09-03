<div>
    @if (bouncer()->hasPermission('leads.create')
        || bouncer()->hasPermission('quotes.create')
        || bouncer()->hasPermission('mail.create')
        || bouncer()->hasPermission('contacts.persons.create')
        || bouncer()->hasPermission('contacts.organizations.create')
        || bouncer()->hasPermission('products.create')
        || bouncer()->hasPermission('settings.automation.attributes.create')
        || bouncer()->hasPermission('settings.user.roles.create')
        || bouncer()->hasPermission('settings.user.users.create')
    )
        <x-admin::dropdown position="bottom-right">
            <x-slot:toggle>
                <!-- Toggle Button -->
                <button class="flex h-9 w-9 cursor-pointer items-center justify-center rounded-full bg-brandColor text-white">
                    <i class="icon-add text-2xl"></i>
                </button>
            </x-slot>

            <!-- Dropdown Content -->
            <x-slot:content class="mt-2 !p-0">
                <div class="relative px-2 py-4">
                    <div class="grid grid-cols-3 gap-2 text-center max-sm:grid-cols-2">
                        <!-- Link to create new Lead -->
                        @if (bouncer()->hasPermission('leads.create'))
                            <div class="rounded-lg bg-white p-2 hover:bg-gray-100 dark:bg-gray-800 dark:hover:bg-gray-950">
                                <a href="{{ route('admin.leads.create') }}">
                                    <div class="flex flex-col gap-1">
                                        <i class="icon-leads text-2xl text-gray-600"></i>

                                        <span class="font-medium dark:text-gray-300">@lang('admin::app.layouts.lead')</span>
                                    </div>
                                </a>
                            </div>
                        @endif

                        <!-- Link to create new Quotes -->
                        @if (bouncer()->hasPermission('quotes.create'))
                            <div class="rounded-lg bg-white p-2 hover:bg-gray-100 dark:bg-gray-800 dark:hover:bg-gray-950">
                                <a href="{{ route('admin.quotes.create') }}">
                                    <div class="flex flex-col gap-1">
                                        <i class="icon-quote text-2xl text-gray-600"></i>

                                        <span class="font-medium dark:text-gray-300">@lang('admin::app.layouts.quote')</span>
                                    </div>
                                </a>
                            </div>
                        @endif

                        <!-- Link to send new Mail-->
                        @if (bouncer()->hasPermission('mail.create'))
                            <div class="rounded-lg bg-white p-2 hover:bg-gray-100 dark:bg-gray-800 dark:hover:bg-gray-950">
                                <a href="{{ route('admin.mail.index', ['route' => 'inbox', 'openModal' => 'true']) }}">
                                    <div class="flex flex-col gap-1">
                                        <i class="icon-mail text-2xl text-gray-600"></i>

                                        <span class="font-medium dark:text-gray-300">@lang('admin::app.layouts.email')</span>
                                    </div>
                                </a>
                            </div>
                        @endif

                        <!-- Link to create new Person-->
                        @if (bouncer()->hasPermission('contacts.persons.create'))
                            <div class="rounded-lg bg-white p-2 hover:bg-gray-100 dark:bg-gray-800 dark:hover:bg-gray-950">
                                <a href="{{ route('admin.contacts.persons.create') }}">
                                    <div class="flex flex-col gap-1">
                                        <i class="icon-settings-user text-2xl text-gray-600"></i>

                                        <span class="font-medium dark:text-gray-300">@lang('admin::app.layouts.person')</span>
                                    </div>
                                </a>
                            </div>
                        @endif

                        <!-- Link to create new Organizations -->
                        @if (bouncer()->hasPermission('contacts.organizations.create'))
                            <div class="rounded-lg bg-white p-2 hover:bg-gray-100 dark:bg-gray-800 dark:hover:bg-gray-950">
                                <a href="{{ route('admin.contacts.organizations.create') }}">
                                    <div class="flex flex-col gap-1">
                                        <i class="icon-organization text-2xl text-gray-600"></i>

                                        <span class="font-medium dark:text-gray-300">@lang('admin::app.layouts.organization')</span>
                                    </div>
                                </a>
                            </div>
                        @endif

                        <!-- Link to create new Products -->
                        @if (bouncer()->hasPermission('products.create'))
                            <div class="rounded-lg bg-white p-2 hover:bg-gray-100 dark:bg-gray-800 dark:hover:bg-gray-950">
                                <a href="{{ route('admin.products.create') }}">
                                    <div class="flex flex-col gap-1">
                                        <i class="icon-product text-2xl text-gray-600"></i>

                                        <span class="font-medium dark:text-gray-300">@lang('admin::app.layouts.product')</span>
                                    </div>
                                </a>
                            </div>
                        @endif

                        <!-- Link to create new Attributes -->
                        @if (bouncer()->hasPermission('settings.automation.attributes.create'))
                            <div class="rounded-lg bg-white p-2 hover:bg-gray-100 dark:bg-gray-800 dark:hover:bg-gray-950">
                                <a href="{{ route('admin.settings.attributes.create') }}">
                                    <div class="flex flex-col gap-1">
                                        <i class="icon-attribute text-2xl text-gray-600"></i>

                                        <span class="font-medium dark:text-gray-300">@lang('admin::app.layouts.attribute')</span>
                                    </div>
                                </a>
                            </div>
                        @endif

                        <!-- Link to create new Role -->
                        @if (bouncer()->hasPermission('settings.user.roles.create'))
                            <div class="rounded-lg bg-white p-2 hover:bg-gray-100 dark:bg-gray-800 dark:hover:bg-gray-950">
                                <a href="{{ route('admin.settings.roles.create') }}">
                                    <div class="flex flex-col gap-1">
                                        <i class="icon-role text-2xl text-gray-600"></i>

                                        <span class="font-medium dark:text-gray-300">@lang('admin::app.layouts.role')</span>
                                    </div>
                                </a>
                            </div>
                        @endif

                        <!-- Link to create new User-->
                        @if (bouncer()->hasPermission('settings.user.users.create'))
                            <div class="rounded-lg bg-white p-2 hover:bg-gray-100 dark:bg-gray-800 dark:hover:bg-gray-950">
                                <a href="{{ route('admin.settings.users.index', ['action' => 'create']) }}">
                                    <div class="flex flex-col gap-1">
                                        <i class="icon-user text-2xl text-gray-600"></i>

                                        <span class="font-medium dark:text-gray-300">@lang('admin::app.layouts.user')</span>
                                    </div>
                                </a>
                            </div>
                        @endif
                    </div>
                </div>
            </x-slot>
        </x-admin::dropdown>
    @endif
</div>
