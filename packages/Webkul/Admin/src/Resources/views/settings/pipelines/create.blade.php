<x-admin::layouts>
    <!-- Page Title -->
    <x-slot:title>
        @lang('admin::app.settings.pipelines.create.title')
    </x-slot>

    {!! view_render_event('krayin.admin.settings.pipelines.create.form.before') !!}

    <x-admin::form
        :action="route('admin.settings.pipelines.store')"
        method="POST"
    >
        <div class="flex items-center justify-between rounded-lg border border-gray-200 bg-white px-4 py-2 text-sm dark:border-gray-800 dark:bg-gray-900 dark:text-gray-300">
            <div class="flex flex-col gap-2">
                <div class="flex cursor-pointer items-center">
                    <!-- Breadcrumbs -->
                    <x-admin::breadcrumbs name="settings.pipelines.create" />
                </div>

                <div class="text-xl font-bold dark:text-gray-300">
                    @lang('admin::app.settings.pipelines.create.title')
                </div>
            </div>

            <div class="flex items-center gap-x-2.5">
                <!-- Create button for Pipeline -->
                <div class="flex items-center gap-x-2.5">
                    <button
                        type="submit"
                        class="primary-button"
                    >
                        @lang('admin::app.settings.pipelines.create.save-btn')
                    </button>
                </div>
            </div>
        </div>

        <div class="mt-3.5 flex gap-2.5 max-xl:flex-wrap">
            <!-- Left sub-component -->
            <div class="flex flex-1 flex-col gap-2 max-xl:flex-auto">
                <div class="box-shadow rounded bg-white p-4 dark:bg-gray-900">
                    <p class="mb-4 text-base font-semibold text-gray-800 dark:text-white">
                        @lang('Stages')
                    </p>
                    
                    <v-stages-component></v-stages-component>
                </div>
            </div>

            <!-- Right sub-component -->
            <div class="flex w-[360px] max-w-full flex-col gap-2 max-sm:w-full">
                <x-admin::accordion>
                    <x-slot:header>
                        <div class="flex items-center justify-between">
                            <p class="p-2.5 text-base font-semibold text-gray-800 dark:text-white">
                                @lang('admin::app.settings.pipelines.create.general')
                            </p>
                        </div>
                    </x-slot>

                    <x-slot:content>
                        <x-admin::form.control-group>
                            <x-admin::form.control-group.label class="required">
                                @lang('admin::app.settings.pipelines.create.name')
                            </x-admin::form.control-group.label>
        
                            <x-admin::form.control-group.control
                                type="text"
                                name="name"
                                id="name"
                                rules="required"
                                :label="trans('admin::app.settings.pipelines.create.name')"
                                :placeholder="trans('admin::app.settings.pipelines.create.name')"
                                value="{{ old('name') }}"
                            />
        
                            <x-admin::form.control-group.error control-name="name" />
                        </x-admin::form.control-group>
    
                        <x-admin::form.control-group>
                            <x-admin::form.control-group.label class="required">
                                @lang('admin::app.settings.pipelines.create.rotten-days')
                            </x-admin::form.control-group.label>
        
                            <x-admin::form.control-group.control
                                type="text"
                                name="rotten_days"
                                id="rotten_days"
                                rules="required|numeric|min_value:1"
                                :label="trans('admin::app.settings.pipelines.create.rotten-days')"
                                :placeholder="trans('admin::app.settings.pipelines.create.rotten-days')"
                                value="{{ old('rotten_days') ?? 30 }}"
                            />
        
                            <x-admin::form.control-group.error control-name="rotten_days" />
                        </x-admin::form.control-group>
    
                        <x-admin::form.control-group class="!mb-0">
                            <x-admin::form.control-group.label>
                                @lang('admin::app.settings.pipelines.create.mark-as-default')
                            </x-admin::form.control-group.label>
    
                            <x-admin::form.control-group.control
                                type="switch"
                                class="cursor-pointer"
                                name="is_default"
                                id="is_default"
                                value="1"
                                :label="trans('admin::app.settings.pipelines.create.mark-as-default')"
                            />
    
                            <x-admin::form.control-group.error control-name="is_default" />
                        </x-admin::form.control-group>
                    </x-slot>
                </x-admin::accordion>
            </div>
        </div>
    </x-admin::form>

    {!! view_render_event('krayin.admin.settings.pipelines.create.form.after') !!}

    @pushOnce('scripts')
        <script
            type="text/x-template"
            id="v-stages-component-template"
        >
            <div>
                <x-admin::table>
                    <x-admin::table.thead class="text-sm font-medium dark:bg-gray-800">
                        <x-admin::table.thead.tr>
                            <x-admin::table.th class="!p-0"></x-admin::table.th>
                            
                            <x-admin::table.th>
                                <label class="required">
                                    @lang('admin::app.settings.pipelines.create.name')
                                </label>
                            </x-admin::table.th>
                            
                            <x-admin::table.th>
                                <label class="required">
                                    @lang('admin::app.settings.pipelines.create.probability')
                                </label>
                            </x-admin::table.th> 
                                                  
                            <x-admin::table.th></x-admin::table.th>
                        </x-admin::table.thead.tr>
                    </x-admin::table.thead>

                    <draggable
                        tag="tbody"
                        ghost-class="draggable-ghost"
                        :handle="isAnyDraggable ? '.icon-edit' : ''"
                        v-bind="{animation: 200}"
                        item-key="id"
                        :list="stages"
                        :move="handleDragging"
                    >
                        <template #item="{ element, index }">
                            <x-admin::table.thead.tr
                                class="hover:bg-gray-50 dark:hover:bg-gray-950"
                                ::class="{ draggable: isDragable(element) }"
                            >
                                <!-- Draggable Icon -->
                                <x-admin::table.td class="!px-0 text-center">
                                    <i
                                        v-if="isDragable(element)" 
                                        class="icon-edit cursor-grab text-xl transition-all group-hover:text-gray-700"
                                    ></i>
                                </x-admin::table.td>

                                <!-- Attribute Name -->
                                <x-admin::table.td>
                                    <input
                                        type="hidden"
                                        id="slug"
                                        :value="slugify(element.name)"
                                        :name="'stages[' + element.id + '][code]'"
                                    />

                                    <x-admin::form.control-group>
                                        <x-admin::form.control-group.control
                                            type="text"
                                            ::name="'stages[' + element.id + '][name]'"
                                            v-model="element['name']"
                                            ::rules="getValidation"
                                            :label="trans('admin::app.settings.pipelines.create.name')"
                                            ::readonly="! isDragable(element)"
                                        />

                                        <x-admin::form.control-group.error ::name="'stages[' + element.id + '][name]'" />
                                    </x-admin::form.control-group>

                                    <input
                                        type="hidden"
                                        :value="index + 1"
                                        :name="'stages[' + element.id + '][sort_order]'"
                                    />
                                </x-admin::table.td>

                                <x-admin::table.td>
                                    <x-admin::form.control-group>
                                        <x-admin::form.control-group.control
                                            type="text"
                                            ::name="'stages[' + element.id + '][probability]'"
                                            v-model="element['probability']"
                                            rules="required|numeric|min_value:0|max_value:100"
                                            ::readonly="element?.code != 'new' && ! isDragable(element)"
                                            :label="trans('admin::app.settings.pipelines.create.probability')"
                                        />
                                        <x-admin::form.control-group.error ::name="'stages[' + element.id + '][probability]'" />
                                    </x-admin::form.control-group>
                                </x-admin::table.td>

                                <x-admin::table.td>
                                    <i 
                                        class="icon-delete cursor-pointer text-2xl" 
                                        @click="removeStage(element)" 
                                        v-if="isDragable(element)"
                                    ></i>
                                </x-admin::table.td>
                            </x-admin::table.thead.tr>
                        </template>
                    </draggable>
                </x-admin::table>

                <button
                    class="primary-button"
                    @click="addStage"
                    type="button"
                >
                    @lang('admin::app.settings.pipelines.create.stage-btn')
                </button>
            </div>
        </script>

        <script type="module">
            app.component('v-stages-component', {
                template: '#v-stages-component-template',

                data() {
                    return {
                        stages: [{
                            'id': 'stage_1',
                            'code': 'new', 
                            'name': "@lang('admin::app.settings.pipelines.create.new-stage')",
                            'probability': 100
                        }, {
                            'id': 'stage_2',
                            'code': '',
                            'name': '',
                            'probability': 100
                        }, {
                            'id': 'stage_99',
                            'code': 'won',
                            'name': "{{ __('admin::app.settings.pipelines.create.won-stage') }}",
                            'probability': 100
                        }, {
                            'id': 'stage_100',
                            'code': 'lost',
                            'name': "{{ __('admin::app.settings.pipelines.create.lost-stage') }}",
                            'probability': 0
                        }],

                        stageCount: 3,

                        isAnyDraggable: true,
                    }
                },

                created() {
                    this.extendValidations();
                },

                computed: {
                    getValidation() {
                        return {
                            required: true,
                            unique_name: this.stages,
                        };
                    },
                },

                methods: {
                    addStage () {
                        this.stages.splice((this.stages.length - 2), 0, {
                            'id': 'stage_' + this.stageCount++,
                            'code': '',
                            'name': '',
                            'probability': 100
                        });
                    },

                    removeStage (stage) {
                        const index = this.stages.indexOf(stage);

                        if (index > -1) {
                            this.stages.splice(index, 1);
                        }

                        this.removeUniqueNameErrors();
                    },

                    isDragable (stage) {
                        if (stage.code == 'new' || stage.code == 'won' || stage.code == 'lost') {
                            return false;
                        }

                        return true;
                    },

                    slugify (name) {
                        return name
                            .toString()

                            .toLowerCase()

                            .replace(/[^\w\u0621-\u064A\u4e00-\u9fa5\u3402-\uFA6D\u3041-\u30A0\u30A0-\u31FF- ]+/g, '')

                            // replace whitespaces with dashes
                            .replace(/ +/g, '-')

                            // avoid having multiple dashes (---- translates into -)
                            .replace('![-\s]+!u', '-')

                            .trim();
                    },

                    extendValidations() {
                        defineRule('unique_name', (value, stages) => {
                            if (! value || !value.length) {
                                return true;
                            }

                            let filteredStages = stages.filter((stage) => {
                                return stage.name.toLowerCase() === value.toLowerCase();
                            });

                            if (filteredStages.length > 1) {
                                return '{!! __('admin::app.settings.pipelines.create.duplicate-name') !!}';
                            }

                            this.removeUniqueNameErrors();

                            return true;
                        });
                    },

                    isDuplicateStageNameExists() {
                        let stageNames = this.stages.map((stage) => stage.name);

                        return stageNames.some((name, index) => stageNames.indexOf(name) !== index);
                    },

                    removeUniqueNameErrors() {
                        if (!this.isDuplicateStageNameExists() && this.errors && Array.isArray(this.errors.items)) {
                            const uniqueNameErrorIds = this.errors.items
                                .filter(error => error.rule === 'unique_name')
                                .map(error => error.id);

                            uniqueNameErrorIds.forEach(id => this.errors.removeById(id));
                        }
                    },

                    handleDragging(event) {
                        const draggedElement = event.draggedContext.element;
                        
                        const relatedElement = event.relatedContext.element;

                        return this.isDragable(draggedElement) && this.isDragable(relatedElement);
                    },
                },
            })
        </script>
    @endPushOnce
</x-admin::layouts>