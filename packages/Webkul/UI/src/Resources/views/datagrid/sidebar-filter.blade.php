@push('scripts')
    <script type="text/x-template" id="sidebar-filter">
        <div :class="`sidebar-filter ${sidebarFilter ? 'show' : ''}`" v-if="sidebarFilter">
            <header>
                <button type="button" class="btn btn-sm btn-white text-black fs-18 pl-0">
                    {{ __('ui.datagrid.filter.title') }}
                </button>
    
                <div class="float-right">
                    <button type="button" class="btn btn-sm btn-white">
                        {{ __('ui.datagrid.filter.remove_all') }}
                    </button>
    
                    <button type="button" class="btn btn-sm btn-primary">
                        {{ __('ui.datagrid.filter.apply_title') }}
                    </button>
    
                    <i class="fa fa-times ml-5" @click="toggleSidebarFilter"></i>
                </div>
            </header>
            
            <div class="control-group">
                <label>Deal Amount Range</label>
    
                <div class="field-container">
                    <input
                        type="text"
                        placeholder="Start"
                        class="control half"
                        v-model="filterData.deal_amount_range[0]"
                    />
    
                    <span class="middle-text">to</span>
                    
                    <input
                        type="text"
                        placeholder="End"
                        class="control half"
                        v-model="filterData.deal_amount_range[1]"
                    />
    
                    <i class="fa fa-times ml-10"></i>
                </div>
            </div>
    
            <div class="control-group">
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
    
            <div class="control-group">
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
                    <select class="control">
                        <option value="null" disabled selected>
                            Select Status
                        </option>
                        <option>Shubham Mehrotra</option>
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
                    <span class="control">+ Add Number</span>
    
                    <i class="fa fa-times ml-10"></i>
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
            </div>
        </div>
    </script>

    <script>
        import { mapState, mapActions } from 'vuex';

        Vue.component('sidebar-filter', {
            template: '#sidebar-filter',

            props: [
            ],

            data: function () {
                return {
                    filterData: {
                        deal_amount_range: ['1000', '50000'],
                        contact_person: ['Shubham', 'Webkul'],
                        date_range: ['2021-04-02', '2021-04-03'],
                        status: ['Won', 'Lost'],
                        phone_number: ['987654321', '987654321'],
                    },

                    addField: {
                        contact_person: false,
                        phone_number: false,
                    },
                }
            },

            computed: {
                ...mapState({
                    sidebarFilter : state => state.sidebarFilter,
                }),
            },

            methods: {
                ...mapActions([
                    'toggleSidebarFilter',
                ]),

                toggleInput: function (key) {
                    this.addField[key] = ! this.addField[key];
                },

                pushFieldValue: function (key, {target}) {
                    this.addField[key] = false;
                    this.filterData[key].push(target.value);

                    target.value = "";
                },

                removeFieldValue: function (key, index) {
                    const values = this.filterData[key];
                    values.splice(index, 1);
                    
                    this.filterData[key] = values;
                }
            },
        })
    </script>
@endpush
