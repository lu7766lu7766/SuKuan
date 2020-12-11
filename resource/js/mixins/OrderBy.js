export default {
	data: () => ({
		sort: {
			key: 'UserID',
			type: 'asc',
		},
	}),
	methods: {
		changeSort(key) {
			if (key == this.sort.key) {
				this.sort.type = this.sort.type == 'asc' ? 'desc' : 'asc'
			} else {
				this.sort.key = key
				this.sort.type = 'asc'
			}
		},
	},
	computed: {
		sortDatas() {
			return _.orderBy(this.datas, this.sort.key, this.sort.type)
		},
	},
}
