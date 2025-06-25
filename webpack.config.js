const Encore = require('@symfony/webpack-encore');
const TsconfigPathsPlugin = require('tsconfig-paths-webpack-plugin');

Encore
// The project directory where compiled assets will be stored
    .setOutputPath('public/build//')

    // The public path used by the web server to access the previous directory
    .setPublicPath('/build/')

    .cleanupOutputBeforeBuild(['public/build/'], (options) => {
        options.verbose = true;
        options.root = __dirname;
        options.exclude = ['.htaccess', 'index.php', 'static', 'bundles', 'lib'];
    })

    // Render all final CSS and JS files with source maps to help debugging
    .enableSourceMaps(!Encore.isProduction())

    // Main scripts and styles definition
    .addEntry('app', './src/js/index.tsx')

    // Use Sass/SCSS files (node-sass, sass-loader)
    // .enableSassLoader()

    .enableTypeScriptLoader()

    .addLoader({
        test: /\.svg$/,
        loader: 'svg-inline-loader'
    })

    .enableSingleRuntimeChunk()

    // Enable notifications (webpack-notifier)
    .enableBuildNotifications();

const config = Encore.getWebpackConfig();

config.module.rules[0].exclude = /node_modules\/(?!(autotrack|dom-utils))/;

config.resolve.plugins = [new TsconfigPathsPlugin({
    extensions: [".js", ".jsx", ".ts", ".tsx"]
})];

module.exports = config;
