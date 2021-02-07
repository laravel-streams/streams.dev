const mix = require('laravel-mix');
require('@laravel-streams/mix-extension');
const {BundleAnalyzerPlugin} = require('webpack-bundle-analyzer');

require('@laravel-streams/mix-extension');

mix
    .js('resources/ts/app.ts', 'js')
    .sass('resources/scss/theme.scss', 'css')
    .streams({
        outputPath: 'vendor',
        packages  : [
            'streams/api',
            'streams/core',
            'streams/ui',
        ]
    })
    .options({
        processCssUrls: false,
        postCss       : [
            require('tailwindcss')('./tailwind.config.js'), // for resources/sass/theme.scss
            require('autoprefixer')
        ],
        terser        : {
            terserOptions: {
                keep_classnames: true,
                keep_fnames    : true,
            }
        },
    })
    .webpackConfig({
        plugins: [
            new BundleAnalyzerPlugin({
                analyzerMode: 'static',
                openAnalyzer: false,
                defaultSizes: 'stat'
            })
        ]
    });

if ( mix.inProduction() ) {
    mix
        .webpackConfig({
            optimization: {
                moduleIds  : 'named',
                chunkIds   : 'named',
                splitChunks: {
                    cacheGroups: {
                        vendor: {
                            name  : 'vendor/streams-vendors',
                            test  : /[\\/]node_modules[\\/]/,
                            chunks: 'initial',
                        },
                    }
                }
            }
        })
        .sourceMaps()
        .version();
}
