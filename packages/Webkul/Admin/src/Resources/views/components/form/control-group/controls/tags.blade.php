<v-control-tags {{ $attributes }} :errors="errors"></v-control-tags>

@pushOnce('scripts')
    <script type="text/x-template" id="v-control-tags-template">
        <div class="flex min-h-[38px] w-full items-center rounded border border-gray-200 px-2.5 py-1.5 text-sm font-normal text-gray-800 transition-all hover:border-gray-400">
            <ul class="flex flex-wrap items-center gap-1">
                <li
                    class="flex items-center gap-1 rounded-md bg-slate-100 pl-2"
                    v-for="tag in tags"
                >
                    <input type="hidden" :name="name" :value="tag"/>

                    @{{ tag }}

                    <span
                        class="icon-cross-large cursor-pointer p-0.5 text-xl"
                        @click="removeTag(tag)"
                    ></span>
                </li>

                <li>
                    <template v-if="! tags.length && input == ''">
                        <v-field
                            v-slot="{ field, errors }"
                            :name="name"
                            v-model="input"
                            ::rules="rules"
                        >
                            <input
                                type="hidden"
                                :name="name"
                                v-bind="field"
                            />
                        </v-field>
                    </template>

                    <v-field
                        v-slot="{ field, errors }"
                        :name="name"
                        v-model="input"
                        rules="email"
                    >
                        <input
                            type="text"
                            :name="name"
                            v-bind="field"
                            :placeholder="placeholder"
                            @keydown.enter.prevent="addTag"
                        />
                    </v-field>
                </li>
            </ul>
        </div>
    </script>

    <script type="module">
        app.component('v-control-tags', {
            template: '#v-control-tags-template',

            props: [
                'name',
                'label',
                'placeholder',
                'rules',
                'data',
                'errors',
            ],

            data: function () {
                return {
                    tags: this.data ? this.data : [],

                    input: '',
                }
            },

            methods: {
                addTag: function() {
                    this.tags.push(this.input.trim());
                    
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
