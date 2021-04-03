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

                <i class="fa fa-times ml-5" @click="toggleSidebarFilter"></i>
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
                    <span class="control" @click="toggleInput(key)">
                        + {{ data.placeholder }}
                    </span>
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

                <i class="fa fa-times ml-10 float-right"></i>
            </div>

            <template v-if="data.type == 'add'">
                <div class="enter-new" v-show="addField[key]">
                    <input
                        type="text"
                        class="control mb-10"
                        :placeholder="data.input_field_placeholder"
                        @keyup.enter="pushFieldValue(key, $event)"
                    />
                </div>
            </template>

            <template v-if="data.type == 'add' || data.type == 'dropdown'">
                <div class="selected-options">
                    <span
                        :key="index"
                        v-for="(value, index) in data.values"
                        class="badge badge-md badge-pill badge-secondary"
                    >
                        {{ value }}

                        <i class="fa fa-times ml-10" @click="removeFieldValue(key, index)"></i>
                    </span>
                </div>
            </template>
        </div>

        <!-- <div class="control-group">
            <label>Contact Person</label>

            <div class="field-container">
                <span class="control" @click="toggleInput('contact_person')">+ Add Person</span>

                <i class="fa fa-times ml-10"></i>
            </div>

            <div class="enter-new" v-show="addField.contact_person">
                <input
                    type="text"
                    class="control mb-10"
                    placeholder="Enter name"
                    @keyup.enter="pushFieldValue('contact_person', $event)"
                />
            </div>

            <div class="selected-options">
                <span   
                    :key="index"
                    v-for="(person, index) in filterData.contact_person"
                    class="badge badge-md badge-pill badge-secondary"
                >
                    {{ person }}

                    <i class="fa fa-times ml-10" @click="removeFieldValue('contact_person', index)"></i>
                </span>
            </div>
        </div>

        <div class="control-group date">
            <label>Date Range</label>

            <div class="field-container">
                <date>
                    <input
                        type="text"
                        class="control half"
                        placeholder="Start Date"
                        v-model="filterData.date_range[0]"
                    />
                </date>

                <span class="middle-text">to</span>
                
                <date>
                    <input
                        type="text"
                        class="control half"
                        placeholder="End Date"
                        v-model="filterData.date_range[1]"
                    />
                </date>

                <i class="fa fa-times ml-10"></i>
            </div>
        </div>

        <div class="control-group">
            <label>Status</label>

            <div class="field-container">
                <select class="control" @change="pushFieldValue('status', $event)">
                    <option value="" disabled selected>
                        Select Status
                    </option>
                    <option value="Won">Won</option>
                    <option value="Lost">Lost</option>
                </select>

                <i class="fa fa-times ml-10"></i>
            </div>
            
            <div class="selected-options">
                <span
                    :key="index"
                    v-for="(status, index) in filterData.status"
                    class="badge badge-md badge-pill badge-secondary"
                >
                    {{ status }}

                    <i class="fa fa-times ml-10" @click="removeFieldValue('status', index)"></i>
                </span>
            </div>
        </div>

        <div class="control-group">
            <label>Phone</label>

            <div class="field-container">
                <span class="control" @click="toggleInput('phone_number')">+ Add Number</span>

                <i class="fa fa-times ml-10"></i>
            </div>

            <div class="enter-new" v-show="addField.phone_number">
                <input
                    type="text"
                    class="control mb-10"
                    placeholder="Enter number"
                    @keyup.enter="pushFieldValue('phone_number', $event)"
                />
            </div>

            <div class="selected-options">
                <span
                    :key="index"
                    v-for="(number, index) in filterData.phone_number"
                    class="badge badge-md badge-pill badge-secondary"
                >
                    {{ number }}

                    <i class="fa fa-times ml-10" @click="removeFieldValue('phone_number', index)"></i>
                </span>
            </div>
        </div> -->
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
                values.push(target.value);

                this.updateFilterValues({
                    key,
                    values
                });

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
            }
        },
    };
</script>