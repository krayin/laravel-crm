const UPDATE_FILTER_VALUES = (state, payload) => {
    for (const filterKey in state.filterData) {
        if (filterKey == payload.key) {
            state.filterData[filterKey].values = payload.values;
        }
    }
};

const SELECT_ALL_ROWS = (state, payload) => {
    state.selectedTableRows = [];
    state.allSelected = payload != 'undefined' ? payload : ! state.allSelected;

    state.tableData.records.data.forEach(row => {
        if (state.allSelected) {
            state.selectedTableRows.push(row.id);
        }
    });
};

const SELECT_TABLE_ROW = (state, payload) => {
    var isExisting = false;

    state.selectedTableRows.forEach((rowId, index) => {
        if (rowId == payload) {
            isExisting = true;
            state.selectedTableRows.splice(index, 1);
        }
    });

    if (! isExisting) {
        state.selectedTableRows.push(payload);
    }

    state.allSelected = (state.tableData.records.data.length == state.selectedTableRows.length);
};

export default {
    UPDATE_FILTER_VALUES,
    SELECT_ALL_ROWS,
    SELECT_TABLE_ROW,
};