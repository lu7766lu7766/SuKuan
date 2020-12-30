export default {
	data: () => ({
		options: {
			empSelect: [],
			subEmpSelect: [],
			choicer: {},
		},
	}),
	methods: {
		async getEmpSelect() {
			const res = await $.callApi.post('oldApi/empSelect')
			this.options.empSelect = res.data
		},
		async getSubEmpSelect() {
			const res = await $.callApi.post('oldApi/subEmpSelect')
			this.options.subEmpSelect = res.data
		},
		async getChoicer() {
			const res = await $.callApi.post('oldApi/choicer')
			this.options.choicer = res.data
		},
	},
}
