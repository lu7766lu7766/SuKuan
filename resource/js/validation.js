import { extend, localize } from 'vee-validate'
import { required, min, max, min_value, max_value, confirmed, required_if, numeric } from 'vee-validate/dist/rules'
import locale from 'vee-validate/dist/locale/zh_TW'

extend('required', {
  ...required
})
extend('numeric', {
  ...numeric
})
extend('min', {
  ...min
})
extend('max', {
  ...max
})
extend('min_value', {
  ...min_value
})
extend('max_value', {
  ...max_value
})
extend('confirmed', {
  ...confirmed
})
extend('url', {
  validate: value => /https?:\/\/.+\..+\..+/.test(value)
})
extend('email', {
  validate: value => /^\w+((-\w+)|(\.\w+))*\@[A-Za-z0-9]+((\.|-)[A-Za-z0-9]+)*\.[A-Za-z]+$/.test(value)
})
extend('phone', {
  validate: value => /^\d{11}$/.test(value)
})
extend('difference', {
  params: ['target'],
  validate: (value, { target }) => value !== target
})
extend('required_if', {
  ...required_if
})
extend('image', {
  validate: files => [...files].every(file => /\.(jpg|svg|jpeg|png|bmp|gif)$/i.test(file.name))
})

extend('decimal', {
  params: ['len'],
  validate: (value, { len }) => {
    value = '' + value
    const isNumber = !isNaN(parseFloat(value)) && isFinite(value)
    if (!isNumber) { return false }
    const hasPoint = value.includes('.')
    const afterPoint = value.split('.')[1]
    return !afterPoint ? (!hasPoint) : afterPoint.length <= len
  }
})


locale.messages = Object.assign(locale.messages, {
  required: () => '必填欄位',
  max: (filed, value) => `字數需小於${value.length}字元`,
  min: (filed, value) => `字數需大於${value.length}字元`,
  min_value: (filed, value) => `數字需大於${value.min}`,
  max_value: (filed, value) => `數字需小於${value.max}`,
  confirmed: () => '需与指定欄位相同',
  url: () => '非合法網址',
  email: () => '非合法Email',
  phone: () => '非合法手機號碼',
  difference: () => '需與指定欄位相異',
  required_if: () => '必填欄位',
  numeric: () => '欄位需為數字',
  decimal: (filed, value) => `必須是數字，且能夠保留${value.len}位小數`,
  image: () => '图片格式不符'
})

localize('tw', locale)