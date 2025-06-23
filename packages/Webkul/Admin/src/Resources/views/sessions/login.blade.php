<x-admin::layouts.anonymous>
    <!-- Page Title -->
    <x-slot:title>
        @lang('admin::app.users.login.title')
    </x-slot>
    <div class="flex container-login flex-col items-center justify-center gap-10  ">
        <div class="flex   items-center gap-5 login-container p-4 rounded-2xl">
            <!-- Logo -->
           
            <img alt="Ofoghe talaei" src="{{vite()->asset('images/landing-logo.svg')}}" class="w-1/2" >

            <div class="w-full h-full">
                {!! view_render_event('admin.sessions.login.form_controls.before') !!}
                <div class="w-full h-full min-h-[400px] b rounded-2xl p-8 shadow-lg flex min-w-[300px] flex-col  bg-white dark:bg-gray-900">
                <!-- Login Form -->
                <x-admin::form :action="route('admin.session.store')" class="h-full">
                    <p class="mt-4 ml-4 p-4 text-3xl font-bold text-gray-800 dark:text-white">
                        @lang('admin::app.users.login.title') 
                    </p>
                    <div class="flex flex-col justify-between  gap-6">

                    <div class="  p-4 dark:border-gray-800">
                        <!-- Email -->
                        <x-admin::form.control-group>
                            <x-admin::form.control-group.label class="block text-sm font-medium text-gray-700 required">
                                @lang('admin::app.users.login.email')
                            </x-admin::form.control-group.label>

                            <x-admin::form.control-group.control
                                type="email"
                                class="mt-1 block w-full rounded-md border border-gray-300 shadow-sm px-4 py-2 focus:ring-2 focus:ring-blue-500 focus:outline-none focus:border-blue-500 placeholder-gray-400"
                                id="email"
                                name="email"
                                rules="required|email"
                                :label="trans('admin::app.users.login.email')"
                                :placeholder="trans('admin::app.users.login.email')"
                            />

                            <x-admin::form.control-group.error control-name="email" />
                        </x-admin::form.control-group>

                        <!-- Password -->
                        <x-admin::form.control-group class="relative w-full">
                            <x-admin::form.control-group.label class=" block text-sm font-medium text-gray-700 required">
                                @lang('admin::app.users.login.password')
                            </x-admin::form.control-group.label>

                            <x-admin::form.control-group.control
                                type="password"
                                class="mt-1 block w-full rounded-md border border-gray-300 shadow-sm px-4 py-2 focus:ring-2 focus:ring-blue-500 focus:outline-none focus:border-blue-500 placeholder-gray-400 ltr:pr-10 rtl:pl-10"
                                id="password"
                                name="password"
                                rules="required|min:6"
                                :label="trans('admin::app.users.login.password')"
                                :placeholder="trans('admin::app.users.login.password')"
                            />

                            <span
                                class="icon-eye-hide absolute top-11 -translate-y-2/4 cursor-pointer text-2xl ltr:right-3 rtl:left-3"
                                onclick="switchVisibility()"
                                id="visibilityIcon"
                                role="presentation"
                                tabindex="0"
                            >
                            </span>

                            <x-admin::form.control-group.error control-name="password" />
                        </x-admin::form.control-group>
                    </div>

                    <div class="flex flex-col gap-4 items-center justify-between p-4">
                        <!-- Submit Button -->
                        <button
                            class="primary-button w-full py-2 rounded-md  "
                            aria-label="{{ trans('admin::app.users.login.submit-btn')}}"
                        >
                            @lang('admin::app.users.login.submit-btn')
                        </button>
                          <!-- Forgot Password Link -->
                        <a
                            class="cursor-pointer text-xs font-semibold leading-6 text-brandColor"
                            href="{{ route('admin.forgot_password.create') }}"
                        >
                            @lang('admin::app.users.login.forget-password-link')
                        </a>
                    </div>
                    </div>
                </x-admin::form>
                </div>
                {!! view_render_event('admin.sessions.login.form_controls.after') !!}
            </div>
        </div>

        <!-- Powered By -->
{{--        <div class="text-sm font-normal">--}}
{{--            @lang('admin::app.components.layouts.powered-by.description', [--}}
{{--                'krayin' => '<a class="text-brandColor hover:underline " href="https://krayincrm.com/">Krayin</a>',--}}
{{--                'webkul' => '<a class="text-brandColor hover:underline " href="https://webkul.com/">Webkul</a>',--}}
{{--            ])--}}
{{--        </div>--}}
    </div>
    @push('scripts')
        <script>
            function switchVisibility() {
                let passwordField = document.getElementById("password");
                let visibilityIcon = document.getElementById("visibilityIcon");

                passwordField.type = passwordField.type === "password" ? "text" : "password";
                visibilityIcon.classList.toggle("icon-eye");
            }
        </script>
    @endpush
</x-admin::layouts.anonymous>
