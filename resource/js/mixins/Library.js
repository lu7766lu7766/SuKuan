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
				toText: convertToText,
				toDataUrl: convertToDataUrl,
				download,
				getURL,
			}
		},
		lodashFunc() {
			return {
				uniq,
				pick,
				omit,
				isUndefined,
				range,
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

function getDate(value) {
	return moment(value).format('YYYY-MM-DD')
}

function getDateTime(value) {
	return moment(value).format('YYYY-MM-DD HH:mm:ss')
}

const pick = (data, props = []) => {
	return props.reduce((result, prop) => {
		if (!isUndefined(data[prop])) {
			result[prop] = data[prop]
		}
		return result
	}, {})
}
const range = (...params) => {
	let start = 0
	let end = 0
	let step = 1
	switch (params.length) {
		case 1:
			end = params[0]
			break
		case 2:
			start = params[0]
			end = params[1]
			break
		case 3:
			start = params[0]
			end = params[1]
			step = params[2]
			break
		default:
			break
	}
	const res = []
	for (let i = start; i < end; i += step) {
		res.push(i)
	}
	return res
}
const omit = (data, key) => {
	if (typeof key === 'string') {
		const { [key]: _, ...res } = data
		noUse(_)
		return res
	}
	if (key instanceof Array) {
		return key.reduce((result, k) => {
			const { [k]: _, ...res } = result
			noUse(_)
			return res
		}, data)
	}
	return {}
}
const uniq = (array) => array.filter((value, index, self) => self.indexOf(value) === index)
const isUndefined = (value) => typeof value === 'undefined'
