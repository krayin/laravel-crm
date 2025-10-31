@aware(['product' => null])

{!! view_render_event('admin.products.create.category.form_controls.before') !!}

<v-category-component :data='@json($categoryData ?? null)'></v-category-component>

{!! view_render_event('admin.products.create.category.form_controls.after') !!}

@pushOnce('scripts')
    <script
        type="text/x-template"
        id="v-category-component-template"
    >
        <!-- Category Search Lookup -->
        <x-admin::form.control-group>
            <x-admin::form.control-group.label>
                @lang('admin::app.products.create.category')
            </x-admin::form.control-group.label>

            <x-admin::lookup
                ::src="src"
                name="category_id"
                ::params="params"
                :label="trans('admin::app.products.create.category')"
                ::value="{id: category.id, name: category.full_name || category.name}"
                :placeholder="trans('admin::app.products.create.select_category')"
                @on-selected="addCategory"
                :can-add-new="false"
                :preload="true"
            />

            <x-admin::form.control-group.error control-name="category_id" />
        </x-admin::form.control-group>
    </script>

    <script type="module">
        app.component('v-category-component', {
            template: '#v-category-component-template',

            props: ['data'],

            data () {
                return {
                    is_searching: false,

                    category: this.data ? this.data : {
                        id: '',
                        name: '',
                        full_name: ''
                    },

                    categories: [],
                }
            },

            computed: {
                src() {
                    return "{{ route('admin.products.categories') }}";
                },

                params() {
                    return {
                        params: {
                            query: this.category['name']
                        }
                    }
                }
            },

            methods: {
                addCategory (category) {
                    this.category = {
                        id: category.id,
                        name: category.name,
                        full_name: category.full_name || category.name
                    };
                },
            }
        });
    </script>
@endPushOnce