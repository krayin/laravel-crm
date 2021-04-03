const TOGGLE_SIDEBAR_FILTER = (state) => {
    state.sidebarFilter = ! state.sidebarFilter;
};

const UPDATE_FILTER_VALUES = (state, payload) => {
    for (const filterKey in state.filterData) {
        if (filterKey == payload.key) {
            state.filterData[filterKey].values = payload.values;
        }
    }
};

export default {
    TOGGLE_SIDEBAR_FILTER,
    UPDATE_FILTER_VALUES
};