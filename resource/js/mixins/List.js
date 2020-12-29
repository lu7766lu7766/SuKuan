import PaginateMixins from './Paginate'

export default {
	mixins: [PaginateMixins],
	components: {
		Paginate: () => import('../components/Paginate'),
		Switcher: () => import('../components/Switcher'),
		DataTable: () => import('../components/DataTable'),
	},
	data: () => ({
		editData: {},
		datas: [],
	}),
	methods: {
		getList() {},
		getTotal() {},
		methods: {
			changePage(page) {
				this.paginate.page = page
				this.getList()
			},
			doSearch() {
				this.changePage(1)
				this.getTotal()
			},
		},
	},
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
