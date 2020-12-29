import Paginate from '../components/Paginate'
import DataTable from '../components/DataTable'
import Switcher from '../components/Switcher.vue'

export default {
	components: {
		Paginate,
		DataTable,
		Switcher,
	},
	data: () => ({
		editData: {},
		datas: [],
	}),
	computed: {
		isAllChecked: {
			get() {
				return this.datas.every((x) => x.checked)
			},
			set(val) {
				this.datas.forEach((x) => (x.checked = val))
			},
		},
		requestBody() {
			return Object.assign({ ...this.editData, ...this.paginate }, this.sort ? { ...this.sort } : {})
		},
	},
}
