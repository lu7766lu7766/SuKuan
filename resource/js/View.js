import Vue from 'vue'
import { JacPlugin } from 'jactools/index'
import _ from 'lodash'
import moment from 'moment'
import MyPlugin from './plugin'
import './css/my.styl'

Vue.use(JacPlugin, {
	_,
	moment,
})
Vue.use(MyPlugin)

new Vue({
	components: {
		// Test: () => import('pages/Test'),
		RateManage: () => import('pages/RateManage'),
		TaskRanking: () => import('pages/TaskRanking'),
		UserList: () => import('pages/UserList'),
	},
}).$mount('#app')
