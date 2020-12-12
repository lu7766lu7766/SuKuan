import Paginate from '../components/Paginate'

export default {
	components: {
		Paginate,
	},
	data: () => ({
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
	},
}
