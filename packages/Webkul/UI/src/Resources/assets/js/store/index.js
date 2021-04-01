import Vuex from "vuex";
import state from './state';
import actions from './actions';
import mutations from './mutations';

const store = new Vuex.Store({
    state,
    actions,
    mutations
});

export default store;