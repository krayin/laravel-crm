<v-datagrid-mass-action
    :available="available"
    :applied="applied"
>
    {{ $slot }}
</v-datagrid-mass-action>

@pushOnce('scripts')
    <script
        type="text/x-template"
        id="v-datagrid-mass-action-template"
    >
        <!-- Empty slot for mass-action before -->
        <slot name="toolbar-left-mass-action-before"></slot>

        <slot
            name="mass-action"
            :available="available"
            :applied="applied"
            :mass-actions="massActions"
            :validate-mass-action="validateMassAction"
            :perform-mass-action="performMassAction"
        >
            <div class="fixed inset-x-0 bottom-24 mx-auto flex gap-2 max-w-max items-center justify-center rounded-lg bg-white p-2 shadow-lg dark:border-gray-800 dark:bg-gray-900 dark:text-gray-300">
                <div class="ltr:pl-2.5 rtl:pr-2.5">
                    <p class="text-sm font-light text-gray-800 dark:text-white">
                        @{{ "@lang('admin::app.components.datagrid.toolbar.selected')".replace(':total', available.meta.total) }}
                    </p>
                </div>

                <template v-if="available.massActions.some(action => action.icon !== 'icon-delete' && action.options.length)">
                    <template v-for="massAction in available.massActions.filter(action => action.icon !== 'icon-delete')">
                        <x-admin::dropdown class="rounded-lg dark:border-gray-800 dark:bg-gray-900 dark:text-gray-300 dark:hover:border-gray-400 dark:focus:border-gray-400">
                            <x-slot:toggle>
                                <button
                                    type="button"
                                    class="inline-flex w-full max-w-max cursor-pointer appearance-none items-center justify-between gap-x-2 rounded-md border bg-white px-2.5 py-1.5 text-center leading-6 text-gray-600 transition-all marker:shadow hover:border-gray-400 focus:border-gray-400 focus:ring-black dark:border-gray-800 dark:bg-gray-900 dark:text-gray-300 dark:hover:border-gray-400 dark:focus:border-gray-400"
                                >
                                    <span class="font-normal text-sm text-black">
                                        @{{ massAction.title }}
                                    </span>
        
                                    <span class="icon-down-arrow text-2xl"></span>
                                </button>
                            </x-slot>
        
                            <x-slot:menu class="!p-0 shadow-[0_5px_20px_rgba(0,0,0,0.15)] dark:border-gray-800">
                                <li v-for="option in massAction?.options">
                                    <a
                                        class="whitespace-no-wrap block rounded-t px-4 py-2 text-sm text-gray-600 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-950"
                                        href="javascript:void(0);"
                                        @click="performMassAction(massAction, option)"
                                    >
                                        @{{ option.label }}
                                    </a>
                                </li>
                            </x-slot>
                        </x-admin::dropdown>    
                    </template>
                </template>
                
                <button
                    type="button"
                    class="!bg-red-500 border-red-500 primary-button"
                    @click="performMassAction(available.massActions.find(action => action.icon === 'icon-delete'))"
                >
                    @{{ available.massActions.find(action => action.icon === 'icon-delete')?.title }}
                </button>

                <i 
                    class="icon-cross-large text-2xl cursor-pointer text-gray-600 dark:text-gray-300"
                    @click="massActions.indices = []"
                ></i>
            </div>
        </slot>

        <!-- Empty slot for mass-action after -->
        <slot name="toolbar-left-mass-action-after"></slot>
    </script>

    <script type="module">
        app.component('v-datagrid-mass-action', {
            template: '#v-datagrid-mass-action-template',

            props: ['available', 'applied'],

            data() {
                return {
                    massActions: {
                        meta: {
                            mode: 'none',

                            action: null,
                        },

                        indices: [],

                        value: null,
                    },
                };
            },

            mounted() {
                this.massActions = this.applied.massActions;
            },

            methods: {
                /**
                 * Validate mass action.
                 *
                 * @param {object} filters
                 * @returns {void}
                 */
                validateMassAction() {
                    if (! this.massActions.indices.length) {
                        this.$emitter.emit('add-flash', { type: 'warning', message: "@lang('admin::app.components.datagrid.index.no-records-selected')" });

                        return false;
                    }

                    if (! this.massActions.meta.action) {
                        this.$emitter.emit('add-flash', { type: 'warning', message: "@lang('admin::app.components.datagrid.index.must-select-a-mass-action')" });

                        return false;
                    }

                    if (
                        this.massActions.meta.action?.options?.length &&
                        this.massActions.value === null
                    ) {
                        this.$emitter.emit('add-flash', { type: 'warning', message: "@lang('admin::app.components.datagrid.index.must-select-a-mass-action-option')" });

                        return false;
                    }

                    return true;
                },

                /**
                 * Perform mass action.
                 *
                 * @param {object} currentAction
                 * @param {object} currentOption
                 * @returns {void}
                 */
                performMassAction(currentAction, currentOption = null) {
                    this.massActions.meta.action = currentAction;

                    if (currentOption) {
                        this.massActions.value = currentOption.value;
                    }

                    if (! this.validateMassAction()) {
                        return;
                    }

                    const { action } = this.massActions.meta;

                    const method = action.method.toLowerCase();

                    this.$emitter.emit('open-confirm-modal', {
                        agree: () => {
                            switch (method) {
                                case 'post':
                                case 'put':
                                case 'patch':
                                    this.$axios[method](action.url, {
                                            indices: this.massActions.indices,
                                            value: this.massActions.value,
                                        })
                                        .then((response) => {
                                            this.$emitter.emit('add-flash', { type: 'success', message: response.data.message });

                                            this.$parent.get();
                                        })
                                        .catch((error) => {
                                            this.$emitter.emit('add-flash', { type: 'error', message: error.response.data.message });

                                            this.$parent.get();
                                        });

                                    break;

                                case 'delete':
                                    this.$axios[method](action.url, {
                                            indices: this.massActions.indices
                                        })
                                        .then(response => {
                                            this.$emitter.emit('add-flash', { type: 'success', message: response.data.message });

                                            /**
                                             * Need to check reason why this.$emit('massActionSuccess') not emitting.
                                             */
                                            this.$parent.get();
                                        })
                                        .catch((error) => {
                                            this.$emitter.emit('add-flash', { type: 'error', message: error.response.data.message });

                                            /**
                                             * Need to check reason why this.$emit('massActionSuccess') not emitting.
                                             */
                                            this.$parent.get();
                                        });

                                    break;

                                default:
                                    console.error('Method not supported.');

                                    break;
                            }

                            this.massActions.indices  = [];
                        },
                    });
                },
            },
        });
    </script>
@endPushOnce
