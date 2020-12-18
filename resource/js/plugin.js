import VueBus from 'vue-bus'
import VueSweetalert2 from 'vue-sweetalert2'
import { ValidationObserver } from 'vee-validate'
import './validation'

export default {
	install: (Vue) => {
		Vue.use(VueBus)
		Vue.use(VueSweetalert2)
		Vue.prototype.$upload = async function(options) {
			return new Promise((resolve) => {
				const a = document.createElement('input')
				a.type = 'file'
				a.onchange = function(e) {
					e.path[0].files[0] && resolve(e.path[0].files[0])
				}
				Object.assign(a, options)
				a.click()
			})
		}
		Vue.prototype.$multiUpload = async function(options) {
			options = { ...options, multiple: 'multiple' }
			return new Promise((resolve) => {
				const a = document.createElement('input')
				a.type = 'file'
				a.onchange = function(e) {
					e.path[0].files && resolve(e.path[0].files)
				}
				Object.assign(a, options)
				a.click()
			})
		}
		Vue.component('Validation', ValidationObserver)
		Vue.component('Validate', require('@/Validate').default)
		Vue.prototype.$confirm = function(title = '你確定要刪除嗎？') {
			return new Promise((resolve) => {
				this.$swal
					.fire({
						title,
						icon: 'warning',
						showCancelButton: true,
						confirmButtonColor: '#3085d6',
						cancelButtonColor: '#d33',
						confirmButtonText: '確定',
						cancelButtonText: '取消',
						reverseButtons: true,
					})
					.then((result) => {
						if (result.isConfirmed) {
							resolve()
						}
					})
			})
		}
	},
}
