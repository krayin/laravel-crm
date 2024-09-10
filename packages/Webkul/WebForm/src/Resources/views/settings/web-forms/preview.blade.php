<x-web_form::layouts>
    <x-slot:title>
        @lang('Form Preview')
    </x-slot>

    <v-preview></v-preview>

    @pushOnce('scripts')
        <script
            type="text/x-template"
            id="v-preview-template"
        >
            <div class="flex h-[100vh] items-center justify-center">
                <div class="flex flex-col items-center gap-5">
                    <img
                        class="h-10 w-[110px]"
                        src="{{ vite()->asset('images/logo.svg') }}"
                        alt="krayin"
                    />
        
                    <div class="box-shadow flex min-w-[300px] flex-col rounded-md bg-white dark:bg-gray-900">
                        <x-web_form::form
                            v-slot="{ meta, values, errors, handleSubmit }"
                            as="div"
                        >
                            <form @submit="handleSubmit($event, create)">
                                <p class="p-4 text-xl font-bold text-gray-800 dark:text-white">
                                    {{ $webForm->title }}
                                </p>

                                <div class="flex flex-col border-y p-4 dark:border-gray-800">
                                    @include('web_form::settings.web-forms.controls')

                                    <button
                                        class="primary-button"
                                        aria-label="{{ trans('admin::app.users.login.submit-btn')}}"
                                    >
                                        @lang('admin::app.users.login.submit-btn')
                                    </button>
                                </div>
                            </form>
                        </x-web_form::form>
                    </div>
                </div>
            </div>
        </script>

        <script type="module">
            app.component('v-preview', {
                template: '#v-preview-template',

                mounted() {
                    console.log('mounted');
                },

                methods: {
                    create(params) {
                        console.log(params);
                    }
                }
            });
        </script>
    @endPushOnce
</x-web_form::layouts>