const toggleSidebarFilter = ({commit}) => {
    commit('TOGGLE_SIDEBAR_FILTER');
};

const updateFilterValues = ({commit}, payload) => {
    commit('UPDATE_FILTER_VALUES', payload);
};

export default {
    toggleSidebarFilter,
    updateFilterValues
};