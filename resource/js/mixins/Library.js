import moment from 'moment'

export default {
	filters: {
		number,
		getDate,
		getDateTime,
	},
	methods: {
		getRef(refName) {
			const $ref = this.$refs[refName]
			if (isUndefined($ref)) {
				throw new Error(`查無ref: ${refName}物件`)
			}
			return $ref
		},
		getModal(refName = 'modal') {
			const $modal = $(this.getRef(refName))
			return {
				show: () => $modal.modal('show'),
				hide: () => $modal.modal('hide'),
			}
		},
		getForm(refName = 'form') {
			const $form = $(this.getRef(refName))
			return {
				isValid: () => $form.valid(),
				isValidAsync: () => new Promise((resolve) => ($form.valid() ? resolve() : '')),
				getObject: () => $form,
				removeFiles: () => $form.find('input:file').val(''),
			}
		},
	},
	computed: {
		requestFunc() {
			const $notify = this.$notify
			const $confirm = this.$confirm
			return {
				toFormData: convertToFormData,
				parse: loopParse,
				async success(fn) {
					return loopParse(await requestSuccess(fn))
				},
				successWithoutParse: requestSuccess,
				async successNotifyMessage(fn, message) {
					const res = await this.success(fn)
					$notify.success(message)
					return res
				},
				async createSuccess(fn) {
					const res = await this.successNotifyMessage(fn, '新增成功')
					return res
				},
				async updateSuccess(fn) {
					const res = await this.successNotifyMessage(fn, '更新成功')
					return res
				},
				async deleteSuccess(fn) {
					const res = await this.successNotifyMessage(fn, '刪除成功')
					return res
				},
				deleteConfirm() {
					return new Promise((resolve) => $confirm('確定要刪除嗎?').then((isOK) => (isOK ? resolve() : '')))
				},
			}
		},
		fileFunc() {
			return {
				exportCSV,
				exportTxt,
				buildCSVContext,
				toText: convertToText,
				toDataUrl: convertToDataUrl,
				toDatas: convertToDatas,
				download,
				getURL,
			}
		},
		lodashFunc() {
			return {
				isUndefined,
			}
		},
		formater() {
			return {
				number,
				date: getDate,
				dateTime: getDateTime,
			}
		},
	},
}

const noUse = (_) => _

function number(value, fixed = 0) {
	const newValue = Number(value)
	let newFixed
	if (!newValue || newValue + '' == 'NaN') return '0'
	if (!Number(fixed) || Number(fixed) + '' == 'NaN') newFixed = 0
	const float = (newValue + '').split('.')[1]
	const paddingRight = (str, length) => (str.length >= lenght ? str : paddingRight(str + '0', length))

	let suffix = ''
	if (float && newFixed) {
		if (float.length >= newFixed.length) {
			suffix = '.' + float.substr(0, newFixed)
		} else {
			suffix = '.' + paddingRight(float, newFixed)
		}
	}
	return newValue.toFixed(0).replace(/(\d)(?=(?:\d{3})+$)/g, '$1,') + suffix
}

function requestSuccess(fn) {
	return new Promise((resolve) => fn.then((res) => (res.returnCode === 0 ? resolve(res) : '')))
}

function convertToFormData(data, sendString = true) {
	const formData = new FormData()
	Object.keys(data).forEach((key) => {
		switch (typeof data[key]) {
			case 'object':
				if (data[key] instanceof File) {
					formData.append(key, data[key])
				} else if (sendString) {
					formData.append(`${key}`, JSON.stringify(data[key]))
				} else if (data[key] instanceof Array) {
					data[key].forEach((v, i) => {
						// formData.append(`${key}[${i}]`, v);
						formData.append(`${key}`, v)
					})
				} else {
					Object.keys(data[key]).forEach((k) => {
						formData.append(`${key}[${k}]`, data[key][k])
					})
				}
				break
			default:
				formData.append(key, data[key])
				break
		}
	})
	return formData
}

function loopParse(data) {
	if (data instanceof Array) {
		return data.map((x) => loopParse(x))
	}
	if (data instanceof Object) {
		return Object.keys(data).reduce((result, key) => {
			result[key] = loopParse(data[key])
			return result
		}, {})
	}
	if (typeof data === 'string') {
		return data.startsWith('{') || data.startsWith('[') ? loopParse(JSON.parse(data)) : data
	}
	return data
}

function getURL(target) {
	return URL.createObjectURL(target)
}

function download(href, fileName) {
	const link = document.createElement('a')
	link.href = href
	link.download = fileName
	link.click()
}

const exportTxt = (content, fileName) => {
	const blob = new Blob([content], {
		type: 'application/octet-stream;charset=utf-8',
	})
	download(getURL(blob), fileName)
}

const exportCSV = (content, fileName) => {
	const blob = new Blob(['\ufeff' + content], {
		type: 'application/octet-stream;charset=utf-8',
	})
	download(getURL(blob), fileName)
}

const convertToText = (file) => {
	return new Promise((resolve) => {
		const reader = new FileReader()
		reader.readAsText(file)
		reader.onload = (event) => {
			resolve(event.target.result)
		}
	})
}

const convertToDataUrl = (file) => {
	return new Promise((resolve) => {
		const reader = new FileReader()
		reader.readAsDataURL(file)
		reader.onload = (event) => {
			resolve(event.target.result)
		}
	})
}

const buildCSVContext = (datas, keymap) => {
	const keys = _.map(keymap, 'key')
	return [_.map(keymap, 'name').join(',')].concat(datas.map((x) => keys.map((key) => x[key]).join(','))).join('\r\n')
}

const convertToDatas = (text, keymap, skipFirstLine = true, filterEmptyProp = true) => {
	return text
		.split('\r\n')
		.slice(skipFirstLine ? 1 : 0)
		.filter((x) => x)
		.map((line) => {
			console.log(line.split(',').map((x) => x.trim()))
			const res = _.mapKeys(
				line.split(',').map((x) => x.trim()),
				(v, k) => keymap[k]
			)
			return filterEmptyProp ? _.pickBy(res) : res
		})
}

function getDate(value) {
	return moment(value).format('YYYY-MM-DD')
}

function getDateTime(value) {
	return moment(value).format('YYYY-MM-DD HH:mm:ss')
}

const isUndefined = (value) => typeof value === 'undefined'
