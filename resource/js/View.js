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
		UserDetail: () => import('pages/UserDetail'),
		CommunicationSearch: () => import('pages/CommunicationSearch'),
		UserRoute: () => import('pages/UserRoute'),
		ManualUserRoute: () => import('pages/ManualUserRoute'),
		PointHistory: () => import('pages/PointHistory'),
		CallStatus: () => import('pages/CallStatus'),
		CallStatistics: () => import('pages/CallStatistics'),
	},
}).$mount('#app')
