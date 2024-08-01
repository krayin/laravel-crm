<!-- Activity Button -->
<div class="">
    <button
        class="flex h-[74px] w-[84px] flex-col items-center justify-center gap-1 rounded-lg bg-blue-200 text-blue-800"
        @click="$refs.leadActionComponent.openModal('mail')"
    >
        <span class="icon-activity text-2xl"></span>

        @lang('admin::app.leads.view.activities.actions.activity.btn')
    </button>

    <!-- Lead Note Action Vue Component -->
    <v-lead-activity ref="leadActionComponent"></v-lead-activity>
</div>

@pushOnce('scripts')
    <script type="text/x-template" id="v-lead-activity-tempalte">
        <x-admin::form
            v-slot="{ meta, errors, handleSubmit }"
            as="div"
            ref="modalForm"
        >
            <form @submit="handleSubmit($event, updateOrCreate)">
                <x-admin::modal ref="mailActivityModal" position="bottom-right">
                    <x-slot:header>
                        <x-admin::dropdown>
                            <x-slot:toggle>
                                <h3 class="flex cursor-pointer items-center gap-1 text-base font-semibold">
                                    @lang('admin::app.leads.view.activities.actions.activity.title') - @{{ selectedType.label }}

                                    <span class="icon-down-arrow text-2xl"></span>
                                </h3>
                            </x-slot>

                            <x-slot:menu>
                                <x-admin::dropdown.menu.item
                                    ::class="{ 'bg-gray-100': selectedType.value === type.value }"
                                    v-for="type in availableTypes"
                                    @click="selectedType = type"
                                >
                                    @{{ type.label }}
                                </x-admin::dropdown.menu.item>
                            </x-slot>
                        </x-admin::dropdown>
                    </x-slot>

                    <x-slot:content>
                        <input type="hidden" name="type" :value="selectedType.value" />
                        
                        <!-- Title -->
                        <x-admin::form.control-group>
                            <x-admin::form.control-group.label class="required">
                                @lang('admin::app.leads.view.activities.actions.activity.title-control')
                            </x-admin::form.control-group.label>
                            
                            <x-admin::form.control-group.control
                                type="text"
                                name="title"
                                rules="required"
                                :label="trans('admin::app.leads.view.activities.actions.activity.title-control')"
                            />

                            <x-admin::form.control-group.error control-name="title" />
                        </x-admin::form.control-group>

                        <!-- Description -->
                        <x-admin::form.control-group>
                            <x-admin::form.control-group.label>
                                @lang('admin::app.leads.view.activities.actions.activity.description')
                            </x-admin::form.control-group.label>
                            
                            <x-admin::form.control-group.control
                                type="textarea"
                                name="comment"
                            />
                        </x-admin::form.control-group>

                        <!-- Schedule Date -->
                        <div class="flex gap-4">
                            <!-- Started From -->
                            <x-admin::form.control-group class="w-full">
                                <x-admin::form.control-group.label class="required">
                                    @lang('admin::app.leads.view.activities.actions.activity.schedule-from')
                                </x-admin::form.control-group.label>
                                
                                <x-admin::form.control-group.control
                                    type="date"
                                    name="schedule_from"
                                    rules="required"
                                    :label="trans('admin::app.leads.view.activities.actions.activity.schedule-from')"
                                />

                                <x-admin::form.control-group.error control-name="schedule_from" />
                            </x-admin::form.control-group>
                            
                            <!-- Started To -->
                            <x-admin::form.control-group class="w-full">
                                <x-admin::form.control-group.label class="required">
                                    @lang('admin::app.leads.view.activities.actions.activity.schedule-to')
                                </x-admin::form.control-group.label>
                                
                                <x-admin::form.control-group.control
                                    type="date"
                                    name="schedule_to"
                                    rules="required"
                                    :label="trans('admin::app.leads.view.activities.actions.activity.schedule-to')"
                                />

                                <x-admin::form.control-group.error control-name="schedule_to" />
                            </x-admin::form.control-group>
                        </div>

                        <!-- Location -->
                        <x-admin::form.control-group>
                            <x-admin::form.control-group.label>
                                @lang('admin::app.leads.view.activities.actions.activity.location')
                            </x-admin::form.control-group.label>
                            
                            <x-admin::form.control-group.control
                                type="text"
                                name="location"
                            />
                        </x-admin::form.control-group>
                    </x-slot>

                    <x-slot:footer>
                        <button
                            type="submit"
                            class="primary-button"
                        >
                            @lang('admin::app.leads.view.activities.actions.activity.save-btn')
                        </button>
                    </x-slot>
                </x-admin::modal>
            </form>
        </x-admin::form>
    </script>

    <script type="module">
        app.component('v-lead-activity', {
            template: '#v-lead-activity-tempalte',

            data: function () {
                return {
                    selectedType: {
                        label: "{{ trans('admin::app.leads.view.activities.actions.activity.call') }}",
                        value: 'call'
                    },

                    availableTypes: [
                        {
                            label: "{{ trans('admin::app.leads.view.activities.actions.activity.call') }}",
                            value: 'call'
                        }, {
                            label: "{{ trans('admin::app.leads.view.activities.actions.activity.meeting') }}",
                            value: 'meeting'
                        }, {
                            label: "{{ trans('admin::app.leads.view.activities.actions.activity.lunch') }}",
                            value: 'task'
                        },
                    ]
                }
            },

            methods: {
                openModal(type) {
                    this.$refs.mailActivityModal.open();
                }
            }
        });
    </script>
@endPushOnce