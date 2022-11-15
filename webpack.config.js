const path = require('path');
const WooCommerceDependencyExtractionWebpackPlugin = require('@woocommerce/dependency-extraction-webpack-plugin');

module.exports = {
	mode: "production",

	entry: {
		"create-signed-url": "./assets/es6/create-signed-url.js",
		"block": "./assets/es6/block.js",
	},
	output: {
		filename: "[name].js",
		path: path.resolve(__dirname, "./assets/js")
	},
	module: {
		rules: [
			{
				test: /\.js$/,
				exclude: /(node_modules|bower_components)/,
				use: {
					loader: 'babel-loader',
					options: {
						presets: ['@babel/preset-env', '@wordpress/babel-preset-default'],
						cacheDirectory: true
					}
				}
			},
		]
	},
	plugins: [
		new WooCommerceDependencyExtractionWebpackPlugin(),
	],
};
