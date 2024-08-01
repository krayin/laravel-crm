<!-- Lead Activities Vue Component -->
<v-lead-activities></v-lead-activities>

@pushOnce('scripts')
    <script type="text/x-template" id="v-lead-activities-tempalte">
        <div class="w-full bg-white">
            <div class="flex gap-4 border-b border-gray-200">
                <div
                    v-for="type in types"
                    class="cursor-pointer px-4 py-2.5 text-sm font-medium text-gray-800"
                    :class="{'border-brandColor border-b-2 !text-brandColor transition': selectedType == type.name }"
                    @click="selectedType = type.name"
                >
                    @{{ type.label }}
                </div>
            </div>

            <div class="animate-[on-fade_0.5s_ease-in-out] p-4">
                Hello there
            </div>
        </div>
    </script>

    <script type="module">
        app.component('v-lead-activities', {
            template: '#v-lead-activities-tempalte',

            data() {
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
                this.get();
            },

            methods: {
                get() {
                    this.$axios.get("{{ route('admin.leads.activities.index', $lead->id) }}")
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