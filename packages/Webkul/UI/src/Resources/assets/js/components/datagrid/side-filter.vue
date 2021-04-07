<template>
    <div :class="`sidebar-filter ${sidebarFilter ? 'show' : ''}`" v-if="sidebarFilter">
        <header>
            <button type="button" class="btn btn-sm btn-white text-black fs-18 pl-0">
                {{ __('ui.datagrid.filter.title') }}
            </button>

            <div class="float-right">
                <button type="button" class="btn btn-sm btn-white" @click="removeAll">
                    {{ __('ui.datagrid.filter.remove_all') }}
                </button>

                <button type="button" class="btn btn-sm btn-primary">
                    {{ __('ui.datagrid.filter.apply_title') }}
                </button>

                <i class="icon close-icon ml-5" @click="toggleSidebarFilter"></i>
            </div>
        </header>
        
        <div :class="`control-group ${data.type == 'date_range' ? 'date' : ''}`" :key="key" v-for="(data, key) in filterData">
            <label>{{ data.label }}</label>

            <div class="field-container">
                <template v-if="data.type == 'integer_range'">
                    <input
                        type="text"
                        placeholder="Start"
                        class="control half"
                        v-model="filterData[key].values[0]"
                    />

                    <span class="middle-text">to</span>
                    
                    <input
                        type="text"
                        placeholder="End"
                        class="control half"
                        v-model="filterData[key].values[1]"
                    />
                </template>

                <template v-else-if="data.type == 'add'">
                    <span class="control" @click="toggleInput(key)" v-if="! addField[key]">
                        <i class="icon add-icon"></i> {{ data.placeholder }}
                    </span>

                    <div class="enter-new" v-else>
                        <input
                            type="text"
                            class="control mb-10"
                            :placeholder="data.input_field_placeholder"
                            @keyup.enter="pushFieldValue(key, $event)"
                        />
                    </div>
                </template>

                <template v-else-if="data.type == 'date_range'">
                    <date>
                        <input
                            type="text"
                            class="control half"
                            placeholder="Start Date"
                            v-model="filterData[key].values[0]"
                        />
                    </date>

                    <span class="middle-text">to</span>
                    
                    <date>
                        <input
                            type="text"
                            class="control half"
                            placeholder="End Date"
                            v-model="filterData[key].values[1]"
                        />
                    </date>
                </template>

                <template v-else-if="data.type == 'dropdown'">
                    <select class="control" @change="pushFieldValue(key, $event)">
                        <option value="" disabled selected>
                            {{ data.placeholder }}
                        </option>
                        <option :value="value" :key="index" v-for="(value, index) in data.options">
                            {{ value }}
                        </option>
                    </select>
                </template>

                <i class="icon close-icon ml-10 float-right" @click="removeFilter({type: data.type, key})"></i>
            </div>

            <template v-if="data.type == 'add' || data.type == 'dropdown'">
                <div class="selected-options">
                    <span
                        :key="index"
                        v-for="(value, index) in data.values"
                        class="badge badge-md badge-pill badge-secondary"
                    >
                        {{ value }}

                        <i class="icon close-icon ml-10" @click="removeFieldValue(key, index)"></i>
                    </span>
                </div>
            </template>
        </div>
    </div>
</template>

<script>
    import { mapState, mapActions } from 'vuex';

    export default {
        props: [
        ],

        data: function () {
            return {
                addField: {
                    contact_person: false,
                    phone_number: false,
                },
            }
        },

        computed: {
            ...mapState({
                filterData      : state => state.filterData,
                sidebarFilter   : state => state.sidebarFilter,
            }),
        },

        methods: {
            ...mapActions([
                'toggleSidebarFilter',
                'updateFilterValues'
            ]),

            toggleInput: function (key) {
                this.addField[key] = ! this.addField[key];
            },

            pushFieldValue: function (key, {target}) {
                this.addField[key] = false;

                const values = this.filterData[key].values;

                if (values.indexOf(target.value) == -1) {
                    values.push(target.value);
    
                    this.updateFilterValues({
                        key,
                        values
                    });
                }

                target.value = "";
            },

            removeFieldValue: function (key, index) {
                const values = this.filterData[key].values;
                values.splice(index, 1);
                
                this.updateFilterValues({
                    key,
                    values
                });
            },

            removeAll: function () {
                for (const key in this.filterData) {
                    this.updateFilterValues({
                        key,
                        values: []
                    });
                }
            },

            removeFilter: function ({type, key}) {
                if (type == "add" && this.addField[key]) {
                    this.addField[key] = false;
                }
            }
        },
    };
</script>