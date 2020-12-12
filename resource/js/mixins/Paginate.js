export default {
	data: () => ({
		paginate: {
			page: 1,
			per_page: 100,
			total: 0,
		},
	}),
	computed: {
		startIndex() {
			return (this.paginate.page - 1) * this.paginate.per_page + 1
		},
	},
}
