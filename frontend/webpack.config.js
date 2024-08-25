const Encore = require('@symfony/webpack-encore');
const webpack = require('webpack');

const environment = process.env.NODE_ENV || 'dev';
Encore.configureRuntimeEnvironment(environment);


Encore
    .setOutputPath('../public/build/client/')
    .setPublicPath('/build/client')
    .addEntry('client', './src/client/index.tsx')
    .enableReactPreset()
    .enableTypeScriptLoader()
    .enableSassLoader()
    .enableSourceMaps(!Encore.isProduction())
    .enableVersioning(Encore.isProduction())
    .configureFilenames({
        js: '[name].js',
        css: '[name].css',
    })
    .enableSingleRuntimeChunk()
    .addPlugin(new webpack.DefinePlugin({
        'process.env.NODE_ENV': JSON.stringify(environment),
    }));

const clientConfig = Encore.getWebpackConfig();
clientConfig.name = 'client';

Encore.reset();
Encore.configureRuntimeEnvironment(environment);


Encore
    .setOutputPath('../public/build/admin/')
    .setPublicPath('/build/admin')
    .addEntry('admin', './src/admin/index.tsx')
    .enableReactPreset()
    .enableTypeScriptLoader()
    .enableSassLoader()
    .enableSourceMaps(!Encore.isProduction())
    .enableVersioning(Encore.isProduction())
    .configureFilenames({
        js: '[name].js',
        css: '[name].css',
    })
    .enableSingleRuntimeChunk()
    .addPlugin(new webpack.DefinePlugin({
        'process.env.NODE_ENV': JSON.stringify(environment),
    }));

const adminConfig = Encore.getWebpackConfig();
adminConfig.name = 'admin';

module.exports = [clientConfig, adminConfig];
