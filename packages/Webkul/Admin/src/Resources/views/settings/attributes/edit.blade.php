<x-admin::layouts>
    <x-slot:title>
        @lang('admin::app.settings.attributes.edit.title')
    </x-slot>

    {!! view_render_event('admin.catalog.attributes.edit.before', ['attribute' => $attribute]) !!}

    <!-- Input Form -->
    <x-admin::form
        :action="route('admin.settings.attributes.update', $attribute->id)"
        encType="multipart/form-data"
        method="PUT"
    >
        <div class="flex flex-col gap-4">
            {!! view_render_event('admin.settings.attributes.edit.form_controls.before', ['attribute' => $attribute]) !!}

            <!-- actions buttons -->
            <div class="flex items-center justify-between rounded-lg border border-gray-200 bg-white px-4 py-2 text-sm dark:border-gray-800 dark:bg-gray-900 dark:text-gray-300">
                <div class="flex flex-col gap-2">
                    {!! view_render_event('admin.settings.attributes.edit.breadcrumbs.before', ['attribute' => $attribute]) !!}

                    <x-admin::breadcrumbs 
                        name="settings.attributes.edit" 
                        :entity="$attribute"
                    />
                   
                    {!! view_render_event('admin.settings.attributes.edit.breadcrumbs.after', ['attribute' => $attribute]) !!}

                    <div class="text-xl font-bold dark:text-white">
                        {!! view_render_event('admin.settings.attributes.edit.title.before', ['attribute' => $attribute]) !!}

                        @lang('admin::app.settings.attributes.edit.title')

                        {!! view_render_event('admin.settings.attributes.edit.title.after', ['attribute' => $attribute]) !!}
                    </div>
                </div>

                <div class="flex items-center gap-x-2.5">
                    <!-- Create button for Attributes -->
                    <div class="flex items-center gap-x-2.5">
                        {!! view_render_event('admin.settings.attributes.edit.edit_button.before', ['attribute' => $attribute]) !!}

                        @if (bouncer()->hasPermission('settings.automation.attributes.edit'))
                            <button
                                type="submit"
                                class="primary-button"
                            >
                                @lang('admin::app.settings.attributes.edit.save-btn')
                            </button>
                        @endif

                        {!! view_render_event('admin.settings.attributes.edit.edit_button.after', ['attribute' => $attribute]) !!}
                    </div>
                </div>
            </div>

            <!-- Edit Attributes Vue Components -->
            <v-edit-attributes>
                <!-- Shimmer Effect -->
                <x-admin::shimmer.settings.attributes />
            </v-edit-attributes>

            {!! view_render_event('admin.settings.attributes.edit.form_controls.after', ['attribute' => $attribute]) !!}
        </div>
    </x-admin::form>

    {!! view_render_event('admin.catalog.attributes.edit.after', ['attribute' => $attribute]) !!}

    @pushOnce('scripts')
        <script
            type="text/x-template"
            id="v-edit-attributes-template"
        >
            <!-- body content -->
            <div class="flex gap-2.5 max-xl:flex-wrap">
                <!-- Left sub Component -->
                {!! view_render_event('admin.catalog.attributes.edit.card.label.before', ['attribute' => $attribute]) !!}
                
                <div class="flex flex-1 flex-col gap-2 max-xl:flex-auto">
                    <div class="box-shadow rounded-lg border border-gray-200 bg-white p-4 dark:border-gray-800 dark:bg-gray-900">
                        <p class="mb-4 text-base font-semibold text-gray-800 dark:text-white">
                            @lang('admin::app.settings.attributes.edit.labels')
                        </p>

                        {!! view_render_event('admin.settings.attributes.edit.form_controls.name.before', ['attribute' => $attribute]) !!}

                        <!-- Admin name -->
                        <x-admin::form.control-group>
                            <x-admin::form.control-group.label class="required">
                                @lang('admin::app.settings.attributes.edit.name')
                            </x-admin::form.control-group.label>

                            <x-admin::form.control-group.control
                                type="text"
                                name="name"
                                rules="required"
                                :value="old('name') ?: $attribute->name"
                                :label="trans('admin::app.settings.attributes.edit.name')"
                                :placeholder="trans('admin::app.settings.attributes.edit.name')"
                            />

                            <x-admin::form.control-group.error control-name="name" />
                        </x-admin::form.control-group>

                        {!! view_render_event('admin.settings.attributes.edit.form_controls.name.after', ['attribute' => $attribute]) !!}

                        <!-- Options -->
                        <div
                            class=" {{ in_array($attribute->type, ['select', 'multiselect', 'checkbox', 'lookup']) ?: 'hidden' }}"
                            v-if="showSwatch"
                        >
                            <div class="mb-3 flex items-center justify-between">
                                <p class="mb-4 text-base font-semibold text-gray-800 dark:text-white">
                                    @lang('admin::app.settings.attributes.edit.options')
                                </p>

                                <!-- Add Row Button -->
                                <div
                                    v-if="optionType == 'options' && attributeType != 'lookup'"
                                    class="secondary-button text-sm"
                                    @click="$refs.addOptionsRow.toggle();swatchValue=''"
                                >
                                    @lang('admin::app.settings.attributes.edit.add-option')
                                </div>
                            </div>

                            <!-- For Attribute Options If Data Exist -->
                            <div class="mt-4 overflow-x-auto">
                                <div class="flex gap-4 max-sm:flex-wrap">
                                    {!! view_render_event('admin.settings.attributes.edit.form_controls.option_type.before', ['attribute' => $attribute]) !!}

                                    <!-- Input Option Type -->
                                    <x-admin::form.control-group
                                        v-if="attributeType != 'lookup'"
                                        class="mb-2.5 w-1/2"
                                    >
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

                                    {!! view_render_event('admin.settings.attributes.edit.form_controls.option_type.after', ['attribute' => $attribute]) !!}

                                    {!! view_render_event('admin.settings.attributes.edit.form_controls.lookup_type.before', ['attribute' => $attribute]) !!}
                                
                                    <!-- Input Lookup Type -->
                                    <x-admin::form.control-group v-if="attributeType == 'lookup' || (optionType == 'lookup')" class="mb-2.5 w-1/2">
                                        <x-admin::form.control-group.label>
                                            @lang('admin::app.settings.attributes.create.lookup-type')
                                        </x-admin::form.control-group.label>
                                
                                        <x-admin::form.control-group.control
                                            type="select"
                                            id="lookup_type"
                                            name="lookup_type"
                                            :value="$attribute->lookup_type"
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

                                    {!! view_render_event('admin.settings.attributes.edit.form_controls.lookup_type.after', ['attribute' => $attribute]) !!}
                                </div>
                                    
                                <template v-if="optionsData?.length">
                                    @if (
                                        $attribute->type == 'select'
                                        || $attribute->type == 'multiselect'
                                        || $attribute->type == 'checkbox'
                                        || $attribute->type == 'lookup'
                                    )
                                        {!! view_render_event('admin.settings.attributes.edit.table.before', ['attribute' => $attribute]) !!}

                                        <!-- Table Information -->
                                        <x-admin::table>
                                            <x-admin::table.thead>
                                                <x-admin::table.thead.tr>
                                                    <x-admin::table.th></x-admin::table.th>
                                                    <!-- Admin tables heading -->
                                                    <x-admin::table.th>
                                                        @lang('admin::app.settings.attributes.edit.option-name')
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
                                                :list="optionsData"
                                                item-key="id"
                                            >
                                                <template #item="{ element, index }">
                                                    <x-admin::table.thead.tr
                                                        class="hover:bg-gray-50 dark:hover:bg-gray-950"
                                                        v-show="! element.isDelete"
                                                    >
                                                        <input
                                                            type="hidden"
                                                            :name="'options[' + element.id + '][isNew]'"
                                                            :value="element.isNew"
                                                        >

                                                        <input
                                                            type="hidden"
                                                            :name="'options[' + element.id + '][isDelete]'"
                                                            :value="element.isDelete"
                                                        >

                                                        <!-- Draggable Icon -->
                                                        <x-admin::table.td class="!px-0 text-center">
                                                            <i class="icon-move cursor-grab text-xl transition-all group-hover:text-gray-700"></i>

                                                            <input
                                                                type="hidden"
                                                                :name="'options[' + element.id + '][sort_order]'"
                                                                :value="index"
                                                            />
                                                        </x-admin::table.td>

                                                        <!-- Admin-->
                                                        <x-admin::table.td>
                                                            <p class="dark:text-white">
                                                                @{{ element.name }}
                                                            </p>

                                                            <input
                                                                type="hidden"
                                                                :name="'options[' + element.id + '][name]'"
                                                                v-model="element.name"
                                                            />
                                                        </x-admin::table.td>

                                                        <!-- Actions Button -->
                                                        <x-admin::table.td class="!px-0">
                                                            <span
                                                                class="icon-edit cursor-pointer rounded-md p-1.5 text-2xl transition-all hover:bg-gray-200 dark:hover:bg-gray-800 max-sm:place-self-center"
                                                                @click="editOptions(element)"
                                                            >
                                                            </span>

                                                            <span
                                                                class="icon-delete cursor-pointer rounded-md p-1.5 text-2xl transition-all hover:bg-gray-100 dark:hover:bg-gray-800 max-sm:place-self-center"
                                                                @click="removeOption(element.id)"
                                                            >
                                                            </span>
                                                        </x-admin::table.td>
                                                    </x-admin::table.thead.tr>
                                                </template>
                                            </draggable>
                                        </x-admin::table>

                                        {!! view_render_event('admin.settings.attributes.edit.table.after', ['attribute' => $attribute]) !!}
                                    @endif
                                </template>
                            </div>
                        </div>
                    </div>
                </div>
                
                {!! view_render_event('admin.catalog.attributes.edit.card.label.after', ['attribute' => $attribute]) !!}

                <!-- Right sub-component -->
                <div class="flex w-[360px] max-w-full flex-col gap-2 max-sm:w-full">
                    {!! view_render_event('admin.settings.attributes.edit.accordian.general.before', ['attribute' => $attribute]) !!}

                    <!-- General -->
                    <x-admin::accordion>
                        <x-slot:header>
                            <p class="p-2.5 text-base font-semibold text-gray-800 dark:text-white">
                                @lang('admin::app.settings.attributes.edit.general')
                            </p>
                        </x-slot>

                        <x-slot:content>
                            {!! view_render_event('admin.settings.attributes.edit.form_controls.code.before', ['attribute' => $attribute]) !!}

                            <!-- Attribute Code -->
                            <x-admin::form.control-group>
                                <x-admin::form.control-group.label class="required">
                                    @lang('admin::app.settings.attributes.edit.code')
                                </x-admin::form.control-group.label>

                                @php
                                    $selectedOption = old('type') ?: $attribute->code;
                                @endphp

                                <x-admin::form.control-group.control
                                    type="text"
                                    class="cursor-not-allowed"
                                    name="code"
                                    rules="required"
                                    :value="$selectedOption"
                                    :disabled="(boolean) $selectedOption"
                                    readonly
                                    :label="trans('admin::app.settings.attributes.edit.code')"
                                    :placeholder="trans('admin::app.settings.attributes.edit.code')"
                                />

                                <x-admin::form.control-group.control
                                    type="hidden"
                                    name="code"
                                    :value="$selectedOption"
                                />

                                <x-admin::form.control-group.error control-name="code" />
                            </x-admin::form.control-group>

                            {!! view_render_event('admin.settings.attributes.edit.form_controls.code.after', ['attribute' => $attribute]) !!}

                            {!! view_render_event('admin.settings.attributes.edit.form_controls.type.before', ['attribute' => $attribute]) !!}

                            <!-- Attribute Type -->
                            <x-admin::form.control-group>
                                <x-admin::form.control-group.label class="required">
                                    @lang('admin::app.settings.attributes.edit.type')
                                </x-admin::form.control-group.label>

                                @php
                                    $selectedOption = old('type') ?: $attribute->type;
                                @endphp

                                <x-admin::form.control-group.control
                                    type="select"
                                    id="type"
                                    class="cursor-not-allowed"
                                    name="type"
                                    rules="required"
                                    :value="$selectedOption"
                                    :disabled="(boolean) $selectedOption"
                                    :label="trans('admin::app.settings.attributes.edit.type')"
                                >
                                    <!-- Here! All Needed types are defined -->
                                    @foreach(['text', 'textarea', 'price', 'boolean', 'select', 'multiselect', 'checkbox', 'email', 'address', 'phone', 'lookup', 'datetime', 'date', 'image', 'file'] as $type)
                                        <option
                                            value="{{ $type }}"
                                            {{ $selectedOption == $type ? 'selected' : '' }}
                                        >
                                            @lang('admin::app.settings.attributes.edit.'. $type)
                                        </option>
                                    @endforeach
                                </x-admin::form.control-group.control>

                                <x-admin::form.control-group.control
                                    type="hidden"
                                    name="type"
                                    :value="$attribute->type"
                                />

                                <x-admin::form.control-group.error control-name="type" />
                            </x-admin::form.control-group>

                            {!! view_render_event('admin.settings.attributes.edit.form_controls.type.after', ['attribute' => $attribute]) !!}

                            {!! view_render_event('admin.settings.attributes.edit.form_controls.entity_type.before', ['attribute' => $attribute]) !!}

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
                                    :value="$attribute->entity_type"
                                    :label="trans('admin::app.settings.attributes.create.entity-type')"
                                    :placeholder="trans('admin::app.settings.attributes.create.entity-type')"
                                >

                                    @foreach (config('attribute_entity_types') as $key => $entityType)
                                        <option value="{{ $key }}">{{ trans($entityType['name']) }}</option>
                                    @endforeach
                                </x-admin::form.control-group.control>
                                    
                                <x-admin::form.control-group.error control-name="entity_type" />
                            </x-admin::form.control-group>

                            {!! view_render_event('admin.settings.attributes.edit.form_controls.entity_type.after', ['attribute' => $attribute]) !!}
                        </x-slot>
                    </x-admin::accordion>
                    
                    {!! view_render_event('admin.settings.attributes.edit.accordian.general.after', ['attribute' => $attribute]) !!}

                    {!! view_render_event('admin.settings.attributes.edit.accordian.validations.before', ['attribute' => $attribute]) !!}

                    <!-- Validations -->
                    <x-admin::accordion>
                        <x-slot:header>
                            <p class="p-2.5 text-base font-semibold text-gray-800 dark:text-white">
                                @lang('admin::app.settings.attributes.edit.validations')
                            </p>
                        </x-slot>

                        <x-slot:content>
                            <!-- Input Validation -->
                            @if($attribute->type == 'text')
                                {!! view_render_event('admin.settings.attributes.edit.form_controls.select.before', ['attribute' => $attribute]) !!}

                                <x-admin::form.control-group>
                                    <x-admin::form.control-group.label>
                                        @lang('admin::app.settings.attributes.edit.input-validation')
                                    </x-admin::form.control-group.label>

                                    <x-admin::form.control-group.control
                                        type="select"
                                        class="cursor-not-allowed"
                                        name="validation"
                                        :value="$attribute->validation"
                                        disabled="disabled"
                                    >
                                        <!-- Here! All Needed types are defined -->
                                        @foreach(['numeric', 'email', 'decimal', 'url'] as $type)
                                            <option value="{{ $type }}" {{ $attribute->validation == $type ? 'selected' : '' }}>
                                                @lang('admin::app.settings.attributes.edit.' . $type)
                                            </option>
                                        @endforeach
                                    </x-admin::form.control-group.control>
                                </x-admin::form.control-group>

                                {!! view_render_event('admin.settings.attributes.edit.form_controls.select.after', ['attribute' => $attribute]) !!}
                            @endif

                            {!! view_render_event('admin.settings.attributes.edit.form_controls.is_required.before', ['attribute' => $attribute]) !!}

                            <!-- Is Required -->
                            <x-admin::form.control-group class="!mb-2 flex select-none items-center gap-2.5">
                                @php
                                    $selectedOption = old('is_required') ?? $attribute->is_required
                                @endphp

                                <x-admin::form.control-group.control
                                    type="hidden"
                                    name="is_required"
                                    :value="(boolean) $selectedOption"
                                />

                                <x-admin::form.control-group.control
                                    type="checkbox"
                                    name="is_required"
                                    id="is_required"
                                    for="is_required"
                                    value="1"
                                    :checked="(boolean) $selectedOption"
                                />

                                <label
                                    class="cursor-pointer text-xs font-medium text-gray-600 dark:text-gray-300"
                                    for="is_required"
                                >
                                    @lang('admin::app.settings.attributes.edit.is-required')
                                </label>
                            </x-admin::form.control-group>

                            {!! view_render_event('admin.settings.attributes.edit.form_controls.is_required.after', ['attribute' => $attribute]) !!}

                            {!! view_render_event('admin.settings.attributes.edit.form_controls.is_unique.before', ['attribute' => $attribute]) !!}

                            <!-- Is Unique -->
                            <x-admin::form.control-group class="!mb-0 flex select-none items-center gap-2.5">
                                <x-admin::form.control-group.control
                                    type="checkbox"
                                    id="is_unique"
                                    name="is_unique"
                                    value="1"
                                    for="is_unique"
                                    :checked="(boolean) $attribute->is_unique"
                                    :disabled="(boolean) $attribute->is_unique"
                                />

                                <label
                                    class="cursor-pointer text-xs font-medium text-gray-600 dark:text-gray-300"
                                    for="is_unique"
                                >
                                    @lang('admin::app.settings.attributes.edit.is-unique')
                                </label>    

                                <x-admin::form.control-group.control
                                    type="hidden"
                                    :name="$type"
                                    :value="$attribute->is_unique"
                                />
                            </x-admin::form.control-group>

                            {!! view_render_event('admin.settings.attributes.edit.form_controls.is_unique.after', ['attribute' => $attribute]) !!}
                        </x-slot>
                    </x-admin::accordion>

                    {!! view_render_event('admin.settings.attributes.edit.accordian.validations.after', ['attribute' => $attribute]) !!}
                </div>
            </div>

            <!-- Add Options Model Form -->
            <x-admin::form
                v-slot="{ meta, errors, handleSubmit }"
                as="div"
                ref="modelForm"
            >
                <form
                    @submit.prevent="handleSubmit($event, storeOptions)"
                    enctype="multipart/form-data"
                    ref="editOptionsForm"
                >
                    <x-admin::modal
                        @toggle="listenModel"
                        ref="addOptionsRow"
                    >
                        <!-- Modal Header -->
                        <x-slot:header>
                            <p class="text-lg font-bold text-gray-800 dark:text-white">
                                @lang('admin::app.settings.attributes.edit.add-option')
                            </p>
                        </x-slot>

                        <!-- Modal Content -->
                        <x-slot:content>
                            <!-- Hidden Id Input -->
                            <x-admin::form.control-group.control
                                type="hidden"
                                name="id"
                            />

                            <x-admin::form.control-group.control
                                type="hidden"
                                name="isNew"
                                ::value="optionIsNew"
                            />

                            {!! view_render_event('admin.settings.attributes.edit.form_controls.name.before', ['attribute' => $attribute]) !!}

                            <!-- Admin Input -->
                            <x-admin::form.control-group class="mb-2.5 w-full">
                                <x-admin::form.control-group.label class="required">
                                    @lang('admin::app.settings.attributes.edit.option-name')
                                </x-admin::form.control-group.label>

                                <x-admin::form.control-group.control
                                    type="text"
                                    name="name"
                                    rules="required"
                                    :label="trans('admin::app.settings.attributes.edit.option-name')"
                                    :placeholder="trans('admin::app.settings.attributes.edit.option-name')"
                                    ref="inputAdmin"
                                />

                                <x-admin::form.control-group.error control-name="name" />
                            </x-admin::form.control-group>

                            {!! view_render_event('admin.settings.attributes.edit.form_controls.name.after', ['attribute' => $attribute]) !!}
                        </x-slot>

                        <!-- Modal Footer -->
                        <x-slot:footer>
                            <!-- Save Button -->
                            <button
                                type="submit"
                                class="primary-button"
                            >
                                @lang('admin::app.settings.attributes.edit.save-btn')
                            </button>
                        </x-slot>
                    </x-admin::modal>
                </form>
            </x-admin::form>
        </script>

        <script type="module">
            app.component('v-edit-attributes', {
                template: '#v-edit-attributes-template',

                data() {
                    return {
                        showSwatch: {{ in_array($attribute->type, ['select', 'checkbox', 'lookup', 'multiselect']) ? 'true' : 'false' }},

                        isNullOptionChecked: false,

                        swatchValue: [
                            {
                                image: [],
                            }
                        ],

                        optionsData: [],

                        optionIsNew: true,

                        optionId: 0,

                        lookupEntityTypes: @json(config('attribute_lookups')),

                        optionType: "{{ $attribute->lookup_type ? 'lookup' : 'options' }}",
                    }
                },

                created () {
                    this.getAttributesOption();
                },

                methods: {
                    storeOptions(params, { resetForm, setValues }) {
                        if (! params.id) {
                            params.id = 'option_' + this.optionId;
                            this.optionId++;
                        }

                        let foundIndex = this.optionsData.findIndex(item => item.id === params.id);

                        if (foundIndex !== -1) {
                            this.optionsData.splice(foundIndex, 1, params);
                        } else {
                            this.optionsData.push(params);
                        }

                        let formData = new FormData(this.$refs.editOptionsForm);

                        const sliderImage = formData.get("swatch_value[]");

                        if (sliderImage) {
                            params.swatch_value = sliderImage;
                        }

                        this.$refs.addOptionsRow.toggle();

                        if (params.swatch_value instanceof File) {
                            this.setFile(sliderImage, params.id);
                        }

                        resetForm();
                    },

                    editOptions(value) {
                        this.optionIsNew = false;

                        this.$refs.modelForm.setValues(value);

                        this.$refs.addOptionsRow.toggle();
                    },

                    removeOption(id) {
                        this.$emitter.emit('open-confirm-modal', {
                            agree: () => {
                                let foundIndex = this.optionsData.findIndex(item => item.id === id);

                                if (foundIndex !== -1) {
                                    if (this.optionsData[foundIndex].isNew) {
                                        this.optionsData.splice(foundIndex, 1);
                                    } else {
                                        this.optionsData[foundIndex].isDelete = true;
                                    }
                                }

                                this.$emitter.emit('add-flash', { type: 'success', message: "@lang('admin::app.settings.attributes.edit.option-deleted')" });
                            }
                        });
                    },

                    listenModel(event) {
                        if (! event.isActive) {
                            this.isNullOptionChecked = false;
                        }
                    },

                    getAttributesOption() {
                        this.$axios.get(`{{ route('admin.settings.attributes.options', $attribute->id) }}`)
                            .then(response => {
                                let options = response.data;

                                options.forEach((option) => {
                                    let row = {
                                        'id': option.id,
                                        'name': option.name,
                                        'sort_order': option.sort_order,
                                        'attribute_id': option.attribute_id,
                                        'isNew': false,
                                        'isDelete': false,
                                    };

                                    if (! option.label) {
                                        this.isNullOptionChecked = true;

                                        row['notRequired'] = true;
                                    } else {
                                        row['notRequired'] = false;
                                    }

                                    this.optionsData.push(row);
                                });
                            });
                    },

                    setFile(file, id) {
                        let dataTransfer = new DataTransfer();

                        dataTransfer.items.add(file);
                    }
                },
            });
        </script>
    @endPushOnce
</x-admin::layouts>
