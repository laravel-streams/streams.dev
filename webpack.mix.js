const mix = require('laravel-mix');
require('laravel-streams-mix-extension')

mix
    .js('resources/js/app.js', 'js')
    .streams({
        outputPath: 'vendor',
        packages: [
            'streams/core',
            'streams/ui',
        ]
    });

mix.options({
    processCssUrls: false,
    postCss       : [require('autoprefixer')],
});

mix.version();

// Purge css
if ( mix.inProduction() ) {
    mix.sourceMaps().version();
}

