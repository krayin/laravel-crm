<x-admin::layouts>
    <x-slot:title>
        @lang('admin::app.settings.google-contacts.index.title')
    </x-slot>

    <div class="flex flex-col gap-4">
        <div class="flex items-center justify-between rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm shadow-sm dark:border-gray-800 dark:bg-gray-900 dark:text-gray-300">
            <div class="flex flex-col gap-2">
                <x-admin::breadcrumbs name="settings.google_contacts" />

                <div class="text-xl font-bold dark:text-white">
                    @lang('admin::app.settings.google-contacts.index.title')
                </div>
            </div>
        </div>

        <div class="rounded-lg border border-gray-300 bg-white p-4 dark:border-gray-800 dark:bg-gray-900">
            @if ($account)
                <p class="mb-4 text-gray-600 dark:text-gray-300">
                    {{ trans('admin::app.settings.google-contacts.index.connected-info', ['email' => $account->google_email]) }}
                </p>

                <form
                    method="POST"
                    action="{{ route('admin.settings.google_contacts.disconnect') }}"
                >
                    @csrf

                    <button
                        type="submit"
                        class="secondary-button"
                    >
                        @lang('admin::app.settings.google-contacts.index.disconnect-btn')
                    </button>
                </form>
            @else
                <p class="mb-4 text-gray-600 dark:text-gray-300">
                    @lang('admin::app.settings.google-contacts.index.not-connected-info')
                </p>

                <a
                    href="{{ route('admin.settings.google_contacts.connect') }}"
                    class="primary-button"
                >
                    @lang('admin::app.settings.google-contacts.index.connect-btn')
                </a>
            @endif
        </div>
    </div>
</x-admin::layouts>
