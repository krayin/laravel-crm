<x-admin::layouts>
    <!-- Page Title -->
    <x-slot:title>
        @lang('Name')
    </x-slot>

    {!! view_render_event('krayin.admin.activities.edit.form.before') !!}

    <x-admin::form
        :action="route('admin.settings.workflows.update', $workflow->id)"
        method="PUT"
    >
        <div class="flex items-center justify-between">
            <p class="text-xl font-bold text-gray-800 dark:text-white">
                @lang('Name')
            </p>

            <div class="flex items-center gap-x-2.5">
                {!! view_render_event('krayin.admin.activities.edit.back_button.before') !!}

                <!-- Back Button -->
                <a
                    href="{{ route('admin.activities.index') }}"
                    class="transparent-button hover:bg-gray-200 dark:text-white dark:hover:bg-gray-800"
                >
                    @lang('Back')
                </a>

                {!! view_render_event('krayin.admin.activities.edit.back_button.after') !!}

                {!! view_render_event('krayin.admin.activities.edit.back_button.before') !!}

                <!-- Save Button -->
                <button
                    type="submit"
                    class="primary-button"
                >
                    @lang('Save Workflow')
                </button>
 
                {!! view_render_event('krayin.admin.activities.edit.back_button.after') !!}
            </div>
        </div>

        <v-workflow></v-workflow>

        @include('admin::common.custom-attributes.edit.lookup')
    </x-admin::form>

    {!! view_render_event('krayin.admin.activities.edit.form.after') !!}

    @pushOnce('scripts')
        <script
            type="text/x-template"
            id="v-workflow-template"
        >
            <!-- Body Content -->
            <div class="mt-3.5 flex gap-2.5 max-xl:flex-wrap">
                <!-- Left sub-component -->
                <div class="flex flex-1 flex-col gap-2 max-xl:flex-auto">
                    <!-- Events -->
                    <div class="box-shadow rounded bg-white p-4 dark:bg-gray-900">
                        <div class="mb-8 flex items-center justify-between gap-4">
                            <p class="text-base font-semibold text-gray-800 dark:text-white">
                                @lang('Event')
                            </p>
                        </div>

                        <!-- Coupon Code -->
                        <x-admin::form.control-group>
                            <x-admin::form.control-group.label class="required">
                                @lang('Event')
                            </x-admin::form.control-group.label>

                            <x-admin::form.control-group.control
                                type="select"
                                id="event"
                                name="event"
                                ::value="event"
                                rules="required"
                                :label="trans('Events')"
                                :placeholder="trans('Events')"
                                v-model="event"
                            >
                                <optgroup
                                    v-for='entity in events'
                                    :label="entity.name"
                                >
                                    <option
                                        v-for='event in entity.events'
                                        :value="event.event"
                                    >
                                        @{{ event.name }}
                                    </option>
                                </optgroup>
                            </x-admin::form.control-group.control>

                            <x-admin::form.control-group.error control-name="coupon_code" />
                        </x-admin::form.control-group>

                    </div>

                    <!-- Conditions -->
                    <div class="box-shadow rounded bg-white p-4 dark:bg-gray-900">
                        <div class="flex items-center justify-between gap-4">
                            <p class="text-base font-semibold text-gray-800 dark:text-white">
                                @lang('Conditions')
                            </p>

                            <!-- Condition Type -->
                            <x-admin::form.control-group>
                                <x-admin::form.control-group.label>
                                    @lang('Condition Type')
                                </x-admin::form.control-group.label>

                                <x-admin::form.control-group.control
                                    type="select"
                                    class="ltr:pr-10 rtl:pl-10"
                                    id="condition_type"
                                    name="condition_type"
                                    v-model="conditionType"
                                    :label="trans('Condition Type')"
                                    :placeholder="trans('Condition Type')"
                                >
                                    <option value="1">
                                        @lang('All conditions are true')
                                    </option>

                                    <option value="2">
                                        @lang('Any condition is true')
                                    </option>
                                </x-admin::form.control-group.control>

                                <x-admin::form.control-group.error control-name="condition_type" />
                            </x-admin::form.control-group>
                        </div>

                        <!-- Workflow Condition Vue Component. -->
                        <v-workflow-condition-item
                            v-for='(condition, index) in conditions'
                            :entityType="entityType"
                            :condition="condition"
                            :key="index"
                            :index="index"
                            @onRemoveCondition="removeCondition($event)"
                        ></v-workflow-condition-item>

                        <div
                            class="secondary-button mt-4 max-w-max"
                            @click="addCondition"
                        >
                            @lang('Add Condition')
                        </div>
                    </div>

                    <!-- Actions -->
                    <div class="box-shadow rounded bg-white p-4 dark:bg-gray-900">
                        <!-- Workflow Condition Vue Component. -->
                        <v-workflow-action-item
                            v-for='(action, index) in actions'
                            :entityType="entityType"
                            :action="action"
                            :key="index"
                            :index="index"
                            @onRemoveAction="removeAction($event)"
                        ></v-workflow-action-item>

                        <div
                            class="secondary-button mt-4 max-w-max"
                            @click="addAction"
                        >
                            @lang('Add Action')
                        </div>
                    </div>
                </div>

                <!-- Right sub-component -->
                <div class="flex w-[360px] max-w-full flex-col gap-2 max-sm:w-full">
                    {!! view_render_event('krayin.admin.activities.edit.accordion.general.before') !!}

                    <x-admin::accordion>
                        <x-slot:header>
                            <div class="flex items-center justify-between">
                                <p class="p-2.5 text-base font-semibold text-gray-800 dark:text-white">
                                    @lang('General')
                                </p>
                            </div>
                        </x-slot>

                        <x-slot:content>
                            <x-admin::form.control-group>
                                <x-admin::form.control-group.label class="required">
                                    @lang('Name')
                                </x-admin::form.control-group.label>

                                <x-admin::form.control-group.control
                                    type="text"
                                    name="name"
                                    id="name"
                                    :value="old('name') ?? $workflow->name"
                                    rules="required"
                                    :label="trans('Name')"
                                    :placeholder="trans('Name')"
                                />
                                <x-admin::form.control-group.error control-name="title" />
                            </x-admin::form.control-group>

                            <x-admin::form.control-group>
                                <x-admin::form.control-group.label class="required">
                                    @lang('Description')
                                </x-admin::form.control-group.label>

                                <x-admin::form.control-group.control
                                    type="textarea"
                                    name="description"
                                    id="description"
                                    :value="old('description') ?? $workflow->description"
                                    :label="trans('description')"
                                    :placeholder="trans('description')"
                                />

                                <x-admin::form.control-group.error control-name="type" />
                            </x-admin::form.control-group>
                        </x-slot>
                    </x-admin::accordion>

                    {!! view_render_event('krayin.admin.activities.edit.accordion.general.after') !!}
                </div>
            </div>
        </script>

        <script
            type="text/x-template"
            id="v-workflow-condition-item-template"
        >
            <div class="mt-4 flex justify-between gap-4">
                <div class="flex flex-1 gap-4 max-sm:flex-1 max-sm:flex-wrap">
                    <select
                        :name="['conditions[' + index + '][attribute]']"
                        :id="['conditions[' + index + '][attribute]']"
                        class="custom-select min:w-1/3 flex h-10 w-1/3 rounded-md border bg-white px-3 py-2.5 text-sm font-normal text-gray-600 transition-all hover:border-gray-400 dark:border-gray-800 dark:bg-gray-900 dark:text-gray-300 dark:hover:border-gray-400 max-sm:max-w-full max-sm:flex-auto"
                        v-model="condition.attribute"
                    >
                        <option
                            v-for='attribute in conditions[entityType]'
                            :value="attribute.id"
                        >
                            @{{ attribute.name }}
                        </option>
                    </select>

                    <template v-if="matchedAttribute">
                        <select
                            :name="['conditions[' + index + '][operator]']"
                            :id="['conditions[' + index + '][operator]']"
                            class="custom-select inline-flex h-10 w-full max-w-[196px] items-center justify-between gap-x-1 rounded-md border bg-white px-3 py-2.5 text-sm font-normal text-gray-600 transition-all hover:border-gray-400 dark:border-gray-800 dark:bg-gray-900 dark:text-gray-300 dark:hover:border-gray-400 max-sm:max-w-full max-sm:flex-auto"
                            v-model="condition.operator"
                        >
                            <option
                                v-for='operator in conditionOperators[matchedAttribute.type]'
                                :value="operator.operator"
                                :text="operator.name"
                            ></option>
                        </select>
                    </template>

                    <template v-if="matchedAttribute">
                        <input
                            type="hidden"
                            :name="['conditions[' + index + '][attribute_type]']"
                            v-model="matchedAttribute.type"
                        >

                        <template
                            v-if="
                                matchedAttribute.type == 'text' 
                                || matchedAttribute.type == 'price'
                                || matchedAttribute.type == 'decimal'
                                || matchedAttribute.type == 'integer'
                                || matchedAttribute.type == 'email'
                                || matchedAttribute.type == 'phone'
                            "
                        >
                            <v-field
                                :name="`conditions[${index}][value]`"
                                v-slot="{ field, errorMessage }"
                                :id="`conditions[${index}][value]`"
                                :rules="
                                    matchedAttribute.type == 'price' ? 'regex:^[0-9]+(\\.[0-9]+)?$' : ''
                                    || matchedAttribute.type == 'decimal' ? 'regex:^[0-9]+(\\.[0-9]+)?$' : ''
                                    || matchedAttribute.type == 'integer' ? 'regex:^[0-9]+$' : ''
                                    || matchedAttribute.type == 'text' ? 'regex:^.*$' : ''
                                    || matchedAttribute.type == 'email' ? 'email' : ''
                                "
                                v-model="condition.value"
                            >
                                <input
                                    type="text"
                                    v-bind="field"
                                    :class="{ 'border border-red-500': errorMessage }"
                                    class="min:w-1/3 flex h-10 w-[289px] rounded-md border px-3 py-2.5 text-sm text-gray-600 transition-all hover:border-gray-400 focus:border-gray-400 dark:border-gray-800 dark:bg-gray-900 dark:text-gray-300 dark:hover:border-gray-400 dark:focus:border-gray-400"
                                />
                            </v-field>

                            <v-error-message
                                :name="`conditions[${index}][value]`"
                                class="mt-1 text-xs italic text-red-500"
                                as="p"
                            >
                            </v-error-message>
                        </template>

                        <template v-if="matchedAttribute.type == 'date'">
                            <x-admin::flat-picker.date
                                class="!w-[140px]"
                                ::allow-input="false"
                            >
                                <input
                                    type="date"
                                    class="flex min-h-[39px] w-full rounded-md border px-3 py-2 text-sm text-gray-600 transition-all hover:border-gray-400 dark:border-gray-800 dark:bg-gray-900 dark:text-gray-300 dark:hover:border-gray-400"
                                    :name="['conditions[' + index + '][value]']"
                                    v-model="condition.value"
                                />
                            </x-admin::flat-picker.date>
                        </template>

                        <template v-if="matchedAttribute.type == 'datetime'">
                            <x-admin::flat-picker.date
                                class="!w-[140px]"
                                ::allow-input="false"
                            >
                                <input
                                    type="datetime"
                                    class="flex min-h-[39px] w-full rounded-md border px-3 py-2 text-sm text-gray-600 transition-all hover:border-gray-400 dark:border-gray-800 dark:bg-gray-900 dark:text-gray-300 dark:hover:border-gray-400"
                                    :name="['conditions[' + index + '][value]']"
                                    v-model="condition.value"
                                />
                            </x-admin::flat-picker.date>
                        </template>

                        <template v-if="matchedAttribute.type == 'boolean'">
                            <select
                                :name="['conditions[' + index + '][value]']"
                                class="custom-select inline-flex h-10 w-full min-w-[196px] items-center justify-between gap-x-1 rounded-md border bg-white px-3 py-2.5 text-sm font-normal text-gray-600 transition-all hover:border-gray-400 dark:border-gray-800 dark:bg-gray-900 dark:text-gray-300 dark:hover:border-gray-400 max-sm:max-w-full max-sm:flex-auto"
                                v-model="condition.value"
                            >
                                <option value="1">
                                    @lang('Yes')
                                </option>

                                <option value="0">
                                    @lang('No')
                                </option>
                            </select>
                        </template>

                        <template
                            v-if="
                                matchedAttribute.type == 'select'
                                || matchedAttribute.type == 'radio'
                                || matchedAttribute.type == 'lookup'
                            "
                        >
                            <template v-if="! matchedAttribute.lookup_type">
                                <select
                                    :name="['conditions[' + index + '][value]']"
                                    class="custom-select inline-flex h-10 w-full min-w-[196px] items-center justify-between gap-x-1 rounded-md border bg-white px-3 py-2.5 text-sm font-normal text-gray-600 transition-all hover:border-gray-400 dark:border-gray-800 dark:bg-gray-900 dark:text-gray-300 dark:hover:border-gray-400"
                                    v-model="condition.value"
                                >
                                    <option
                                        v-for='option in matchedAttribute.options'
                                        :value="option.id"
                                        :text="option.name"
                                    >
                                    </option>
                                </select>
                            </template>

                            <template v-else>
                                <v-lookup-component
                                    :attribute="{'code': 'conditions[' + index + '][value]', 'name': 'Email', 'lookup_type': matchedAttribute.lookup_type}"
                                    validations="required|email"
                                    :data="condition.value"
                                ></v-lookup-component>
                            </template>
                        </template>

                        <template
                            v-if="matchedAttribute.type == 'multiselect'
                            || matchedAttribute.type == 'checkbox'"
                        >
                            <select
                                :name="['conditions[' + index + '][value][]']"
                                class="inline-flex h-20 w-[250px] max-w-[350px] items-center justify-between gap-x-1 rounded-md border px-3 py-2 text-sm text-gray-600 transition-all hover:border-gray-400 focus:border-gray-400 dark:border-gray-800 dark:bg-gray-900 dark:text-gray-300 dark:hover:border-gray-400 dark:focus:border-gray-400"
                                v-model="condition.value"
                                multiple
                            >
                                <option
                                    v-for='option in matchedAttribute.options'
                                    :value="option.id"
                                    :text="option.name"
                                ></option>
                            </select>
                        </template>
                                                    
                        <template v-if="matchedAttribute.type == 'textarea'">
                            <textarea
                                :name="['conditions[' + index + '][value]']"
                                :id="['conditions[' + index + '][value]']"
                                v-model="condition.value"
                                class="w-full rounded-md border px-3 py-2.5 text-sm text-gray-600 transition-all hover:border-gray-400 focus:border-gray-400 dark:border-gray-800 dark:bg-gray-900 dark:text-gray-300 dark:hover:border-gray-400 dark:focus:border-gray-400"
                            ></textarea>
                        </template>
                    </template>
                </div>

                <span
                    class="icon-delete max-h-9 max-w-9 cursor-pointer rounded-md p-1.5 text-2xl transition-all hover:bg-gray-100 dark:hover:bg-gray-950 max-sm:place-self-center"
                    @click="removeCondition"
                ></span>
            </div>
        </script>

        <script
            type="text/x-template"
            id="v-workflow-action-item-template"
        >   
            <div class="mt-4 flex justify-between gap-4">
                <div class="flex flex-1 gap-4 max-sm:flex-1 max-sm:flex-wrap">
                    <select
                        :name="['actions[' + index + '][id]']"
                        :id="['actions[' + index + '][id]']"
                        class="custom-select min:w-1/3 flex h-10 w-1/3 rounded-md border bg-white px-3 py-2.5 text-sm font-normal text-gray-600 transition-all hover:border-gray-400 dark:border-gray-800 dark:bg-gray-900 dark:text-gray-300 dark:hover:border-gray-400 max-sm:max-w-full max-sm:flex-auto"
                        v-model="action.id"
                    >
                        <option
                            v-for='action in actions[entityType]'
                            :value="action.id"
                        >
                            @{{ action.name }}
                        </option>
                    </select>

                    <template v-if="matchedAction && matchedAction.attributes">
                        <select
                            :name="['actions[' + index + '][attribute]']"
                            :id="['actions[' + index + '][attribute]']"
                            class="custom-select inline-flex h-10 w-full max-w-[196px] items-center justify-between gap-x-1 rounded-md border bg-white px-3 py-2.5 text-sm font-normal text-gray-600 transition-all hover:border-gray-400 dark:border-gray-800 dark:bg-gray-900 dark:text-gray-300 dark:hover:border-gray-400 max-sm:max-w-full max-sm:flex-auto"
                            v-model="action.attribute"
                        >
                            <option
                                v-for='attribute in matchedAction.attributes'
                                :value="attribute.id"
                            >
                                @{{ attribute.name }}
                            </option>
                        </select>

                        <template v-if="matchedAttribute">
                            <input
                                type="hidden"
                                :name="['actions[' + index + '][attribute_type]']"
                                v-model="matchedAttribute.type"
                            >

                            <template
                                v-if="
                                    matchedAttribute.type == 'text' 
                                    || matchedAttribute.type == 'price'
                                    || matchedAttribute.type == 'decimal'
                                    || matchedAttribute.type == 'integer'
                                    || matchedAttribute.type == 'email'
                                    || matchedAttribute.type == 'phone'
                                "
                            >
                                <v-field
                                    :name="`actions[${index}][value]`"
                                    v-slot="{ field, errorMessage }"
                                    :id="`actions[${index}][value]`"
                                    :rules="
                                        matchedAttribute.type == 'price' ? 'regex:^[0-9]+(\\.[0-9]+)?$' : ''
                                        || matchedAttribute.type == 'decimal' ? 'regex:^[0-9]+(\\.[0-9]+)?$' : ''
                                        || matchedAttribute.type == 'integer' ? 'regex:^[0-9]+$' : ''
                                        || matchedAttribute.type == 'text' ? 'regex:^.*$' : ''
                                        || matchedAttribute.type == 'email' ? 'email' : ''
                                    "
                                    v-model="condition.value"
                                >
                                    <input
                                        type="text"
                                        v-bind="field"
                                        :class="{ 'border border-red-500': errorMessage }"
                                        class="min:w-1/3 flex h-10 w-[289px] rounded-md border px-3 py-2.5 text-sm text-gray-600 transition-all hover:border-gray-400 focus:border-gray-400 dark:border-gray-800 dark:bg-gray-900 dark:text-gray-300 dark:hover:border-gray-400 dark:focus:border-gray-400"
                                    />
                                </v-field>

                                <v-error-message
                                    :name="`actions[${index}][value]`"
                                    class="mt-1 text-xs italic text-red-500"
                                    as="p"
                                >
                                </v-error-message>
                            </template>

                            <template v-if="matchedAttribute.type == 'date'">
                                <x-admin::flat-picker.date
                                    class="!w-[140px]"
                                    ::allow-input="false"
                                >
                                    <input
                                        type="date"
                                        class="flex min-h-[39px] w-full rounded-md border px-3 py-2 text-sm text-gray-600 transition-all hover:border-gray-400 dark:border-gray-800 dark:bg-gray-900 dark:text-gray-300 dark:hover:border-gray-400"
                                        :name="['actions[' + index + '][value]']"
                                        v-model="condition.value"
                                    />
                                </x-admin::flat-picker.date>
                            </template>

                            <template v-if="matchedAttribute.type == 'datetime'">
                                <x-admin::flat-picker.date
                                    class="!w-[140px]"
                                    ::allow-input="false"
                                >
                                    <input
                                        type="datetime"
                                        class="flex min-h-[39px] w-full rounded-md border px-3 py-2 text-sm text-gray-600 transition-all hover:border-gray-400 dark:border-gray-800 dark:bg-gray-900 dark:text-gray-300 dark:hover:border-gray-400"
                                        :name="['actions[' + index + '][value]']"
                                        v-model="condition.value"
                                    />
                                </x-admin::flat-picker.date>
                            </template>

                            <template v-if="matchedAttribute.type == 'boolean'">
                                <select
                                    :name="['actions[' + index + '][value]']"
                                    class="custom-select inline-flex h-10 w-full min-w-[196px] items-center justify-between gap-x-1 rounded-md border bg-white px-3 py-2.5 text-sm font-normal text-gray-600 transition-all hover:border-gray-400 dark:border-gray-800 dark:bg-gray-900 dark:text-gray-300 dark:hover:border-gray-400 max-sm:max-w-full max-sm:flex-auto"
                                    v-model="condition.value"
                                >
                                    <option value="1">
                                        @lang('Yes')
                                    </option>

                                    <option value="0">
                                        @lang('No')
                                    </option>
                                </select>
                            </template>

                            <template
                                v-if="
                                    matchedAttribute.type == 'select'
                                    || matchedAttribute.type == 'radio'
                                    || matchedAttribute.type == 'lookup'
                                "
                            >
                                <template v-if="! matchedAttribute.lookup_type">
                                    <select
                                        :name="['actions[' + index + '][value]']"
                                        class="custom-select inline-flex h-10 w-full min-w-[196px] items-center justify-between gap-x-1 rounded-md border bg-white px-3 py-2.5 text-sm font-normal text-gray-600 transition-all hover:border-gray-400 dark:border-gray-800 dark:bg-gray-900 dark:text-gray-300 dark:hover:border-gray-400"
                                        v-model="condition.value"
                                    >
                                        <option
                                            v-for='option in matchedAttribute.options'
                                            :value="option.id"
                                            :text="option.name"
                                        >
                                        </option>
                                    </select>
                                </template>

                                <template v-else>
                                    <v-lookup-component
                                        :attribute="{'code': 'actions[' + index + '][value]', 'name': 'Email', 'lookup_type': matchedAttribute.lookup_type}"
                                        validations="required|email"
                                        :data="condition.value"
                                    ></v-lookup-component>
                                </template>
                            </template>

                            <template
                                v-if="matchedAttribute.type == 'multiselect'
                                || matchedAttribute.type == 'checkbox'"
                            >
                                <select
                                    :name="['actions[' + index + '][value][]']"
                                    class="inline-flex h-20 w-[250px] max-w-[350px] items-center justify-between gap-x-1 rounded-md border px-3 py-2 text-sm text-gray-600 transition-all hover:border-gray-400 focus:border-gray-400 dark:border-gray-800 dark:bg-gray-900 dark:text-gray-300 dark:hover:border-gray-400 dark:focus:border-gray-400"
                                    v-model="condition.value"
                                    multiple
                                >
                                    <option
                                        v-for='option in matchedAttribute.options'
                                        :value="option.id"
                                        :text="option.name"
                                    ></option>
                                </select>
                            </template>
                                                        
                            <template v-if="matchedAttribute.type == 'textarea'">
                                <textarea
                                    :name="['actions[' + index + '][value]']"
                                    :id="['actions[' + index + '][value]']"
                                    v-model="condition.value"
                                    class="w-full rounded-md border px-3 py-2.5 text-sm text-gray-600 transition-all hover:border-gray-400 focus:border-gray-400 dark:border-gray-800 dark:bg-gray-900 dark:text-gray-300 dark:hover:border-gray-400 dark:focus:border-gray-400"
                                ></textarea>
                            </template>
                        </template>
                    </template>

                    <template v-if="matchedAction && matchedAction.options">
                        <select
                            :name="['actions[' + index + '][id]']"
                            :id="['actions[' + index + '][id]']"
                            class="custom-select min:w-1/3 flex h-10 w-1/3 rounded-md border bg-white px-3 py-2.5 text-sm font-normal text-gray-600 transition-all hover:border-gray-400 dark:border-gray-800 dark:bg-gray-900 dark:text-gray-300 dark:hover:border-gray-400 max-sm:max-w-full max-sm:flex-auto"
                            v-model="action.id"
                        >
                            <option
                                v-for='action in actions[entityType]'
                                :value="action.id"
                                :text="action.name"
                            ></option>
                        </select>
                    </template>

                    <template v-if="matchedAction && matchedAction.request_methods">
                        @include('admin::settings.workflows.webhook.index')

                        <v-webhook-component
                            :entity-type="entityType"git a
                            :index="index"
                            :matched-action="matchedAction"
                        ></v-webhook-component>
                    </template>
                </div>

                <span
                    class="icon-delete max-h-9 max-w-9 cursor-pointer rounded-md p-1.5 text-2xl transition-all hover:bg-gray-100 dark:hover:bg-gray-950 max-sm:place-self-center"
                    @click="removeAction"
                ></span>
            </div>
        </script>

        <script type="module">
            app.component('v-workflow', {
                template: '#v-workflow-template',

                data() {
                    return {
                        events: @json(app('\Webkul\Workflow\Helpers\Entity')->getEvents()),

                        event: "{{ $workflow->event }}",

                        conditionType: "{{ $workflow->condition_type }}",

                        conditions: @json($workflow->conditions ?: []),

                        actions: @json($workflow->actions ?: []),
                    };
                },

                computed: {
                    /**
                     * Get the entity type.
                     * 
                     * @return {String}
                     */
                    entityType() {
                        if (this.event == '') {
                            return '';
                        }

                        let entityType = '';

                        for (let event in this.events) {
                            this.events[event].events.forEach(eventTemp => {
                                if (eventTemp.event == this.event) {
                                    entityType = event;
                                }
                            });
                        }

                        return entityType;
                    }
                },

                watch: {
                    /**
                     * Watch the entity Type.
                     * 
                     * @return {void}
                     */
                    entityType(newValue, oldValue) {
                        this.conditions = [];

                        this.actions = [];
                    },
                },

                methods: {
                    /**
                     * Add the condition.
                     * 
                     * @returns {void}
                     */
                    addCondition() {
                        this.conditions.push({
                            'attribute': '',
                            'operator': '==',
                            'value': '',
                        });
                    },

                    /**
                     * Remove the condition.
                     * 
                     * @param {Object} condition
                     * @returns {void}
                     */
                    removeCondition(condition) {
                        let index = this.conditions.indexOf(condition);

                        this.conditions.splice(index, 1);
                    },

                    /**
                     * Add the action.
                     * 
                     * @returns {void}
                     */
                    addAction() {
                        this.actions.push({
                            'id': '',
                            'attribute': '',
                            'value': '',
                        });
                    },

                    /**
                     * Remove the action.
                     * 
                     * @param {Object} action
                     * @returns {void}
                     */
                    removeAction(action) {
                        let index = this.actions.indexOf(action)

                        this.actions.splice(index, 1);
                    },
                },
            });
        </script>

        <script type="module">
            app.component('v-workflow-condition-item', {
                template: '#v-workflow-condition-item-template',

                props: ['index', 'entityType', 'condition'],

                emits: ['on-remove-condition'],
                
                data() {
                    return {
                        conditions: @json(app('\Webkul\Workflow\Helpers\Entity')->getConditions()),

                        conditionOperators: {
                            'price': [{
                                    'operator': '==',
                                    'name': '@lang('admin::app.settings.workflows.is-equal-to')'
                                }, {
                                    'operator': '!=',
                                    'name': '@lang('admin::app.settings.workflows.is-not-equal-to')'
                                }, {
                                    'operator': '>=',
                                    'name': '@lang('admin::app.settings.workflows.equals-or-greater-than')'
                                }, {
                                    'operator': '<=',
                                    'name': '@lang('admin::app.settings.workflows.equals-or-less-than')'
                                }, {
                                    'operator': '>',
                                    'name': '@lang('admin::app.settings.workflows.greater-than')'
                                }, {
                                    'operator': '<',
                                    'name': '@lang('admin::app.settings.workflows.less-than')'
                                }],
                            'decimal': [{
                                    'operator': '==',
                                    'name': '@lang('admin::app.settings.workflows.is-equal-to')'
                                }, {
                                    'operator': '!=',
                                    'name': '@lang('admin::app.settings.workflows.is-not-equal-to')'
                                }, {
                                    'operator': '>=',
                                    'name': '@lang('admin::app.settings.workflows.equals-or-greater-than')'
                                }, {
                                    'operator': '<=',
                                    'name': '@lang('admin::app.settings.workflows.equals-or-less-than')'
                                }, {
                                    'operator': '>',
                                    'name': '@lang('admin::app.settings.workflows.greater-than')'
                                }, {
                                    'operator': '<',
                                    'name': '@lang('admin::app.settings.workflows.less-than')'
                                }],
                            'integer': [{
                                    'operator': '==',
                                    'name': '@lang('admin::app.settings.workflows.is-equal-to')'
                                }, {
                                    'operator': '!=',
                                    'name': '@lang('admin::app.settings.workflows.is-not-equal-to')'
                                }, {
                                    'operator': '>=',
                                    'name': '@lang('admin::app.settings.workflows.equals-or-greater-than')'
                                }, {
                                    'operator': '<=',
                                    'name': '@lang('admin::app.settings.workflows.equals-or-less-than')'
                                }, {
                                    'operator': '>',
                                    'name': '@lang('admin::app.settings.workflows.greater-than')'
                                }, {
                                    'operator': '<',
                                    'name': '@lang('admin::app.settings.workflows.less-than')'
                                }],
                            'text': [{
                                    'operator': '==',
                                    'name': '@lang('admin::app.settings.workflows.is-equal-to')'
                                }, {
                                    'operator': '!=',
                                    'name': '@lang('admin::app.settings.workflows.is-not-equal-to')'
                                }, {
                                    'operator': '{}',
                                    'name': '@lang('admin::app.settings.workflows.contain')'
                                }, {
                                    'operator': '!{}',
                                    'name': '@lang('admin::app.settings.workflows.does-not-contain')'
                                }],
                            'boolean': [{
                                    'operator': '==',
                                    'name': '@lang('admin::app.settings.workflows.is-equal-to')'
                                }, {
                                    'operator': '!=',
                                    'name': '@lang('admin::app.settings.workflows.is-not-equal-to')'
                                }],
                            'date': [{
                                    'operator': '==',
                                    'name': '@lang('admin::app.settings.workflows.is-equal-to')'
                                }, {
                                    'operator': '!=',
                                    'name': '@lang('admin::app.settings.workflows.is-not-equal-to')'
                                }, {
                                    'operator': '>=',
                                    'name': '@lang('admin::app.settings.workflows.equals-or-greater-than')'
                                }, {
                                    'operator': '<=',
                                    'name': '@lang('admin::app.settings.workflows.equals-or-less-than')'
                                }, {
                                    'operator': '>',
                                    'name': '@lang('admin::app.settings.workflows.greater-than')'
                                }, {
                                    'operator': '<',
                                    'name': '@lang('admin::app.settings.workflows.less-than')'
                                }],
                            'datetime': [{
                                    'operator': '==',
                                    'name': '@lang('admin::app.settings.workflows.is-equal-to')'
                                }, {
                                    'operator': '!=',
                                    'name': '@lang('admin::app.settings.workflows.is-not-equal-to')'
                                }, {
                                    'operator': '>=',
                                    'name': '@lang('admin::app.settings.workflows.equals-or-greater-than')'
                                }, {
                                    'operator': '<=',
                                    'name': '@lang('admin::app.settings.workflows.equals-or-less-than')'
                                }, {
                                    'operator': '>',
                                    'name': '@lang('admin::app.settings.workflows.greater-than')'
                                }, {
                                    'operator': '<',
                                    'name': '@lang('admin::app.settings.workflows.less-than')'
                                }],
                            'select': [{
                                    'operator': '==',
                                    'name': '@lang('admin::app.settings.workflows.is-equal-to')'
                                }, {
                                    'operator': '!=',
                                    'name': '@lang('admin::app.settings.workflows.is-not-equal-to')'
                                }],
                            'radio': [{
                                    'operator': '==',
                                    'name': '@lang('admin::app.settings.workflows.is-equal-to')'
                                }, {
                                    'operator': '!=',
                                    'name': '@lang('admin::app.settings.workflows.is-not-equal-to')'
                                }],
                            'multiselect': [{
                                    'operator': '{}',
                                    'name': '@lang('admin::app.settings.workflows.contains')'
                                }, {
                                    'operator': '!{}',
                                    'name': '@lang('admin::app.settings.workflows.does-not-contain')'
                                }],
                            'checkbox': [{
                                    'operator': '{}',
                                    'name': '@lang('admin::app.settings.workflows.contains')'
                                }, {
                                    'operator': '!{}',
                                    'name': '@lang('admin::app.settings.workflows.does-not-contain')'
                                }],
                            'email': [{
                                    'operator': '{}',
                                    'name': '@lang('admin::app.settings.workflows.contains')'
                                }, {
                                    'operator': '!{}',
                                    'name': '@lang('admin::app.settings.workflows.does-not-contain')'
                                }],
                            'phone': [{
                                    'operator': '{}',
                                    'name': '@lang('admin::app.settings.workflows.contains')'
                                }, {
                                    'operator': '!{}',
                                    'name': '@lang('admin::app.settings.workflows.does-not-contain')'
                                }],
                            'lookup': [{
                                    'operator': '==',
                                    'name': '@lang('admin::app.settings.workflows.is-equal-to')'
                                }, {
                                    'operator': '!=',
                                    'name': '@lang('admin::app.settings.workflows.is-not-equal-to')'
                                }],
                        }
                    };
                },

                computed: {
                    /**
                     * Get the matched attribute.
                     * 
                     * @returns {Object}
                     */
                    matchedAttribute() {
                        if (this.condition.attribute == '') {
                            return;
                        }

                        let matchedAttribute = this.conditions[this.entityType].find(attribute => attribute.id == this.condition.attribute);

                        if (
                            matchedAttribute['type'] == 'multiselect'
                            || matchedAttribute['type'] == 'checkbox'
                        ) {
                            if (! this.condition.operator) {
                                this.condition.operator = '{}'; 
                            }

                            if (! this.condition.value) {
                                this.condition.value = [];
                            }
                        } else if (
                            matchedAttribute['type'] == 'email'
                            || matchedAttribute['type'] == 'phone'
                        ) {
                            this.condition.operator = '{}';
                        }

                        return matchedAttribute;
                    },
                },

                methods: {
                    /**
                     * Remove the condition.
                     * 
                     * @returns {void}
                     */
                    removeCondition() {
                        this.$emit('onRemoveCondition', this.condition);
                    },
                }
            });
        </script>

        <script type="module">
            app.component('v-workflow-action-item', {
                template: '#v-workflow-action-item-template',

                props: ['index', 'entityType', 'action'],

                data() {
                    return {
                        actions: @json(app('\Webkul\Workflow\Helpers\Entity')->getActions()),
                    };
                },

                computed: {
                    /**
                     * Get the matched action.
                     * 
                     * @returns {Object}
                     */
                    matchedAction: function () {
                        if (this.entityType == '') {
                            return;
                        }

                        return this.actions[this.entityType].find((action) => action.id == this.action.id);
                    },

                    /**
                     * Get the matched attribute.
                     * 
                     * @return {void}
                     */
                    matchedAttribute() {
                        if (! this.matchedAction) {
                            return;
                        }

                        let matchedAttribute = this.matchedAction.attributes.find((attribute) => attribute.id == this.action.attribute);

                        if (! matchedAttribute.length) {
                            return;
                        }

                        if (
                            matchedAttribute['type'] == 'multiselect'
                            || matchedAttribute['type'] == 'checkbox'
                        ) {
                            this.action.value = this.action.value == '' && this.action.value != undefined
                                ? []
                                : Array.isArray(this.action.value) ? this.action.value : [];
                        } else if (
                            matchedAttribute['type'] == 'email'
                            || matchedAttribute['type'] == 'phone'
                        ) {
                            this.action.value = this.action.value == '' && this.action.value != undefined
                                ? [{
                                    'label': 'work',
                                    'value': ''
                                }]
                                : Array.isArray(this.action.value) ? this.action.value : [{
                                    'label': 'work',
                                    'value': ''
                                }];
                        } else if (matchedAttribute['type'] == 'text') {
                            this.action.value = '';
                        }

                        return matchedAttribute;
                    },
                },

                methods: {
                    /**
                     * Remove the action.
                     * 
                     * @returns {void}
                     */
                    removeAction() {
                        this.$emit('onRemoveAction', this.action);
                    },
                },
            });
        </script>
   @endPushOnce
</x-admin::layouts>