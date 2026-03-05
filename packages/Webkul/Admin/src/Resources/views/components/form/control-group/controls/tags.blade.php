<v-control-tags
    :errors="errors"
    {{ $attributes }}
    v-bind="$attrs"
></v-control-tags>

@pushOnce('scripts')
    <script
        type="text/x-template"
        id="v-control-tags-template"
    >
        <div 
            class="flex min-h-[38px] w-full items-center rounded border border-gray-200 px-2.5 py-1.5 text-sm font-normal text-gray-800 transition-all hover:border-gray-400 dark:border-gray-800 dark:text-white dark:hover:border-gray-400"
            :class="[errors[`temp-${name}`] ? 'border !border-red-600 hover:border-red-600' : '']"
        >
            <ul
                class="flex flex-wrap items-center gap-1"
                v-bind="$attrs"
            >
                <li
                    v-for="(tag, index) in tags"
                    :key="index"
                    class="flex items-center gap-1 rounded-md bg-gray-100 dark:bg-gray-950 ltr:pl-2 rtl:pr-2"
                >
                    <x-admin::form.control-group.control
                        type="hidden"
                        ::name="name + '[' + index + ']'"
                        ::value="tag"
                    />

                    @{{ tag }}

                    <span
                        class="icon-cross-large cursor-pointer p-0.5 text-xl"
                        @click="removeTag(tag)"
                    ></span>
                </li>

                <li :class="['w-full', tags.length && 'mt-1.5']">
                    <v-field
                        v-slot="{ field, errors }"
                        :name="'temp-' + name"
                        v-model="input"
                        :rules="tags.length ? inputRules : [inputRules, rules].filter(Boolean).join('|')"
                        :label="label"
                    >
                        <input
                            type="text"
                            :name="'temp-' + name"
                            v-bind="field"
                            class="w-full dark:!bg-gray-900"
                            :placeholder="placeholder"
                            :label="label"
                            @keydown.enter.prevent="addTag"
                            autocomplete="new-email"
                            @blur="addTag"
                        />
                    </v-field>

                    <template v-if="! tags.length && input != ''">
                        <v-field
                            v-slot="{ field, errors }"
                            :name="name + '[' + 0 +']'"
                            :value="input"
                            :rules="inputRules"
                            :label="label"
                        >
                            <input
                                type="hidden"
                                :name="name + '[0]'"
                                v-bind="field"
                            />
                        </v-field>
                    </template>
                </li>
            </ul>
        </div>

        <v-error-message
            :name="'temp-' + name"
            v-slot="{ message }"
        >
            <p
                class="mt-1 text-xs italic text-red-600"
                v-text="message"
            >
            </p>
        </v-error-message>
    </script>

    <script type="module">
        app.component('v-control-tags', {
            template: '#v-control-tags-template',

            props: {
                name: {
                    type: String,
                    required: true,
                },

                label: {
                    type: String,
                    default: '',
                },

                placeholder: {
                    type: String,
                    default: '',
                },

                rules: {
                    type: String,
                    default: '',
                },

                inputRules: {
                    type: String,
                    default: '',
                },

                data: {
                    type: Array,
                    default: () => [],
                },

                errors: {
                    type: Object,
                    default: () => {},
                },

                allowDuplicates: {
                    type: Boolean,
                    default: true,
                },
            },

            data() {
                return {
                    tags: this.data ? this.data : [],

                    input: '',
                }
            },

            methods: {
                addTag() {
                    if (this.errors['temp-' + this.name]) {
                        return;
                    }

                    const tag = this.input.trim();

                    if (! tag) {
                        return;
                    }

                    if (
                        ! this.allowDuplicates
                        && this.tags.includes(tag)
                    ) {
                        this.input = '';

                        return;
                    }

                    this.tags.push(tag);

                    this.$emit('tags-updated', this.tags);

                    this.input = '';
                },

                removeTag: function(tag) {
                    this.tags = this.tags.filter(function (tempTag) {
                        return tempTag != tag;
                    });

                    this.$emit('tags-updated', this.tags);
                },
            }
        });
    </script>
@endpushOnce
