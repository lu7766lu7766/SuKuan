import Vue from 'vue'
import { JacPlugin } from 'jactools/index'
import _ from 'lodash'
import moment from 'moment'
import MyPlugin from './plugin'
import './css/my.styl'
import VueRouter from 'vue-router'

Vue.use(JacPlugin, {
	_,
	moment,
})
Vue.use(MyPlugin)

Vue.use(VueRouter)
const router = new VueRouter({
	routes: [],
})

new Vue({
	router,
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
		GroupCallScheduleList: () => import('pages/GroupCallScheduleList'),
		GroupCallScheduleDetail: () => import('pages/GroupCallScheduleDetail'),
		BulletinBoard: () => import('pages/BulletinBoard'),
		ExtensionManage: () => import('pages/ExtensionManage'),
		ExtensionDetail: () => import('pages/ExtensionDetail'),
		AdGroupCallScheduleList: () => import('pages/AdGroupCallScheduleList'),
		AdGroupCallScheduleDetail: () => import('pages/AdGroupCallScheduleDetail'),
	},
}).$mount('#app')
