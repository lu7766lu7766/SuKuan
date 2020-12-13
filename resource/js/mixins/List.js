import Paginate from '../components/Paginate'
import DataTable from '../components/DataTable'

export default {
	components: {
		Paginate,
		DataTable,
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
