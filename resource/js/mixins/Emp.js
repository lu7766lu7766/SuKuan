export default {
	data: () => ({
		options: {
			empSelect: [],
		},
	}),
	methods: {
		async getEmpSelect() {
			const res = await $.callApi.post('oldApi/empSelect')
			this.options.empSelect = res.data
		},
	},
}
