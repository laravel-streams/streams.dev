const mix = require('laravel-mix');
const tailwindcss = require('tailwindcss');
require('laravel-streams-mix-extension')


mix
    .js('resources/js/app.js', 'js')
    .streams({
        tailwind: false,
        packages: [
            'streams/core',
            'streams/ui',
        ]
    });
// const {merge} =  require('lodash')
// let config  = merge(
//     require('./tailwind.config'),
//     require('./vendor/streams/ui/tailwind.config')
// )
mix.options({
    processCssUrls: false,
    postCss       : [tailwindcss('./tailwind.config.js')],
});

mix.version();

// Purge css
if ( mix.inProduction() ) {
    mix.sourceMaps().version();
}

