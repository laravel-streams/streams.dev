const mix = require('laravel-mix');
const tailwindcss = require('tailwindcss');

require('@laravel-streams/mix-extension');

mix
    .ts('resources/js/app.ts', 'js')
    .sass('resources/scss/theme.scss', 'css')
    .streams({
        "packages": [
            "streams/core"
        ]
    })
    .options({
        processCssUrls: false,
        postCss: [tailwindcss('./tailwind.config.js')],
    })
    .webpackConfig(
        function (webpack) {
            return {
                externals: {
                    '@streams/core': ['streams', 'core'],
                    'axios': ['streams', 'core', 'axios'],
                },
                output: {
                    library: ['app'],
                    libraryTarget: 'window',
                }
            };
        }
    ).sourceMaps();


// mix.browserSync({
//     proxy: process.env.APP_URL,
//     files: [
//         'public/js/**/*.js',
//         'public/css/**/*.css',
//         'resources/views/**/*.html',
//         'streams/**/*.json',
//         'streams/**/*.md'
//     ],
//     notify: false
// });
