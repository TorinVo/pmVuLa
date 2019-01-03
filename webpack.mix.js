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

mix.js([
      'resources/js/app.js',
      'node_modules/admin-lte/plugins/daterangepicker/daterangepicker.js',
      'public/js/paste_image_reader.js',
   ], 'public/js')
   .sass('resources/sass/app.scss', 'public/css')
   .styles([
      'node_modules/admin-lte/plugins/daterangepicker/daterangepicker-bs3.css',
   ], 'public/css/style.css')
