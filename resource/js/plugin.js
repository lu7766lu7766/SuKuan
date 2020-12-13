import VueBus from 'vue-bus'
import VueSweetalert2 from 'vue-sweetalert2'

export default {
	install: (Vue) => {
		Vue.use(VueBus)
		Vue.use(VueSweetalert2)
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
