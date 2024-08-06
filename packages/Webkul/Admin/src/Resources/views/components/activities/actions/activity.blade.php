@props([
    'entity'            => null,
    'entityControlName' => null,
])

<!-- Activity Button -->
<div class="">
    <button
        class="flex h-[74px] w-[84px] flex-col items-center justify-center gap-1 rounded-lg bg-blue-200 text-blue-800"
        @click="$refs.actionComponent.openModal('mail')"
    >
        <span class="icon-activity text-2xl"></span>

        @lang('admin::app.components.activities.actions.activity.btn')
    </button>

    <!-- Note Action Vue Component -->
    <v-activity
        ref="actionComponent"
        :entity="{{ json_encode($entity) }}"
        :entity-control-name="{{ $entityControlName }}"
    ></v-activity>
</div>

@pushOnce('scripts')
    <script type="text/x-template" id="v-activity-template">
        <x-admin::form
            v-slot="{ meta, errors, handleSubmit }"
            as="div"
            ref="modalForm"
        >
            <form @submit="handleSubmit($event, save)">
                <x-admin::modal ref="mailActivityModal" position="bottom-right">
                    <x-slot:header>
                        <x-admin::dropdown>
                            <x-slot:toggle>
                                <h3 class="flex cursor-pointer items-center gap-1 text-base font-semibold">
                                    @lang('admin::app.components.activities.actions.activity.title') - @{{ selectedType.label }}

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
                        <!-- Activity Type -->
                        <x-admin::form.control-group.control
                            type="hidden"
                            name="type"
                            ::value="selectedType.value"
                        />
                        
                        <!-- Id -->
                        <x-admin::form.control-group.control
                            type="hidden"
                            ::name="entityControlName"
                            ::value="entity.id"
                        />
                        
                        <!-- Title -->
                        <x-admin::form.control-group>
                            <x-admin::form.control-group.label class="required">
                                @lang('admin::app.components.activities.actions.activity.title-control')
                            </x-admin::form.control-group.label>
                            
                            <x-admin::form.control-group.control
                                type="text"
                                name="title"
                                rules="required"
                                :label="trans('admin::app.components.activities.actions.activity.title-control')"
                            />

                            <x-admin::form.control-group.error control-name="title" />
                        </x-admin::form.control-group>

                        <!-- Description -->
                        <x-admin::form.control-group>
                            <x-admin::form.control-group.label>
                                @lang('admin::app.components.activities.actions.activity.description')
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
                                    @lang('admin::app.components.activities.actions.activity.schedule-from')
                                </x-admin::form.control-group.label>
                                
                                <x-admin::form.control-group.control
                                    type="date"
                                    name="schedule_from"
                                    rules="required"
                                    :label="trans('admin::app.components.activities.actions.activity.schedule-from')"
                                />

                                <x-admin::form.control-group.error control-name="schedule_from" />
                            </x-admin::form.control-group>
                            
                            <!-- Started To -->
                            <x-admin::form.control-group class="w-full">
                                <x-admin::form.control-group.label class="required">
                                    @lang('admin::app.components.activities.actions.activity.schedule-to')
                                </x-admin::form.control-group.label>
                                
                                <x-admin::form.control-group.control
                                    type="date"
                                    name="schedule_to"
                                    rules="required"
                                    :label="trans('admin::app.components.activities.actions.activity.schedule-to')"
                                />

                                <x-admin::form.control-group.error control-name="schedule_to" />
                            </x-admin::form.control-group>
                        </div>

                        <!-- Location -->
                        <x-admin::form.control-group class="!mb-0">
                            <x-admin::form.control-group.label>
                                @lang('admin::app.components.activities.actions.activity.location')
                            </x-admin::form.control-group.label>
                            
                            <x-admin::form.control-group.control
                                type="text"
                                name="location"
                            />
                        </x-admin::form.control-group>
                    </x-slot>

                    <x-slot:footer>
                        <x-admin::button
                            class="primary-button"
                            :title="trans('admin::app.components.activities.actions.activity.save-btn')"
                            ::loading="isStoring"
                            ::disabled="isStoring"
                        />
                    </x-slot>
                </x-admin::modal>
            </form>
        </x-admin::form>
    </script>

    <script type="module">
        app.component('v-activity', {
            template: '#v-activity-template',

            props: {
                entity: {
                    type: Object,
                    required: true,
                    default: () => {}
                },

                entityControlName: {
                    type: String,
                    required: true,
                    default: ''
                }
            },

            data: function () {
                return {
                    isStoring: false,
                    
                    selectedType: {
                        label: "{{ trans('admin::app.components.activities.actions.activity.call') }}",
                        value: 'call'
                    },

                    availableTypes: [
                        {
                            label: "{{ trans('admin::app.components.activities.actions.activity.call') }}",
                            value: 'call'
                        }, {
                            label: "{{ trans('admin::app.components.activities.actions.activity.meeting') }}",
                            value: 'meeting'
                        }, {
                            label: "{{ trans('admin::app.components.activities.actions.activity.lunch') }}",
                            value: 'task'
                        },
                    ]
                }
            },

            methods: {
                openModal(type) {
                    this.$refs.mailActivityModal.open();
                },

                save(params) {
                    this.isStoring = true;

                    let self = this;

                    this.$axios.post("{{ route('admin.activities.store') }}", params)
                        .then (function(response) {
                            self.isStoring = false;

                            self.$emitter.emit('add-flash', { type: 'success', message: response.data.message });

                            self.$emitter.emit('on-activity-added', response.data.data);

                            self.$refs.mailActivityModal.close();
                        })
                        .catch (function (error) {
                            self.isStoring = false;

                            if (error.response.status == 422) {
                                setErrors(error.response.data.errors);
                            } else {
                                self.$emitter.emit('add-flash', { type: 'error', message: error.response.data.message });

                                self.$refs.mailActivityModal.close();
                            }
                        });
                },
            },
        });
    </script>
@endPushOnce