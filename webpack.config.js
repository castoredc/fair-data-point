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
        options.exclude = ['.htaccess', 'js.components.Layout.Header.js.php', 'static', 'bundles', 'lib'];
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
