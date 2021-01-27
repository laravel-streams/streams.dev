const mix = require('laravel-mix');
require('laravel-streams-mix-extension')

mix
    .js('resources/ts/app.ts', 'js')
    .sass('resources/sass/theme.scss', 'css')
    .streams({
        outputPath: 'vendor',
        packages: [
            'streams/api',
            'streams/core',
            'streams/ui',
        ]
    });

mix.options({
    processCssUrls: false,
    postCss       : [
        require('tailwindcss')('./tailwind.config.js'), // for resources/sass/theme.scss
        require('autoprefixer')
    ],
});

mix.version();

// Purge css
if ( mix.inProduction() ) {
    mix.sourceMaps().version();
}

