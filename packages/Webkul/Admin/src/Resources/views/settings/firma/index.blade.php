<x-admin::layouts>
    <x-slot:title>
        @lang('admin::app.firma.settings.title')
    </x-slot>

    <div class="flex flex-col gap-4">
        <div class="flex items-center justify-between rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm shadow-sm dark:border-gray-800 dark:bg-gray-900 dark:text-gray-300">
            <div class="flex flex-col gap-2">
                <x-admin::breadcrumbs name="settings.firma" />

                <div class="text-xl font-bold dark:text-white">
                    @lang('admin::app.firma.settings.title')
                </div>
            </div>
        </div>

        {!! view_render_event('admin.settings.firma.index.form.before') !!}

        <!-- Connection -->
        <div class="rounded-lg border border-gray-300 bg-white p-4 dark:border-gray-800 dark:bg-gray-900">
            <p class="mb-4 text-gray-600 dark:text-gray-300">
                @lang('admin::app.firma.settings.info')
            </p>

            <form
                method="POST"
                action="{{ route('admin.settings.firma.update') }}"
            >
                @csrf

                <div class="mb-4">
                    <label
                        class="mb-1.5 block text-xs font-medium text-gray-800 dark:text-white"
                        for="api_key"
                    >
                        @lang('admin::app.firma.settings.api-key')
                    </label>

                    <input
                        id="api_key"
                        name="api_key"
                        type="password"
                        autocomplete="off"
                        class="w-full rounded border border-gray-300 px-2.5 py-2 text-sm font-normal text-gray-800 transition-all hover:border-gray-400 focus:border-gray-400 dark:border-gray-800 dark:bg-gray-900 dark:text-gray-300 dark:hover:border-gray-400 dark:focus:border-gray-400"
                    />

                    <p class="mt-1 text-xs text-gray-500">
                        @if ($apiKeyHint)
                            @lang('admin::app.firma.settings.api-key-stored', ['hint' => $apiKeyHint])
                        @else
                            @lang('admin::app.firma.settings.api-key-missing')
                        @endif
                    </p>

                    @if ($apiKeyHint)
                        <label class="mt-1 flex items-center gap-1 text-xs text-gray-600 dark:text-gray-300">
                            <input
                                type="checkbox"
                                name="remove_api_key"
                                value="1"
                            />

                            @lang('admin::app.firma.settings.remove')
                        </label>
                    @endif
                </div>

                <div class="mb-4">
                    <label
                        class="mb-1.5 block text-xs font-medium text-gray-800 dark:text-white"
                        for="webhook_secret"
                    >
                        @lang('admin::app.firma.settings.webhook-secret')
                    </label>

                    <input
                        id="webhook_secret"
                        name="webhook_secret"
                        type="password"
                        autocomplete="off"
                        class="w-full rounded border border-gray-300 px-2.5 py-2 text-sm font-normal text-gray-800 transition-all hover:border-gray-400 focus:border-gray-400 dark:border-gray-800 dark:bg-gray-900 dark:text-gray-300 dark:hover:border-gray-400 dark:focus:border-gray-400"
                    />

                    <p class="mt-1 text-xs text-gray-500">
                        @if ($hasWebhookSecret)
                            @lang('admin::app.firma.settings.webhook-secret-stored')
                        @else
                            @lang('admin::app.firma.settings.webhook-secret-missing')
                        @endif
                    </p>

                    @if ($hasWebhookSecret)
                        <label class="mt-1 flex items-center gap-1 text-xs text-gray-600 dark:text-gray-300">
                            <input
                                type="checkbox"
                                name="remove_webhook_secret"
                                value="1"
                            />

                            @lang('admin::app.firma.settings.remove')
                        </label>
                    @endif
                </div>

                <button
                    type="submit"
                    class="primary-button"
                >
                    @lang('admin::app.firma.settings.save-btn')
                </button>
            </form>
        </div>

        <!-- Webhook -->
        <div class="rounded-lg border border-gray-300 bg-white p-4 dark:border-gray-800 dark:bg-gray-900">
            <p class="mb-2 text-base font-semibold dark:text-white">
                @lang('admin::app.firma.settings.webhook-title')
            </p>

            <p class="mb-2 text-gray-600 dark:text-gray-300">
                @lang('admin::app.firma.settings.webhook-url', ['url' => $webhookUrl])
            </p>

            @if ($webhookId)
                <p class="mb-2 text-gray-600 dark:text-gray-300">
                    @lang('admin::app.firma.settings.webhook-id', ['id' => $webhookId])
                </p>
            @endif

            <p class="mb-4 text-gray-600 dark:text-gray-300">
                @lang('admin::app.firma.settings.enable-webhooks')
            </p>

            <div class="flex gap-2">
                <form
                    method="POST"
                    action="{{ route('admin.settings.firma.test') }}"
                >
                    @csrf

                    <button
                        type="submit"
                        class="secondary-button"
                    >
                        @lang('admin::app.firma.settings.test-btn')
                    </button>
                </form>

                <form
                    method="POST"
                    action="{{ route('admin.settings.firma.register_webhook') }}"
                >
                    @csrf

                    <button
                        type="submit"
                        class="secondary-button"
                    >
                        @lang('admin::app.firma.settings.register-btn')
                    </button>
                </form>
            </div>
        </div>

        {!! view_render_event('admin.settings.firma.index.form.after') !!}
    </div>
</x-admin::layouts>
