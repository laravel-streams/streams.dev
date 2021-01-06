const mix = require('laravel-mix');
const tailwindcss = require('tailwindcss');
require('laravel-streams-mix-extension')


mix
    .js('resources/js/app.js', 'js')
    .sass('resources/sass/theme.scss', 'css')
    .streamify({
        some: 'config',
        can: 'be',
        here: 'foo'
    });

mix.options({
    processCssUrls: false,
    postCss       : [tailwindcss('./tailwind.config.js')],
});

mix.version();

// Purge css
if ( mix.inProduction() ) {
    mix.sourceMaps().version();
}

