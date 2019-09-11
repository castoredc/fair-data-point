// const INLINE_THRESHOLD = 1000;
//
// module.exports = {
//     module: {
//         rules: [
//             {
//                 test: /\.(js|jsx)$/,
//                 exclude: /node_modules/,
//                 use: {
//                     loader: "babel-loader"
//                 }
//             },
//             {
//                 test: /\.(css|scss)$/,
//                 //exclude: /node_modules/,
//                 use: [
//                     {
//                         loader: 'style-loader',
//                     },
//                     {
//                         loader: 'css-loader',
//                         options: {
//                             sourceMap: true,
//                         },
//                     },
//                     {
//                         loader: 'sass-loader',
//                         options: {
//                             sourceMap: true,
//                         },
//                     },
//                 ],
//             },
//             {
//                 test: /\.(png|jp(e*)g)$/,
//                 use: [
//                     {
//                         loader: 'url-loader',
//                         options: {
//                             limit: INLINE_THRESHOLD,
//                         },
//                     },
//                 ],
//             },
//             {
//                 test: /\.(ttf|woff2?)$/,
//                 use: [
//                     {
//                         loader: 'url-loader',
//                         options: {
//                             limit: INLINE_THRESHOLD,
//                             name: '../fonts/[name].[ext]',
//                         },
//                     },
//                 ],
//             },
//             {
//                 test: /\.svg$/,
//                 use: [
//                     {
//                         loader: 'babel-loader',
//                     },
//                     {
//                         loader: 'react-svg-loader',
//                         options: {
//                             jsx: true, // true outputs JSX tags
//                         },
//                     },
//                 ],
//             },
//         ]
//     },
//     entry: {
//         components: './src/js/index.js'
//     },
//     output: {
//         path: __dirname + '/public/build/',
//         filename: '[name].js'
//     },
//     mode: 'development'
// };

const Encore = require('@symfony/webpack-encore');
const ManifestPlugin = require('webpack-manifest-plugin');
const path = require('path');

Encore
// The project directory where compiled assets will be stored
    .setOutputPath('public/build//')

    // The public path used by the web server to access the previous directory
    .setPublicPath('/build/')

    .cleanupOutputBeforeBuild(['public/build/'], (options) => {
        options.verbose = true;
        options.root = __dirname;
        options.exclude = ['.htaccess', 'index.php', 'static', 'bundles'];
    })

    // Render all final CSS and JS files with source maps to help debugging
    .enableSourceMaps(!Encore.isProduction())

    // Main scripts and styles definition
    .addEntry('app', './src/js/index.js')

    // Use Sass/SCSS files (node-sass, sass-loader)
    .enableSassLoader()

    .addLoader({
        test: /\.svg$/,
        loader: 'svg-inline-loader'
    })

    .addPlugin(new ManifestPlugin({
        fileName: path.resolve(__dirname, 'public/build/manifest.json')
    }))

    .enableSingleRuntimeChunk()

    // Enable notifications (webpack-notifier)
    .enableBuildNotifications();

const config = Encore.getWebpackConfig();

config.module.rules[0].exclude = /node_modules\/(?!(autotrack|dom-utils))/;

module.exports = config;
