var state = {
    filters: {},
    tableData: {
        records: {
            data: {}
        },

        columns: [],

        actions: [],

        tabFilters: [],

        massactions: [],

        paginationData: {
            has_pages: false
        }
    },
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
    customTabFilter: false,
};

export default state;