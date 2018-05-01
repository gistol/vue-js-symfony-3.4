var Encore = require('@symfony/webpack-encore');

Encore
   .setOutputPath('web/build/')
   .setPublicPath('/build')
   .cleanupOutputBeforeBuild()
   .enableSourceMaps(!Encore.isProduction())
   .addEntry('js/app', './assets/js/app.js')
   // .addStyleEntry('css/app', './assets/css/app.css')
   // .enableSassLoader()
   // .autoProvidejQuery()

   // Enable Vue loader
   .enableVueLoader()
;

module.exports = Encore.getWebpackConfig();