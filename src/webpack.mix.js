const mix = require('laravel-mix');
mix.setPublicPath(path.normalize('public_html'));

/*
 |--------------------------------------------------------------------------
 | Mix Asset Management
 |--------------------------------------------------------------------------
 |
 | Mix provides a clean, fluent API for defining some Webpack build steps
 | for your Laravel applications. By default, we are compiling the CSS
 | file for the application as well as bundling up all the JS files.
 |
 */

mix.styles([
    'resources/css/bootstrap.min.css',
    'resources/css/app.css',
    'resources/css/c3.css',
], 'public_html/css/front.css')
    .styles([
        'resources/css/handwriting.css',
    ], 'public_html/css/handwriting.css');
mix.scripts([
    'resources/js/jquery.min.js',
    'resources/js/popper.js',
    'resources/js/jquery-ui.min.js',
    'resources/js/bootstrap.min.js',
    'resources/js/utils.js',
    'resources/js/locale.js',
], 'public_html/js/front.js')
    .scripts([
        'resources/js/dark-mode-switch.min.js',
    ], 'public_html/js/front-defer.js')
    .scripts([
        'resources/js/c3.min.js',
        'resources/js/d3.v5.min.js',
    ], 'public_html/js/charts.js')
    .scripts([
        'resources/js/mapStyles.js',
        'resources/js/mapLogic.js',
    ], 'public_html/js/map.js')
    .scripts([
        'resources/js/handwriting.js',
    ], 'public_html/js/handwriting.js')
    .scripts([
        'resources/js/raspberryPi.js',
    ], 'public_html/js/raspberryPi.js');
