const htmlStandards = require('reshape-standard');
const cssStandards = require('spike-css-standards');
const jsStandards = require('spike-js-standards');
const pageId = require('spike-page-id');
const env = process.env.SPIKE_ENV;
module.exports = {
	devtool: 'source-map',
	ignore: ['**/layout.html', '**/_*', '**/.*', 'yarn.lock', 'package-lock.json'],
	reshape: htmlStandards({
		locals: (ctx) => {
			return {pageId: pageId(ctx)}
		},
		minify: env === 'production'
	}),
	postcss: cssStandards({
		minify: env === 'production',
		warnForDuplicates: env !== 'production'
	}),
	babel: jsStandards()
};
