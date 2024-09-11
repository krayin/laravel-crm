<x-web_form::layouts>
    <x-slot:title>
        {{ $webForm->title }}
    </x-slot>

    <!-- Web Form -->
    <v-web-form>
        <div class="flex h-[100vh] items-center justify-center">
            <div class="flex flex-col items-center gap-5">
                <x-web_form::spinner />
            </div>
        </div>
    </v-web-form>

    @pushOnce('scripts')
        <script
            type="text/template"
            id="v-web-form-template"
        >
            <div 
                class="flex h-[100vh] items-center justify-center"
                style="background-color: {{ $webForm->background_color }}"
            >
                <div class="flex flex-col items-center gap-5">
                    <!-- Logo -->
                    <img
                        class="w-max"
                        src="{{ vite()->asset('images/logo.svg') }}"
                        alt="{{ config('app.name') }}"
                    />

                    <h1 style="color: {{ $webForm->form_title_color }} !important;">{{ $webForm->title }}</h1>

                    <p>{{ $webForm->description }}</p>

                    <div 
                        class="box-shadow flex min-w-[300px] flex-col rounded-lg border border-gray-200 bg-white p-4 dark:bg-gray-900"
                        style="background-color: {{ $webForm->form_background_color }}"
                    >
                        {!! view_render_event('web_forms.web_forms.form_controls.before', ['webForm' => $webForm]) !!}
            
                        <!-- Webform Form -->
                        <x-web_form::form
                            v-slot="{ meta, values, errors, handleSubmit }"
                            as="div"
                            ref="modalForm"
                        >
                            <form 
                                @submit="handleSubmit($event, create)"
                                ref="webForm"
                            >
                                @include('web_form::settings.web-forms.controls')

                                <div class="flex justify-center">
                                    <x-web_form::button
                                        class="primary-button"
                                        :title="$webForm->submit_button_label"
                                        ::loading="isStoring"
                                        ::disabled="isStoring"
                                        style="background-color: {{ $webForm->form_submit_button_color }} !important"
                                    />
                                </div>
                            </form>
                        </x-web_form::form>

                        {!! view_render_event('web_forms.web_forms.form_controls.after', ['webForm' => $webForm]) !!}
                    </div>
                </div>
            </div>
        </script>

        <script type="module">
            app.component('v-web-form', {
                template: '#v-web-form-template',

                data() {
                    return {
                        isStoring: false,
                    };
                },

                methods: {
                    create(params, { resetForm }) {
                        this.isStoring = true;

                        const formData = new FormData(this.$refs.webForm);
                        
                        this.$axios.post('{{ route('admin.settings.web_forms.form_store', $webForm->id) }}', formData, {
                            headers: {
                                'Content-Type': 'multipart/form-data',
                            },
                        })
                            .then(response => {
                                resetForm();

                                this.$refs.webForm.reset();

                                this.$emitter.emit('add-flash', { type: 'success', message: response.data.message });
                            })
                            .catch(error => {
                                if (error.response.data.redirect) {
                                    window.location.href = error.response.data.redirect;

                                    return;
                                }
                                
                                this.$emitter.emit('add-flash', { type: 'error', message: error.response.data.message });
                            })
                            .finally(() => {
                                this.isStoring = false;
                            });
                    }
                }
            });
        </script>
    @endPushOnce
</x-web_form::layouts>
