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
    'resources/css/app.css',
    'resources/css/c3.css',
], 'public_html/css/front.css')
    .styles([
    'resources/css/handwriting.css',
], 'public_html/css/handwriting.css');
mix.scripts([
    'resources/js/utils.js',
], 'public_html/js/front.js')
    .scripts([
        'resources/js/c3.min.js',
        'resources/js/d3.v5.min.js',
        'resources/js/mapStyles.js',
        'resources/js/mapLogic.js',
    ], 'public_html/js/map.js')
    .scripts([
        'resources/js/handwriting.js',
    ], 'public_html/js/handwriting.js');
