<x-admin::layouts>
    <x-slot:title>
        @lang('admin::app.settings.attributes.create.title')
    </x-slot>

    {!! view_render_event('admin.settings.attributes.create.before') !!}

    <!-- Input Form -->
    <x-admin::form
        :action="route('admin.settings.attributes.store')"
        enctype="multipart/form-data"
    >
        <div class="flex flex-col gap-4">
            {!! view_render_event('admin.settings.attributes.create.form_controls.before') !!}

            <!-- actions buttons -->
            <div class="flex items-center justify-between rounded-lg border border-gray-200 bg-white px-4 py-2 text-sm dark:border-gray-800 dark:bg-gray-900 dark:text-gray-300">
                <div class="flex flex-col gap-2">
                    {!! view_render_event('admin.settings.attributes.create.breadcrumbs.before') !!}

                    <x-admin::breadcrumbs name="settings.attributes.create" />

                    {!! view_render_event('admin.settings.attributes.create.breadcrumbs.after') !!}
                    
                    <div class="text-xl font-bold dark:text-white">
                        {!! view_render_event('admin.settings.attributes.create.title.before') !!}

                        @lang('admin::app.settings.attributes.create.title')

                        {!! view_render_event('admin.settings.attributes.create.title.after') !!}
                    </div>
                </div>

                <div class="flex items-center gap-x-2.5">
                    <!-- Create button for Attributes -->
                    <div class="flex items-center gap-x-2.5">
                        {!! view_render_event('admin.settings.attributes.create.create_button.before') !!}

                        @if (bouncer()->hasPermission('settings.automation.attributes.create'))
                            <button
                                type="submit"
                                class="primary-button"
                            >
                                @lang('admin::app.settings.attributes.create.save-btn')
                            </button>
                        @endif

                        {!! view_render_event('admin.settings.attributes.create.create_button.after') !!}
                    </div>
                </div>
            </div>
            
            <!-- Create Attributes Vue Components -->
            <v-create-attributes>
                <!-- Shimmer Effect -->
                <x-admin::shimmer.settings.attributes />
            </v-create-attributes>

            {!! view_render_event('admin.settings.attributes.create.form_controls.after') !!}
        </div>
    </x-admin::form>

    {!! view_render_event('admin.settings.attributes.create.after') !!}

    @pushOnce('scripts')
        <script
            type="text/x-template"
            id="v-create-attributes-template"
        >
            <!-- body content -->
            {!! view_render_event('admin.settings.attributes.create.card.label.before') !!}
            
            <div class="flex gap-2.5 max-xl:flex-wrap">
                <!-- Left sub Component -->
                <div class="flex flex-1 flex-col gap-2 max-xl:flex-auto">
                    <div class="box-shadow rounded-lg border border-gray-200 bg-white p-4 dark:border-gray-800 dark:bg-gray-900">
                        <p class="mb-4 text-base font-semibold text-gray-800 dark:text-white">
                            @lang('admin::app.settings.attributes.create.labels')
                        </p>

                        {!! view_render_event('admin.settings.attributes.create.form_controls.name.before') !!}

                        <!-- Name -->
                        <x-admin::form.control-group>
                            <x-admin::form.control-group.label class="required">
                                @lang('admin::app.settings.attributes.create.name')
                            </x-admin::form.control-group.label>

                            <x-admin::form.control-group.control
                                type="text"
                                id="name"
                                name="name"
                                rules="required"
                                value="{{ old('name') }}"
                                :label="trans('admin::app.settings.attributes.create.name')"
                                :placeholder="trans('admin::app.settings.attributes.create.name')"
                            />

                            <x-admin::form.control-group.error control-name="name" />
                        </x-admin::form.control-group>

                        {!! view_render_event('admin.settings.attributes.create.form_controls.name.after') !!}

                        <!-- Options -->
                        <div
                            v-if="swatchAttribute && (
                                    attributeType == 'select'
                                    || attributeType == 'multiselect'
                                    || attributeType == 'checkbox'
                                    || attributeType == 'lookup'
                                )"
                        >
                            <div class="mb-3 flex items-center justify-between">
                                <p class="mb-4 text-base font-semibold text-gray-800 dark:text-white">
                                    @lang('admin::app.settings.attributes.create.options')
                                </p>

                                <!-- Add Row Button -->
                                <div
                                    v-if="optionType == 'options' && attributeType != 'lookup'"
                                    class="secondary-button text-sm"
                                    @click="$refs.addOptionsRow.toggle();swatchValue=''"
                                >
                                    @lang('admin::app.settings.attributes.create.add-option')
                                </div>
                            </div>

                            <!-- For Attribute Options If Data Exist -->
                            <div class="mt-4 overflow-x-auto">
                                <div class="flex gap-4 max-sm:flex-wrap">
                                    {!! view_render_event('admin.settings.attributes.create.form_controls.option_type.before') !!}

                                    <!-- Input Option Type -->
                                    <x-admin::form.control-group v-if="attributeType != 'lookup'" class="mb-2.5 w-1/2">
                                        <x-admin::form.control-group.label>
                                            @lang('admin::app.settings.attributes.create.option-type')
                                        </x-admin::form.control-group.label>
                                
                                        <x-admin::form.control-group.control
                                            type="select"
                                            id="optionType"
                                            name="option_type"
                                            :value="old('optionType')"
                                            v-model="optionType"
                                            @change="showSwatch=true"
                                        >
                                            <option value="lookup">
                                                @lang('admin::app.settings.attributes.create.lookup')
                                            </option>
                                            <option value="options">
                                                @lang('admin::app.settings.attributes.create.options')
                                            </option>
                                        </x-admin::form.control-group.control>
                                
                                        <x-admin::form.control-group.error
                                            class="mt-3"
                                            control-name="admin"
                                        />
                                    </x-admin::form.control-group>
                                
                                    {!! view_render_event('admin.settings.attributes.create.form_controls.option_type.after') !!}

                                    {!! view_render_event('admin.settings.attributes.create.form_controls.lookup_type.before') !!}

                                    <!-- Input Lookup Type -->
                                    <x-admin::form.control-group v-if="attributeType == 'lookup' || (optionType == 'lookup')" class="mb-2.5 w-1/2">
                                        <x-admin::form.control-group.label>
                                            @lang('admin::app.settings.attributes.create.lookup-type')
                                        </x-admin::form.control-group.label>
                                
                                        <x-admin::form.control-group.control
                                            type="select"
                                            id="lookup_type"
                                            name="lookup_type"
                                            :value="old('optionType')"
                                            @change="showSwatch=true"
                                        >
                                            <option
                                                :key="index"
                                                :value="index"
                                                v-text="entityType.name"
                                                v-for="(entityType, index) in lookupEntityTypes"
                                            ></option>
                                        </x-admin::form.control-group.control>
                                
                                        <x-admin::form.control-group.error
                                            class="mt-3"
                                            control-name="admin"
                                        />
                                    </x-admin::form.control-group>

                                    {!! view_render_event('admin.settings.attributes.create.form_controls.lookup_type.after') !!}
                                </div>

                                <template v-if="this.options?.length && optionType == 'options'">
                                    {!! view_render_event('admin.settings.attributes.create.table.before') !!}

                                    <!-- Table Information -->
                                    <x-admin::table>
                                        <x-admin::table.thead class="text-sm font-medium dark:bg-gray-800">
                                            <x-admin::table.thead.tr>
                                                <x-admin::table.th class="!p-0" />

                                                <!-- Admin tables heading -->
                                                <x-admin::table.th>
                                                    @lang('admin::app.settings.attributes.create.option-name')
                                                </x-admin::table.th>

                                                <x-admin::table.th>
                                                    @lang('admin::app.settings.attributes.edit.actions')
                                                </x-admin::table.th>
                                            </x-admin::table.thead.tr>
                                        </x-admin::table.thead>

                                        <!-- Draggable Component -->
                                        <draggable
                                            tag="tbody"
                                            ghost-class="draggable-ghost"
                                            handle=".icon-move"
                                            v-bind="{animation: 200}"
                                            :list="options"
                                            item-key="id"
                                        >
                                            <template #item="{ element, index }">
                                                <x-admin::table.thead.tr class="hover:bg-gray-50 dark:hover:bg-gray-950">
                                                    <!-- Draggable Icon -->
                                                    <x-admin::table.td class="!px-0 text-center">
                                                        <i class="icon-move cursor-grab text-xl transition-all group-hover:text-gray-700"></i>

                                                        <input
                                                            type="hidden"
                                                            :name="'options[' + element.id + '][position]'"
                                                            :value="index"
                                                        />
                                                    </x-admin::table.td>

                                                    <!-- Admin -->
                                                    <x-admin::table.td>
                                                        <p class="dark:text-white">
                                                            @{{ element.params.name }}
                                                        </p>

                                                        <input
                                                            type="hidden"
                                                            :name="'options[' + element.id + '][name]'"
                                                            v-model="element.params.name"
                                                        />
                                                    </x-admin::table.td>

                                                    <!-- Actions button -->
                                                    <x-admin::table.td class="!px-0">
                                                        <span
                                                            class="icon-edit cursor-pointer rounded-md p-1.5 text-2xl transition-all hover:bg-gray-200 dark:hover:bg-gray-800 max-sm:place-self-center"
                                                            @click="editModal(element)"
                                                        >
                                                        </span>

                                                        <span
                                                            class="icon-delete cursor-pointer rounded-md p-1.5 text-2xl transition-all hover:bg-gray-200 dark:hover:bg-gray-800 max-sm:place-self-center"
                                                            @click="removeOption(element.id)"
                                                        >
                                                        </span>
                                                    </x-admin::table.td>
                                                </x-admin::table.thead.tr>
                                            </template>
                                        </draggable>
                                    </x-admin::table>

                                    {!! view_render_event('admin.settings.attributes.create.table.after') !!}
                                </template>
                            </div>
                        </div>
                    </div>
                </div>

                {!! view_render_event('admin.settings.attributes.create.card.label.after') !!}

                {!! view_render_event('admin.settings.attributes.create.card.general.before') !!}

                <!-- Right sub-component -->
                <div class="flex w-[360px] max-w-full flex-col gap-2">
                    {!! view_render_event('admin.settings.attributes.create.accordion.general.before') !!}

                    <!-- General -->
                    <x-admin::accordion>
                        <x-slot:header>
                            <p class="p-2.5 text-base font-semibold text-gray-800 dark:text-white">
                                @lang('admin::app.settings.attributes.create.general')
                            </p>
                        </x-slot>

                        <x-slot:content>
                            {!! view_render_event('admin.settings.attributes.create.form_controls.code.before') !!}

                            <!-- Code -->
                            <x-admin::form.control-group>
                                <x-admin::form.control-group.label class="required">
                                    @lang('admin::app.settings.attributes.create.code')
                                </x-admin::form.control-group.label>

                                <x-admin::form.control-group.control
                                    type="text"
                                    id="code"
                                    name="code"
                                    rules="required"
                                    value="{{ old('code') }}"
                                    :label="trans('admin::app.settings.attributes.create.code')"
                                    :placeholder="trans('admin::app.settings.attributes.create.code')"
                                />

                                <x-admin::form.control-group.error control-name="code" />
                            </x-admin::form.control-group>

                            {!! view_render_event('admin.settings.attributes.create.form_controls.code.after') !!}

                            {!! view_render_event('admin.settings.attributes.create.form_controls.select.before') !!}

                            <!-- Attribute Type -->
                            <x-admin::form.control-group>
                                <x-admin::form.control-group.label class="required">
                                    @lang('admin::app.settings.attributes.create.type')
                                </x-admin::form.control-group.label>

                                <x-admin::form.control-group.control
                                    type="select"
                                    id="type"
                                    class="cursor-pointer"
                                    name="type"
                                    rules="required"
                                    :value="old('type')"
                                    v-model="attributeType"
                                    :label="trans('admin::app.settings.attributes.create.type')"
                                    @change="swatchAttribute=true"
                                >
                                    <!-- Here! All Needed types are defined -->
                                    @foreach(['text', 'textarea', 'price', 'boolean', 'select', 'multiselect', 'checkbox', 'email', 'address', 'phone', 'lookup', 'datetime', 'date', 'image', 'file'] as $type)
                                        <option
                                            value="{{ $type }}"
                                            {{ $type === 'text' ? "selected" : '' }}
                                        >
                                            @lang('admin::app.settings.attributes.create.'. $type)
                                        </option>
                                    @endforeach
                                </x-admin::form.control-group.control>

                                <x-admin::form.control-group.error control-name="type" />
                            </x-admin::form.control-group>

                            {!! view_render_event('admin.settings.attributes.create.form_controls.select.after') !!}

                            {!! view_render_event('admin.settings.attributes.create.form_controls.entity_type.before') !!}

                            <!-- Entity Type -->
                            <x-admin::form.control-group>
                                <x-admin::form.control-group.label class="required">
                                    @lang('admin::app.settings.attributes.create.entity-type')
                                </x-admin::form.control-group.label>

                                <x-admin::form.control-group.control
                                    type="select"
                                    id="entity_type"
                                    name="entity_type"
                                    rules="required"
                                    value="{{ old('entity_type') }}"
                                    :label="trans('admin::app.settings.attributes.create.entity-type')"
                                    :placeholder="trans('admin::app.settings.attributes.create.entity-type')"
                                >

                                    @foreach (config('attribute_entity_types') as $key => $entityType)
                                        <option value="{{ $key }}">{{ trans($entityType['name']) }}</option>
                                    @endforeach
                                </x-admin::form.control-group.control>
                                    
                                <x-admin::form.control-group.error control-name="entity_type" />
                            </x-admin::form.control-group>

                            {!! view_render_event('admin.settings.attributes.create.form_controls.entity_type.after') !!}
                        </x-slot>
                    </x-admin::accordion>
                    
                    {!! view_render_event('admin.settings.attributes.create.accordion.general.after') !!}

                    {!! view_render_event('admin.settings.attributes.create.accordion.validation.before') !!}

                    <!-- Validations -->
                    <x-admin::accordion>
                        <x-slot:header>
                            <p class="p-2.5 text-base font-semibold text-gray-800 dark:text-white">
                                @lang('admin::app.settings.attributes.create.validations')
                            </p>
                        </x-slot>

                        <x-slot:content>
                            {!! view_render_event('admin.settings.attributes.create.form_controls.validation.before') !!}

                            <!-- Input Validation -->
                            <x-admin::form.control-group v-if="swatchAttribute && (attributeType == 'text')">
                                <x-admin::form.control-group.label>
                                    @lang('admin::app.settings.attributes.create.input-validation')
                                </x-admin::form.control-group.label>

                                <x-admin::form.control-group.control
                                    type="select"
                                    class="cursor-pointer"
                                    id="validation"
                                    name="validation"
                                    :value="old('validation')"
                                    v-model="validationType"
                                    :label="trans('admin::app.settings.attributes.create.input-validation')"
                                    refs="validation"
                                    @change="inputValidation=true"
                                >
                                    <!-- Here! All Needed types are defined -->
                                    @foreach(['numeric', 'email', 'decimal', 'url'] as $type)
                                        <option value="{{ $type }}">
                                            @lang('admin::app.settings.attributes.create.' . $type)
                                        </option>
                                    @endforeach
                                </x-admin::form.control-group.control>

                                <x-admin::form.control-group.error control-name="validation" />
                            </x-admin::form.control-group>

                            {!! view_render_event('admin.settings.attributes.create.form_controls.validation.after') !!}

                            {!! view_render_event('admin.settings.attributes.create.form_controls.is_required.before') !!}

                            <!-- Is Required -->
                                <x-admin::form.control-group class="!mb-2 flex items-center gap-2.5">
                                <x-admin::form.control-group.control
                                    type="checkbox"
                                    id="is_required"
                                    name="is_required"
                                    value="1"
                                    for="is_required"
                                />

                                <label
                                    class="cursor-pointer text-xs font-medium text-gray-600 dark:text-gray-300"
                                    for="is_required"
                                >
                                    @lang('admin::app.settings.attributes.create.is-required')
                                </label>
                            </x-admin::form.control-group>

                            {!! view_render_event('admin.settings.attributes.create.form_controls.is_required.after') !!}

                            {!! view_render_event('admin.settings.attributes.create.form_controls.is_unique.before') !!}

                            <!-- Is Unique -->
                            <x-admin::form.control-group class="!mb-0 flex select-none items-center gap-2.5">
                                <x-admin::form.control-group.control
                                    type="checkbox"
                                    id="is_unique"
                                    name="is_unique"
                                    value="1"
                                    for="is_unique"
                                />

                                <label
                                    class="cursor-pointer text-xs font-medium text-gray-600 dark:text-gray-300"
                                    for="is_unique"
                                >
                                    @lang('admin::app.settings.attributes.create.is-unique')
                                </label>
                            </x-admin::form.control-group>

                            {!! view_render_event('admin.settings.attributes.create.form_controls.is_unique.after') !!}
                        </x-slot>
                    </x-admin::accordion>

                    {!! view_render_event('admin.settings.attributes.create.accordion.validation.after') !!}
                </div>
            </div>
            
            {!! view_render_event('admin.settings.attributes.create.card.general.after') !!}

            {!! view_render_event('admin.settings.attributes.create.modal.before') !!}

            <!-- Add Options Model Form -->
            <x-admin::form
                v-slot="{ meta, errors, handleSubmit }"
                as="div"
                ref="modelForm"
            >
                <form
                    @submit.prevent="handleSubmit($event, storeOptions)"
                    enctype="multipart/form-data"
                    ref="createOptionsForm"
                >
                    <x-admin::modal
                        @toggle="listenModal"
                        ref="addOptionsRow"
                    >
                        <!-- Modal Header !-->
                        <x-slot:header>
                            <p class="text-lg font-bold text-gray-800 dark:text-white">
                                @lang('admin::app.settings.attributes.create.add-option')
                            </p>
                        </x-slot>

                        <!-- Modal Content !-->
                        <x-slot:content>
                            <!-- Hidden Id Input -->
                            <x-admin::form.control-group.control
                                type="hidden"
                                name="id"
                            />

                            {!! view_render_event('admin.settings.attributes.create.form_controls.name.before') !!}

                            <!-- Admin Input -->
                            <x-admin::form.control-group class="mb-2.5 w-full">
                                <x-admin::form.control-group.label class="required">
                                    @lang('admin::app.settings.attributes.create.option-name')
                                </x-admin::form.control-group.label>

                                <x-admin::form.control-group.control
                                    type="text"
                                    name="name"
                                    rules="required"
                                    :label="trans('admin::app.settings.attributes.create.option-name')"
                                    :placeholder="trans('admin::app.settings.attributes.create.option-name')"
                                />

                                <x-admin::form.control-group.error control-name="name" />
                            </x-admin::form.control-group>

                            {!! view_render_event('admin.settings.attributes.create.form_controls.name.after') !!}
                        </x-slot>

                        <!-- Modal Footer !-->
                        <x-slot:footer>
                            <button
                                type="submit"
                                class="primary-button"
                            >
                                @lang('admin::app.settings.attributes.create.save-btn')
                            </button>
                        </x-slot>
                    </x-admin::modal>
                </form>
            </x-admin::form>

            {!! view_render_event('admin.settings.attributes.create.modal.after') !!}
        </script>

        <script type="module">
            app.component('v-create-attributes', {
                template: '#v-create-attributes-template',

                data() {
                    return {
                        optionRowCount: 1,

                        attributeType: '',

                        validationType: '',

                        inputValidation: false,

                        optionType: '',

                        swatchAttribute: false,

                        showSwatch: false,

                        isNullOptionChecked: false,

                        options: [],

                        swatchValue: [],

                        lookupEntityTypes: @json(config('attribute_lookups')),
                    }
                },

                computed: {
                    isFilterableDisabled() {
                        return this.attributeType == 'lookup' || this.attributeType == 'checkbox'
                            || this.attributeType == 'select' || this.attributeType == 'multiselect'
                            ? false : true;
                    },
                },

                methods: {
                    storeOptions(params, { resetForm }) {
                        if (params.id) {
                            let foundIndex = this.options.findIndex(item => item.id === params.id);

                            this.options.splice(foundIndex, 1, {
                                ...this.options[foundIndex],
                                params: {
                                    ...this.options[foundIndex].params,
                                    ...params,
                                }
                            });
                        } else {
                            this.options.push({
                                id: 'option_' + this.optionRowCount,
                                params
                            });

                            params.id = 'option_' + this.optionRowCount;
                            this.optionRowCount++;
                        }

                        let formData = new FormData(this.$refs.createOptionsForm);

                        const sliderImage = formData.get("swatch_value[]");

                        if (sliderImage) {
                            params.swatch_value = sliderImage;
                        }

                        this.$refs.addOptionsRow.toggle();

                        if (params.swatch_value instanceof File) {
                            this.setFile(params);
                        }

                        resetForm();
                    },

                    editModal(values) {
                        values.params.id = values.id;

                        this.$refs.modelForm.setValues(values.params);

                        this.$refs.addOptionsRow.toggle();
                    },

                    removeOption(id) {
                        this.$emitter.emit('open-confirm-modal', {
                            agree: () => {
                                this.options = this.options.filter(option => option.id !== id);

                                this.$emitter.emit('add-flash', { type: 'success', message: "@lang('admin::app.catalog.attributes.create.option-deleted')" });
                            }
                        });
                    },

                    listenModal(event) {
                        if (! event.isActive) {
                            this.isNullOptionChecked = false;
                        }
                    },

                    setFile(event) {
                        let dataTransfer = new DataTransfer();

                        dataTransfer.items.add(event.swatch_value);
                    }
                },
            });
        </script>
    @endPushOnce
</x-admin::layouts>
