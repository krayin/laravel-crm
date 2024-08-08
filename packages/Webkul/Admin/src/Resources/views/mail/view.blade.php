<x-admin::layouts>
   <v-email-list></v-email-list>

    @pushOnce('scripts')
        <script
            type="text/x-template"
            id="v-email-list-template"
        >

            {{-- <div class="email-list">
                <email-item-component
                    :email="email"
                    :key="0"
                    :index="0"
                    @onEmailAction="emailAction($event)"
                ></email-item-component>


                <div class="email-reply-list">
                    <email-item-component
                        v-for='(email, index) in email.emails'
                        :email="email"
                        :key="index + 1"
                        :index="index + 1"
                        @onEmailAction="emailAction($event)"
                    ></email-item-component>
                </div>

                <div class="email-action" v-if="! action">
                    <span class="reply-button" @click="emailAction({'type': 'reply'})">
                        <i class="icon reply-icon"></i>

                        {{ __('admin::app.mail.reply') }}
                    </span>

                    <span class="forward-button" @click="emailAction({'type': 'forward'})">
                        <i class="icon forward-icon"></i>

                        {{ __('admin::app.mail.forward') }}
                    </span>
                </div>

                <div class="email-form-container" id="email-form-container" v-else>
                    <email-form
                        :action="action"
                        @onDiscard="discard($event)"
                    ></email-form>
                </div>
            </div> --}}
        </script>

        <script type="module">
            app.component('v-email-list', {
                template: '#v-email-list-template',

                data() {
                    return {
                        email: @json($email),

                        action: null,
                    };
                },
                
                methods: {
                    emailAction(event) {
                        this.action = event;

                        if (! this.action.email) {
                            this.action.email = this.lastEmail();
                        }

                        setTimeout(() => this.scrollBottom(), 0);
                    },

                    scrollBottom() {
                        const scrollBottom = window.scrollY + window.innerHeight;

                        window.scrollTo({
                            top: scrollBottom,
                            behavior: 'smooth',
                        });
                    },

                    lastEmail() {
                        if (
                            this.email.emails === undefined 
                            || ! this.email.emails.length
                        ) {
                            return this.email;
                        }

                        return this.email.emails[this.email.emails.length - 1];
                    },

                    discard() {
                        this.action = null;
                    },
                },
            });
        </script>
    @endPushOnce
</x-admin::layouts>