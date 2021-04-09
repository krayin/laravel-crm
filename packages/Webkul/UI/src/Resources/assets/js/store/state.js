var state = {
    tableData: {},
    allSelected: false,
    sidebarFilter: false,
    selectedTableRows: [],
    filterData: {
        deal_amount_range: {
            label: 'Deal Amount Range',
            type: 'integer_range',
            values: ['1000', '50000'],
        },
        contact_person: {
            label: 'Contact Person',
            type: 'add',
            placeholder: 'Add Person',
            input_field_placeholder: 'Enter Person',
            values: ['Shubham', 'Webkul'],
        },
        date_range: {
            label: 'Date Range',
            type: 'date_range',
            values: ['2021-04-02', '2021-04-03'],
        },
        status: {
            label: 'Status',
            type: 'dropdown',
            placeholder: 'Select Status',
            values: ['Won', 'Lost'],
            options: ['Won', 'Lost']
        },
        phone_number: {
            label: 'Phone',
            type: 'add',
            placeholder: 'Add Number',
            input_field_placeholder: 'Enter Number',
            values: ['987654321', '987654321'],
        },
    },
    tabs: {
        type: [{
            'name'      : 'All',
            'isActive'  : true,
            'key'       : 'all',
        }, {
            'name'      : 'Call',
            'isActive'  : false,
            'key'       : 'call',
        }, {
            'name'      : 'Mail',
            'isActive'  : false,
            'key'       : 'mail',
        }, {
            'name'      : 'Meeting',
            'isActive'  : false,
            'key'       : 'meeting',
        }],

        duration: [{
            'name'      : 'Yesterday',
            'isActive'  : true,
            'key'       : 'yesterday',
        }, {
            'name'      : 'Today',
            'isActive'  : false,
            'key'       : 'today',
        }, {
            'name'      : 'Tomorrow',
            'isActive'  : false,
            'key'       : 'tomorrow',
        }, {
            'name'      : 'This week',
            'isActive'  : false,
            'key'       : 'this_week',
        }, {
            'name'      : 'This month',
            'isActive'  : false,
            'key'       : 'this_month',
        }, {
            'name'      : 'Custom',
            'isActive'  : false,
            'key'       : 'custom',
        }],
    }
};

export default state;