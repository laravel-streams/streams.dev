const mix = require('laravel-mix');
require('laravel-streams-mix-extension');
const {BundleAnalyzerPlugin} = require('webpack-bundle-analyzer');

mix
    .js('resources/ts/app.ts', 'js')
    .sass('resources/sass/theme.scss', 'css')
    .streams({
        outputPath: 'vendor',
        packages  : [
            'streams/api',
            'streams/core',
            'streams/ui',
        ]
    });
mix.webpackConfig({
    // optimization: {
        // moduleIds  : 'named',
        // chunkIds   : 'named',
        // splitChunks: {
        //     cacheGroups: {
        //         vendor: {
        //             name  : 'vendor/streams-vendors',
        //             test  : /[\\/]node_modules[\\/]/,
        //             chunks: 'initial',
        //         },
        //     }
        // }
    // },
    plugins     : [
        new BundleAnalyzerPlugin({
            analyzerMode: 'static',
            openAnalyzer: false,
            defaultSizes: 'stat'
        })
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
const TerserPlugin = require('terser-webpack-plugin');
// Purge css
if ( mix.inProduction() ) {
    mix.webpackConfig({
        optimization: {
            minimize : true,
            minimizer: [
                new TerserPlugin({
                    terserOptions: {

                        keep_classnames: /.*ServiceProvider.*/,
                        keep_fnames    : /.*ServiceProvider.*/,
                    }
                })
            ]
        }
    });
    mix.sourceMaps().version();
}

