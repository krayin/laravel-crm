const UPDATE_FILTER_VALUES = (state, payload) => {
    var key = payload?.key || null;

    if (key) {
        for (const filterKey in state.tableData.columns) {
            if (filterKey == key) {
                key = state.tableData.columns[filterKey].index;
                state.tableData.columns[filterKey].values = payload.values;
            }
        }
    }

    EventBus.$emit('updateFilter', {
        key,
        value: payload?.values?.toString(),
        cond: payload?.condition || 'in',
    });
};

const SELECT_ALL_ROWS = (state, payload) => {
    if (! (payload && state.selectedTableRows.length > 0)) {
        state.selectedTableRows = [];

        state.allSelected = payload || ! state.allSelected;

        state.tableData.records.data.forEach(row => {
            if (state.allSelected) {
                state.selectedTableRows.push(row.id);
            }
        });
    } else {
        state.selectedTableRows = [];
        state.allSelected = false;
    }
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
