import axios from 'axios';
import store from './store';
import VTooltip from 'v-tooltip';

window.axios = axios;
window.EventBus = new Vue();

VTooltip.options.disposeTimeout = 0;

Vue.directive('tooltip', VTooltip.VTooltip)
Vue.directive('debounce', require('./directives/debounce').default);

Vue.prototype.$http = axios;
Vue.config.productionTip = false;

Vue.mixin({
	store
});

Vue.component('flash-wrapper', require('./components/flash-wrapper.vue').default);
Vue.component('flash', require('./components/flash.vue').default);
Vue.component('tabs', require('./components/tabs/tabs').default);
Vue.component('tab', require('./components/tabs/tab').default);
Vue.component('modal', require('./components/modal').default);
Vue.component('accordian', require('./components/accordian').default);
Vue.component('image-upload', require('./components/image/image-upload').default);
Vue.component('image-wrapper', require('./components/image/image-wrapper').default);
Vue.component('image-item', require('./components/image/image-item').default);
Vue.directive('slugify', require('./directives/slugify').default);
Vue.directive('slugify-target', require('./directives/slugify-target').default);
Vue.directive('code', require('./directives/code').default);
Vue.directive('alert', require('./directives/alert').default);
Vue.component('datetime', require('./components/datetime').default);
Vue.component('date', require('./components/date').default);
Vue.component("time-component", require('./components/time').default);
Vue.component('overlay-loader', require('./components/overlay-loader').default);
Vue.component('table-component', require('./components/datagrid/table').default);
Vue.component('filter-component', require('./components/datagrid/filters').default);
Vue.component('pagination-component', require('./components/datagrid/pagination').default);
Vue.component('thead-component', require('./components/datagrid/table-head').default);
Vue.component('tbody-component', require('./components/datagrid/table-body').default);
Vue.component('table-tab', require('./components/datagrid/table-tab').default);
Vue.component('sidebar-filter', require('./components/datagrid/side-filter').default);

Vue.mixin(require('./components/trans'));

Vue.filter('truncate', function (value, limit, trail) {
	if (! value)
        value = '';

	limit = limit ? limit : 20;
	trail = trail ? trail : '...';

	return value.length > limit ? value.substring(0, limit) + trail : value;
});

require('flatpickr/dist/flatpickr.css');

require('@babel/polyfill');