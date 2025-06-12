<x-admin::layouts.anonymous>
    <!-- Page Title -->
    <x-slot:title>
        @lang('admin::app.users.forget-password.create.page-title')
    </x-slot>

    <div class="flex h-[100vh] flex-col items-center justify-center gap-10">
        <div class="flex login-container p-4 rounded-2xl items-center gap-5">
            <!-- Logo -->
             <img alt="Ofoghe talaei" src="{{vite()->asset('images/landing-logo.svg')}}" class="w-1/2" >

            <div class="box-shadow flex m-auto w-full min-w-[300px] flex-col rounded-md bg-white dark:bg-gray-900">
                {!! view_render_event('admin.sessions.forgor_password.form_controls.before') !!}

                <!-- Forget Password Form -->
                <x-admin::form :action="route('admin.forgot_password.store')" class="h-full">
                    <div class="p-4">
                        <p class="text-xl text-center font-bold text-gray-800 dark:text-white">
                            @lang('admin::app.users.forget-password.create.title')
                        </p>
                    </div>

                    <div class=" p-4 dark:border-gray-800">
                        <!-- Registered Email -->
                        <x-admin::form.control-group>
                            <x-admin::form.control-group.label class="block text-sm font-medium text-gray-700 required">
                                @lang('admin::app.users.forget-password.create.email')
                            </x-admin::form.control-group.label>

                            <x-admin::form.control-group.control
                                type="email"
                                class="w-[254px] max-w-full mt-1 block w-full rounded-md border border-gray-300 shadow-sm px-4 py-2 focus:ring-2 focus:ring-blue-500 focus:outline-none focus:border-blue-500 placeholder-gray-400 ltr:pr-10 rtl:pl-10"
                                id="email"
                                name="email"
                                rules="required|email"
                                :value="old('email')"
                                :label="trans('admin::app.users.forget-password.create.email')"
                                :placeholder="trans('admin::app.users.forget-password.create.email')"
                            />

                            <x-admin::form.control-group.error control-name="email" />
                        </x-admin::form.control-group>
                    </div>

                    <div class="flex flex-col gap-4 items-center justify-between p-4">

                        <!-- Form Submit Button -->
                        <button
                            class="primary-button w-full">
                            @lang('admin::app.users.forget-password.create.submit-btn')
                        </button>
                        <!-- Back to Sign In link -->
                        <a
                            class="cursor-pointer text-xs font-semibold leading-6 text-brandColor"
                            href="{{ route('admin.session.create') }}"
                        >
                            @lang('admin::app.users.forget-password.create.sign-in-link')
                        </a>
                    </div>
                </x-admin::form>

                {!! view_render_event('admin.sessions.forgor_password.form_controls.after') !!}
            </div>
        </div>

        <!-- Powered By -->
{{--        <div class="text-sm font-normal">--}}
{{--            @lang('admin::app.components.layouts.powered-by.description', [--}}
{{--                'krayin' => '<a class="text-brandColor hover:underline " href="https://krayincrm.com/">Krayin</a>',--}}
{{--                'webkul' => '<a class="text-brandColor hover:underline " href="https://webkul.com/">Webkul</a>',--}}
{{--            ]) --}}
{{--        </div>--}}
    </div>
</x-admin::layouts.anonymous>
