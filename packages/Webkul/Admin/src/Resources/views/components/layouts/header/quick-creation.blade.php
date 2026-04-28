@php
    $canCreateLead = bouncer()->hasPermission('leads.create');
    $canCreateMail = bouncer()->hasPermission('mail.create');
    $canCreatePerson = bouncer()->hasPermission('contacts.persons.create');
    $canCreateOrganization = bouncer()->hasPermission('contacts.organizations.create');
    $canCreateProduct = bouncer()->hasPermission('products.create');
    $canCreateAttribute = bouncer()->hasPermission('settings.automation.attributes.create');

    $hasAnyQuickAddPermission = $canCreateLead
        || $canCreateMail
        || $canCreatePerson
        || $canCreateOrganization
        || $canCreateProduct
        || $canCreateAttribute;

    $attributeTypesForQuickAdd = ['text', 'textarea', 'price', 'boolean', 'select', 'multiselect', 'checkbox', 'email', 'address', 'phone', 'lookup', 'datetime', 'date', 'image', 'file'];

    $attributeEntityTypesForQuickAdd = config('attribute_entity_types');

    $defaultQuickAddTab = match (true) {
        $canCreateLead => 'lead',
        $canCreatePerson => 'person',
        $canCreateOrganization => 'organization',
        $canCreateProduct => 'product',
        $canCreateMail => 'mail',
        $canCreateAttribute => 'attribute',
        default => '',
    };
@endphp

<div>
    @if ($hasAnyQuickAddPermission)
        <v-quick-add></v-quick-add>
    @endif

    @pushOnce('scripts')
        <script
            type="text/x-template"
            id="v-quick-add-template"
        >
            <!-- Trigger Button -->
            <button
                type="button"
                class="flex h-9 w-9 cursor-pointer items-center justify-center rounded-full bg-brandColor text-white"
                @click="open"
            >
                <i class="icon-add text-2xl"></i>
            </button>

            <Teleport to="body">
                <x-admin::modal
                    ref="modal"
                    size="large"
                >
                    <x-slot:header>
                        <div class="flex items-center justify-between">
                            <p class="text-xl font-semibold text-gray-800 dark:text-white">
                                @lang('admin::app.layouts.quick-add.title')
                            </p>
                        </div>
                    </x-slot>

                    <x-slot:content>
                        <div class="flex flex-col gap-2">
                            <!-- Tabs -->
                            <div class="flex flex-wrap gap-1 border-b border-gray-300 dark:border-gray-800">
                                <template
                                    v-for="type in types"
                                    :key="type.name"
                                >
                                    <span
                                        :class="[
                                            'inline-block px-3 py-2.5 border-b-2 cursor-pointer text-sm font-medium',
                                            selectedType == type.name
                                                ? 'text-brandColor border-brandColor'
                                                : 'text-gray-600 dark:text-gray-300 border-transparent hover:text-gray-800 hover:border-gray-400 dark:hover:border-gray-400 dark:hover:text-white'
                                        ]"
                                        @click="selectedType = type.name"
                                    >
                                        @{{ type.label }}
                                    </span>
                                </template>
                            </div>

                            <!-- Tab Panels -->
                            <div class="max-h-[60vh] overflow-y-auto pt-4">
                                @if ($canCreateLead)
                                    <div v-show="selectedType == 'lead'">
                                        <x-admin::form
                                            v-slot="{ meta, errors, handleSubmit }"
                                            as="div"
                                            ref="leadFormWrapper"
                                        >
                                            <form
                                                @submit="handleSubmit($event, createLead)"
                                                ref="leadForm"
                                            >
                                                <input type="hidden" name="quick_add" value="lead" />

                                                <div class="flex gap-4 max-sm:flex-wrap">
                                                    <div class="w-1/2">
                                                        <x-admin::attributes
                                                            :custom-attributes="app('Webkul\Attribute\Repositories\AttributeRepository')->findWhere([
                                                                ['code', 'IN', ['title']],
                                                                'entity_type' => 'leads',
                                                                'quick_add' => 1,
                                                            ])"
                                                        />
                                                    </div>

                                                    <div class="w-1/2">
                                                        <x-admin::attributes
                                                            :custom-attributes="app('Webkul\Attribute\Repositories\AttributeRepository')->findWhere([
                                                                ['code', 'IN', ['lead_value']],
                                                                'entity_type' => 'leads',
                                                                'quick_add' => 1,
                                                            ])"
                                                        />
                                                    </div>
                                                </div>

                                                <x-admin::attributes
                                                    :custom-attributes="app('Webkul\Attribute\Repositories\AttributeRepository')->findWhere([
                                                        ['code', 'IN', ['description']],
                                                        'entity_type' => 'leads',
                                                        'quick_add' => 1,
                                                    ])"
                                                />

                                                <div class="flex gap-4 max-sm:flex-wrap">
                                                    <div class="w-1/2">
                                                        <x-admin::attributes
                                                            :custom-attributes="app('Webkul\Attribute\Repositories\AttributeRepository')->findWhere([
                                                                ['code', 'IN', ['lead_pipeline_id']],
                                                                'entity_type' => 'leads',
                                                                'quick_add' => 1,
                                                            ])"
                                                        />
                                                    </div>

                                                    <div class="w-1/2">
                                                        <x-admin::attributes
                                                            :custom-attributes="app('Webkul\Attribute\Repositories\AttributeRepository')->findWhere([
                                                                ['code', 'IN', ['lead_pipeline_stage_id']],
                                                                'entity_type' => 'leads',
                                                                'quick_add' => 1,
                                                            ])"
                                                        />
                                                    </div>
                                                </div>

                                                <div class="flex gap-4 max-sm:flex-wrap">
                                                    <div class="w-1/2">
                                                        <x-admin::attributes
                                                            :custom-attributes="app('Webkul\Attribute\Repositories\AttributeRepository')->findWhere([
                                                                ['code', 'IN', ['lead_type_id']],
                                                                'entity_type' => 'leads',
                                                                'quick_add' => 1,
                                                            ])"
                                                        />
                                                    </div>

                                                    <div class="w-1/2">
                                                        <x-admin::attributes
                                                            :custom-attributes="app('Webkul\Attribute\Repositories\AttributeRepository')->findWhere([
                                                                ['code', 'IN', ['lead_source_id']],
                                                                'entity_type' => 'leads',
                                                                'quick_add' => 1,
                                                            ])"
                                                        />
                                                    </div>
                                                </div>

                                                <div class="flex gap-4 max-sm:flex-wrap">
                                                    <div class="w-1/2">
                                                        <x-admin::attributes
                                                            :custom-attributes="app('Webkul\Attribute\Repositories\AttributeRepository')->findWhere([
                                                                ['code', 'IN', ['user_id']],
                                                                'entity_type' => 'leads',
                                                                'quick_add' => 1,
                                                            ])"
                                                        />
                                                    </div>

                                                    <div class="w-1/2">
                                                        <x-admin::attributes
                                                            :custom-attributes="app('Webkul\Attribute\Repositories\AttributeRepository')->findWhere([
                                                                ['code', 'IN', ['expected_close_date']],
                                                                'entity_type' => 'leads',
                                                                'quick_add' => 1,
                                                            ])"
                                                            :custom-validations="[
                                                                'expected_close_date' => [
                                                                    'date_format:yyyy-MM-dd',
                                                                    'after:' .  \Carbon\Carbon::yesterday()->format('Y-m-d'),
                                                                ],
                                                            ]"
                                                        />
                                                    </div>
                                                </div>
                                            </form>
                                        </x-admin::form>
                                    </div>
                                @endif

                                @if ($canCreatePerson)
                                    <div v-show="selectedType == 'person'">
                                        <x-admin::form
                                            v-slot="{ meta, errors, handleSubmit }"
                                            as="div"
                                            ref="personFormWrapper"
                                        >
                                            <form
                                                @submit="handleSubmit($event, createPerson)"
                                                ref="personForm"
                                            >
                                                <input type="hidden" name="quick_add" value="person" />
                                                <input type="hidden" name="entity_type" value="persons" />

                                                <x-admin::attributes
                                                    :custom-attributes="app('Webkul\Attribute\Repositories\AttributeRepository')->findWhere([
                                                        'entity_type' => 'persons',
                                                        'quick_add' => 1,
                                                    ])"
                                                />
                                            </form>
                                        </x-admin::form>
                                    </div>
                                @endif

                                @if ($canCreateOrganization)
                                    <div v-show="selectedType == 'organization'">
                                        <x-admin::form
                                            v-slot="{ meta, errors, handleSubmit }"
                                            as="div"
                                            ref="organizationFormWrapper"
                                        >
                                            <form
                                                @submit="handleSubmit($event, createOrganization)"
                                                ref="organizationForm"
                                            >
                                                <input type="hidden" name="quick_add" value="organization" />
                                                <input type="hidden" name="entity_type" value="organizations" />

                                                <x-admin::attributes
                                                    :custom-attributes="app('Webkul\Attribute\Repositories\AttributeRepository')->findWhere([
                                                        'entity_type' => 'organizations',
                                                        'quick_add' => 1,
                                                    ])"
                                                />
                                            </form>
                                        </x-admin::form>
                                    </div>
                                @endif

                                @if ($canCreateProduct)
                                    <div v-show="selectedType == 'product'">
                                        <x-admin::form
                                            v-slot="{ meta, errors, handleSubmit }"
                                            as="div"
                                            ref="productFormWrapper"
                                        >
                                            <form
                                                @submit="handleSubmit($event, createProduct)"
                                                ref="productForm"
                                            >
                                                <input type="hidden" name="quick_add" value="product" />
                                                <input type="hidden" name="entity_type" value="products" />

                                                <x-admin::attributes
                                                    :custom-attributes="app('Webkul\Attribute\Repositories\AttributeRepository')->findWhere([
                                                        'entity_type' => 'products',
                                                        'quick_add' => 1,
                                                    ])"
                                                />
                                            </form>
                                        </x-admin::form>
                                    </div>
                                @endif

                                @if ($canCreateMail)
                                    <div v-show="selectedType == 'mail'">
                                        <x-admin::form
                                            v-slot="{ meta, errors, handleSubmit }"
                                            as="div"
                                            ref="mailFormWrapper"
                                        >
                                            <form
                                                @submit="handleSubmit($event, createMail)"
                                                ref="mailForm"
                                            >
                                                <input type="hidden" name="is_draft" value="0" />

                                                <!-- To -->
                                                <x-admin::form.control-group>
                                                    <x-admin::form.control-group.label class="required">
                                                        @lang('admin::app.components.activities.actions.mail.to')
                                                    </x-admin::form.control-group.label>

                                                    <div class="relative">
                                                        <x-admin::form.control-group.control
                                                            type="tags"
                                                            name="reply_to"
                                                            rules="required"
                                                            input-rules="email"
                                                            :label="trans('admin::app.components.activities.actions.mail.to')"
                                                            :placeholder="trans('admin::app.components.activities.actions.mail.enter-emails')"
                                                        />

                                                        <div class="absolute top-[9px] flex items-center gap-2 ltr:right-2 rtl:left-2">
                                                            <span
                                                                class="cursor-pointer font-medium hover:underline dark:text-white"
                                                                @click="showCC = ! showCC"
                                                            >
                                                                @lang('admin::app.components.activities.actions.mail.cc')
                                                            </span>

                                                            <span
                                                                class="cursor-pointer font-medium hover:underline dark:text-white"
                                                                @click="showBCC = ! showBCC"
                                                            >
                                                                @lang('admin::app.components.activities.actions.mail.bcc')
                                                            </span>
                                                        </div>
                                                    </div>

                                                    <x-admin::form.control-group.error control-name="reply_to" />
                                                </x-admin::form.control-group>

                                                <template v-if="showCC">
                                                    <!-- Cc -->
                                                    <x-admin::form.control-group>
                                                        <x-admin::form.control-group.label>
                                                            @lang('admin::app.components.activities.actions.mail.cc')
                                                        </x-admin::form.control-group.label>

                                                        <x-admin::form.control-group.control
                                                            type="tags"
                                                            name="cc"
                                                            input-rules="email"
                                                            :label="trans('admin::app.components.activities.actions.mail.cc')"
                                                            :placeholder="trans('admin::app.components.activities.actions.mail.enter-emails')"
                                                        />

                                                        <x-admin::form.control-group.error control-name="cc" />
                                                    </x-admin::form.control-group>
                                                </template>

                                                <template v-if="showBCC">
                                                    <!-- Cc -->
                                                    <x-admin::form.control-group>
                                                        <x-admin::form.control-group.label>
                                                            @lang('admin::app.components.activities.actions.mail.bcc')
                                                        </x-admin::form.control-group.label>

                                                        <x-admin::form.control-group.control
                                                            type="tags"
                                                            name="bcc"
                                                            input-rules="email"
                                                            :label="trans('admin::app.components.activities.actions.mail.bcc')"
                                                            :placeholder="trans('admin::app.components.activities.actions.mail.enter-emails')"
                                                        />

                                                        <x-admin::form.control-group.error control-name="bcc" />
                                                    </x-admin::form.control-group>
                                                </template>

                                                <!-- Subject -->
                                                <x-admin::form.control-group>
                                                    <x-admin::form.control-group.label>
                                                        @lang('admin::app.layouts.quick-add.subject')
                                                    </x-admin::form.control-group.label>

                                                    <x-admin::form.control-group.control
                                                        type="text"
                                                        id="quick-mail-subject"
                                                        name="subject"
                                                        :label="trans('admin::app.layouts.quick-add.subject')"
                                                        :placeholder="trans('admin::app.layouts.quick-add.subject')"
                                                    />

                                                    <x-admin::form.control-group.error control-name="subject" />
                                                </x-admin::form.control-group>

                                                <!-- Content (with TinyMCE editor) -->
                                                <x-admin::form.control-group>
                                                    <x-admin::form.control-group.label class="required">
                                                        @lang('admin::app.layouts.quick-add.message')
                                                    </x-admin::form.control-group.label>

                                                    <x-admin::form.control-group.control
                                                        type="textarea"
                                                        id="quick-mail-reply"
                                                        name="reply"
                                                        rules="required"
                                                        rows="2"
                                                        :tinymce="true"
                                                        :label="trans('admin::app.layouts.quick-add.message')"
                                                        :placeholder="trans('admin::app.layouts.quick-add.message')"
                                                    />

                                                    <x-admin::form.control-group.error control-name="reply" />
                                                </x-admin::form.control-group>

                                                <!-- Attachments -->
                                                <x-admin::form.control-group class="!mb-0">
                                                    <x-admin::attachments
                                                        name="attachments"
                                                        allow-multiple="true"
                                                    />
                                                </x-admin::form.control-group>
                                            </form>
                                        </x-admin::form>
                                    </div>
                                @endif

                                @if ($canCreateAttribute)
                                    <div v-show="selectedType == 'attribute'">
                                        <x-admin::form
                                            v-slot="{ meta, errors, handleSubmit }"
                                            as="div"
                                            ref="attributeFormWrapper"
                                        >
                                            <form
                                                @submit="handleSubmit($event, createAttribute)"
                                                ref="attributeForm"
                                            >
                                                <div class="flex gap-4 max-sm:flex-wrap">
                                                    <x-admin::form.control-group class="w-1/2">
                                                        <x-admin::form.control-group.label class="required">
                                                            @lang('admin::app.settings.attributes.create.name')
                                                        </x-admin::form.control-group.label>

                                                        <x-admin::form.control-group.control
                                                            type="text"
                                                            id="quick-attribute-name"
                                                            name="name"
                                                            rules="required"
                                                            :label="trans('admin::app.settings.attributes.create.name')"
                                                            :placeholder="trans('admin::app.settings.attributes.create.name')"
                                                        />

                                                        <x-admin::form.control-group.error control-name="name" />
                                                    </x-admin::form.control-group>

                                                    <x-admin::form.control-group class="w-1/2">
                                                        <x-admin::form.control-group.label class="required">
                                                            @lang('admin::app.settings.attributes.create.code')
                                                        </x-admin::form.control-group.label>

                                                        <x-admin::form.control-group.control
                                                            type="text"
                                                            id="quick-attribute-code"
                                                            name="code"
                                                            rules="required"
                                                            :label="trans('admin::app.settings.attributes.create.code')"
                                                            :placeholder="trans('admin::app.settings.attributes.create.code')"
                                                        />

                                                        <x-admin::form.control-group.error control-name="code" />
                                                    </x-admin::form.control-group>
                                                </div>

                                                <div class="flex gap-4 max-sm:flex-wrap">
                                                    <x-admin::form.control-group class="w-1/2">
                                                        <x-admin::form.control-group.label class="required">
                                                            @lang('admin::app.settings.attributes.create.type')
                                                        </x-admin::form.control-group.label>

                                                        <x-admin::form.control-group.control
                                                            type="select"
                                                            id="quick-attribute-type"
                                                            name="type"
                                                            rules="required"
                                                            :label="trans('admin::app.settings.attributes.create.type')"
                                                        >
                                                            @foreach ($attributeTypesForQuickAdd as $type)
                                                                <option value="{{ $type }}" @selected($type === 'text')>
                                                                    @lang('admin::app.settings.attributes.create.'.$type)
                                                                </option>
                                                            @endforeach
                                                        </x-admin::form.control-group.control>

                                                        <x-admin::form.control-group.error control-name="type" />
                                                    </x-admin::form.control-group>

                                                    <x-admin::form.control-group class="w-1/2">
                                                        <x-admin::form.control-group.label class="required">
                                                            @lang('admin::app.settings.attributes.create.entity-type')
                                                        </x-admin::form.control-group.label>

                                                        <x-admin::form.control-group.control
                                                            type="select"
                                                            id="quick-attribute-entity-type"
                                                            name="entity_type"
                                                            rules="required"
                                                            :label="trans('admin::app.settings.attributes.create.entity-type')"
                                                        >
                                                            @foreach ($attributeEntityTypesForQuickAdd as $key => $entityType)
                                                                <option value="{{ $key }}">
                                                                    {{ trans($entityType['name']) }}
                                                                </option>
                                                            @endforeach
                                                        </x-admin::form.control-group.control>

                                                        <x-admin::form.control-group.error control-name="entity_type" />
                                                    </x-admin::form.control-group>
                                                </div>

                                                <x-admin::form.control-group class="!mb-2 flex items-center gap-2.5">
                                                    <x-admin::form.control-group.control
                                                        type="checkbox"
                                                        id="quick-attribute-is-required"
                                                        name="is_required"
                                                        value="1"
                                                        for="quick-attribute-is-required"
                                                    />

                                                    <label
                                                        class="cursor-pointer text-xs font-medium text-gray-600 dark:text-gray-300"
                                                        for="quick-attribute-is-required"
                                                    >
                                                        @lang('admin::app.settings.attributes.create.is-required')
                                                    </label>
                                                </x-admin::form.control-group>

                                                <x-admin::form.control-group class="flex items-center gap-2.5">
                                                    <x-admin::form.control-group.control
                                                        type="checkbox"
                                                        id="quick-attribute-quick-add"
                                                        name="quick_add"
                                                        value="1"
                                                        for="quick-attribute-quick-add"
                                                    />

                                                    <label
                                                        class="cursor-pointer text-xs font-medium text-gray-600 dark:text-gray-300"
                                                        for="quick-attribute-quick-add"
                                                    >
                                                        @lang('admin::app.settings.attributes.create.quick_add')
                                                    </label>
                                                </x-admin::form.control-group>
                                            </form>
                                        </x-admin::form>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </x-slot>

                    <x-slot:footer>
                        <x-admin::button
                            class="primary-button"
                            :title="trans('admin::app.layouts.quick-add.save')"
                            ::loading="isStoring"
                            ::disabled="isStoring"
                            @click="submit"
                        />
                    </x-slot>
                </x-admin::modal>
            </Teleport>
        </script>

        <script type="module">
            app.component('v-quick-add', {
                template: '#v-quick-add-template',

                data() {
                    return {
                        isStoring: false,

                        selectedType: @json($defaultQuickAddTab),

                        types: [
                            @if ($canCreateLead)
                                { name: 'lead',         label: "@lang('admin::app.layouts.lead')" },
                            @endif
                            @if ($canCreatePerson)
                                { name: 'person',       label: "@lang('admin::app.layouts.person')" },
                            @endif
                            @if ($canCreateOrganization)
                                { name: 'organization', label: "@lang('admin::app.layouts.organization')" },
                            @endif
                            @if ($canCreateProduct)
                                { name: 'product',      label: "@lang('admin::app.layouts.product')" },
                            @endif
                            @if ($canCreateMail)
                                { name: 'mail',         label: "@lang('admin::app.layouts.email')" },
                            @endif
                            @if ($canCreateAttribute)
                                { name: 'attribute',    label: "@lang('admin::app.layouts.attribute')" },
                            @endif
                        ],

                        endpoints: {
                            lead:         "{{ route('admin.leads.store') }}",
                            person:       "{{ route('admin.contacts.persons.store') }}",
                            organization: "{{ route('admin.contacts.organizations.store') }}",
                            product:      "{{ route('admin.products.store') }}",
                            mail:         "{{ route('admin.mail.store') }}",
                            attribute:    "{{ route('admin.settings.attributes.store') }}",
                            role:         "{{ route('admin.settings.roles.store') }}",
                            user:         "{{ route('admin.settings.users.store') }}",
                        },
                    };
                },

                methods: {
                    open() {
                        this.$refs.modal.open();
                    },

                    formRefFor(type) {
                        return type + 'Form';
                    },

                    submit() {
                        const ref = this.formRefFor(this.selectedType);

                        if (! this.$refs[ref]) {
                            return;
                        }

                        if (typeof this.$refs[ref].requestSubmit === 'function') {
                            this.$refs[ref].requestSubmit();
                        } else {
                            this.$refs[ref].dispatchEvent(new Event('submit', { cancelable: true, bubbles: true }));
                        }
                    },

                    createLead(params, { setErrors })         { this.store('lead', setErrors); },
                    createPerson(params, { setErrors })       { this.store('person', setErrors); },
                    createOrganization(params, { setErrors }) { this.store('organization', setErrors); },
                    createProduct(params, { setErrors })      { this.store('product', setErrors); },
                    createMail(params, { setErrors })         { this.store('mail', setErrors); },
                    createAttribute(params, { setErrors })    { this.store('attribute', setErrors); },
                    createRole(params, { setErrors })         { this.store('role', setErrors); },
                    createUser(params, { setErrors })         { this.store('user', setErrors); },

                    store(type, setErrors) {
                        const formEl = this.$refs[this.formRefFor(type)];

                        if (! formEl) {
                            return;
                        }

                        this.isStoring = true;

                        const formData = new FormData(formEl);

                        this.$axios.post(this.endpoints[type], formData)
                            .then(response => {
                                this.$emitter.emit('add-flash', { type: 'success', message: response.data.message });

                                this.$refs.modal.close();

                                formEl.reset();
                            })
                            .catch(error => {
                                if (error.response?.status == 422) {
                                    setErrors(error.response.data.errors);
                                } else {
                                    this.$emitter.emit('add-flash', {
                                        type: 'error',
                                        message: error.response?.data?.message || error.message,
                                    });
                                }
                            })
                            .finally(() => {
                                this.isStoring = false;
                            });
                    },
                },
            });
        </script>
    @endPushOnce
</div>
