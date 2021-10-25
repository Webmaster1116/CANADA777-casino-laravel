const mix = require('laravel-mix');

/*
 |--------------------------------------------------------------------------
 | Mix Asset Management
 |--------------------------------------------------------------------------
 |
 | Mix provides a clean, fluent API for defining some Webpack build steps
 | for your Laravel application. By default, we are compiling the Sass
 | file for the application as well as bundling up all the JS files.
 |
 */

 mix.js('public/frontend/Page/js/script.js', 'public/js/app.js')
//  .js('public/frontend/Page/js/jquery.bxslider.min.js', 'public/js/app.js')
//  .js('public/frontend/Page/js/websiting.main.js', 'public/js/app.js')
//  .js('public/popup/content/plugins/halfdata-green-popups/js/lepopup.js', 'public/js/app.js')
 .js('public/support/js/main.js', 'public/js/app.js')
 .postCss('public/frontend/Page/css/jquery.steps.css', 'public/css/app.css')
 .postCss('public/frontend/Page/css/style.css', 'public/css/app.css');
