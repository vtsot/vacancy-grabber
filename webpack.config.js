/* eslint-disable */

import Encore from '@symfony/webpack-encore';
import fs from 'fs-extra';
import { glob } from 'glob';
import _ from 'lodash';
import Dotenv from 'dotenv';
import path from 'path';
import { VuetifyPlugin } from 'webpack-plugin-vuetify';

// const BundleAnalyzerPlugin = require('webpack-bundle-analyzer').BundleAnalyzerPlugin;
// import CssMinimizerPlugin from 'css-minimizer-webpack-plugin';
// import lightningcss from 'lightningcss';
// import browserslist from 'browserslist';

const config = Dotenv.parse(fs.readFileSync('./.env'));
const dirname = import.meta.dirname;

if (fs.existsSync('.env.local')) {
    let localConfig = Dotenv.parse(fs.readFileSync('./.env.local'));

    for (let k in localConfig) {
        config[k] = localConfig[k];
    }
}

for (let k in config) {
    process.env[k] = config[k];
}

// Manually configure the runtime environment if not already configured yet by the "encore" command.
// It's useful when you use tools that rely on webpack.config.js file.
if (!Encore.isRuntimeEnvironmentConfigured()) {
    Encore.configureRuntimeEnvironment(process.env.NODE_ENV || 'dev');
}

Encore
    .setOutputPath('public/assets/')
    .setPublicPath('/assets')
    // only needed for CDN's or subdirectory deploy
    //.setManifestKeyPrefix('build/')

    .splitEntryChunks()
    .enableSingleRuntimeChunk()

    /*
     * FEATURE CONFIG
     *
     * Enable & configure other features below. For a full
     * list of features, see:
     * https://symfony.com/doc/current/frontend.html#adding-more-features
     */
    .cleanupOutputBeforeBuild()

    // Displays build status system notifications to the user
    // .enableBuildNotifications()

    .enableSourceMaps(!Encore.isProduction())
    // enables hashed filenames (e.g. app.abc123.css)
    .enableVersioning(Encore.isProduction())

    .enableSassLoader((options) => {
        options.sassOptions.quietDeps = true;
    })
    .enableVueLoader(() => {}, { runtimeCompilerBuild: true })
    .enableTypeScriptLoader(function(tsConfig) {
        tsConfig.ignoreDiagnostics = [2322, 2731, 2590];
    })
    .enableIntegrityHashes(Encore.isProduction())

    .copyFiles([
        {
            from: './assets/images',
            // optional target path, relative to the output dir
            to: 'images/[path][name].[hash:8].[ext]',

            // only copy files matching this pattern
            //pattern: /\.(png|jpg|jpeg)$/
        },
        {
            from: './assets/ckeditor-plugins',
            to: 'ckeditor-plugins/[path][name].[ext]',
        }
    ])

    // .configureBabelPresetEnv((config) => {
    //     config.useBuiltIns = 'usage';
    //     config.corejs = 3;
    // })

    .configureJsMinimizerPlugin()
    // .configureTerserPlugin((options) => {
    //     options.terserOptions = {
    //         output: {
    //             comments: false,
    //         },
    //     };
    // })

    // .configureCssMinimizerPlugin((options) => {
    //     options.minify = CssMinimizerPlugin.lightningCssMinify;
    //     options.minimizerOptions = {
    //         // errorRecovery: true,
    //         targets: lightningcss.browserslistToTargets(browserslist('>= 0.25%, last 2 versions, not dead, Safari >= 16.0'))
    //     }
    // })

    .configureMiniCssExtractPlugin(
        (loaderConfig) => {},
        (pluginConfig) => {
            pluginConfig.ignoreOrder = true;
        },
    )

    .configureDefinePlugin((options) => {
        options.__VUE_OPTIONS_API__ = true;
        options.__VUE_PROD_DEVTOOLS__ = false;
    })

    .addPlugin(new VuetifyPlugin({autoImport: true}))
;

    /*
if (Encore.isProduction()) {
    Encore.configureSplitChunks(function (splitChunks) {
        splitChunks.chunks = 'async';
        // splitChunks.maxSize = 300000;
        splitChunks.maxAsyncRequests = 6;
        splitChunks.maxInitialRequests = 4;

        splitChunks.cacheGroups = {
            vue: {
                test: /[\\/]node_modules[\\/](vue\/|@vue\/|vue-i18n|@intlify|core-js|mitt|pinia)([0-9a-z-]*)[\\/]/,
                name: 'vendor',
                chunks: 'all',
                enforce: true,
            },
            vuetify: {
                test: /[\\/]node_modules[\\/](vuetify\/lib\/composables|vuetify\/lib\/util)([0-9a-z-]*)[\\/]/,
                name: 'vendor',
                chunks: 'all',
                enforce: true,
            },
            axios: {
                test: /[\\/]node_modules[\\/](axios|yallist|lru-cache|tslib)([0-9a-z-]*)[\\/]/,
                name: 'vendor',
                chunks: 'all',
                enforce: true,
            },
            yup: {
                test: /[\\/]node_modules[\\/](yup|tiny-case|toposort|property-expr)([0-9a-z-]*)[\\/]/,
                name: 'vendor',
                chunks: 'all',
                enforce: true,
            },
            router: {
                test: /(jsrouting-bundle)/,
                name: 'vendor',
                chunks: 'all',
                enforce: true,
            },
            'vue-plugins': {
                test: /[\\/]node_modules[\\/](vue-|recaptcha)([0-9a-z-]*)[\\/]/,
                name: 'plugins',
                chunks: 'all',
                enforce: true,
            },
            'v-calendar': {
                test: /[\\/]node_modules[\\/](v-calendar|@popperjs)([0-9a-z-]*)[\\/]/,
                name: 'plugins',
                chunks: 'all',
                enforce: true,
            },
        };
    });

    if (process.env.ANALYZE) {
        Encore.addPlugin(new BundleAnalyzerPlugin({
            analyzerMode: 'server',
            analyzerHost: '0.0.0.0',
            analyzerPort: '8888',
        }));
    }
}*/


if (fs.existsSync(dirname + '/assets/js/page')) {
    _.forEach(
        glob.sync(dirname + '/assets/js/page/**/entry.point.ts'),
        (jsFile) => {
            const name = jsFile.replace(new RegExp('^.*/assets/(.+)/entry.point.ts$'), '$1');
            Encore.addEntry(name, jsFile);
        },
    );
}

if (fs.existsSync(dirname + '/assets/scss')) {
    let scssFiles = getFiles(dirname + '/assets/scss', [], 'scss');
    _.forEach(scssFiles, function (fileName) {
        Encore.addStyleEntry('css/' + fileName, ['./assets/scss/' + fileName + '.scss']);
    });
}

// if (fs.existsSync(dirname + '/assets/pdf/scss')) {
//     let scssFiles = getFiles(dirname + '/assets/pdf/scss', [], 'scss');
//     _.forEach(scssFiles, function (fileName) {
//         Encore.addStyleEntry('css/pdf-' + fileName, ['./assets/pdf/scss/' + fileName + '.scss']);
//     });
// }

export default await Encore.getWebpackConfig();
// module.exports = Encore.getWebpackConfig();

///// Functions

function getFiles(dir, exclude, fileType) {
    let files = fs.readdirSync(dir)
        .filter(function (file) {
            return -1 === exclude.indexOf(file) && !fs.statSync(path.join(dir, file)).isDirectory() && !_.startsWith(file, '_');
        });

    return _.map(files, function (filename) {
        let lastDotPosition = filename.lastIndexOf('.');
        let ext;
        if (lastDotPosition !== -1) {
            ext = filename.substr(lastDotPosition + 1);
            if (ext === fileType) {
                return filename.substr(0, lastDotPosition);
            }
        }
    });
}
