const mix = require('laravel-mix')

const rm = require('rimraf')
rm(path.join(__dirname, 'public/vue/vendor'), (err) => {})

const env = require('./env.json')
const base_folder = env ? (env.BASE_FOLDER ? env.BASE_FOLDER : '/') : '/'
mix.webpackConfig({
	resolve: {
		extensions: ['.js', '.vue', '.json'],
		alias: {
			// 'vue$': 'vue/dist/vue.esm.js',
			'~': __dirname + '/node_modules',
			'@': __dirname + '/resource/js/components',
			pages: __dirname + '/resource/js/pages',
			mixins: __dirname + '/resource/js/mixins',
			config: __dirname + '/resource/js/config',
			lib: __dirname + '/resource/js/lib',
			constants: __dirname + '/resource/js/constants',
			store: __dirname + '/resource/js/store',
		},
	},
	output: {
		path: path.resolve(base_folder + 'public/vue'),
		publicPath: base_folder + 'public/vue/',
		chunkFilename: 'vendor/[name].js',
	},
})

mix
	.js('resource/js/View.js', './')
	.autoload({
		// jquery: ['$', 'window.jQuery', 'jQuery'],
		moment: 'moment',
		axios: 'axios',
		_: 'lodash',
	})
	.extract(['vue', 'lodash', 'moment'])
