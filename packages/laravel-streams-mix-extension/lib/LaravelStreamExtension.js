"use strict";
Object.defineProperty(exports, "__esModule", { value: true });
exports.LaravelStreamExtension = void 0;
const path_1 = require("path");
const isDev = process.env.NODE_ENV === 'development';
class Prefix {
    static val(val) { return this.pre(val); }
    ;
    static has(val) { return val.toString().startsWith(this.value); }
    ;
    static clear(val) { return val.toString().slice(this.value.length); }
    static toString() { return this.value; }
    static pre(val) { return this.value + val; }
} //@formatter:on
Prefix.value = '';
const config = {
    devtool: isDev ? 'hidden-source-map' : false,
    resolve: {
        symlinks: true,
        extensions: ['.js', '.vue', '.json', '.web.ts', '.ts', '.web.tsx', '.tsx', '.styl', '.less', '.scss', '.stylus', '.css', '.mjs', '.web.js', '.json', '.web.jsx', '.jsx'],
        mainFields: ['module', 'browser', 'main'],
        mainFiles: ['index', 'index.ts', 'index.tsx'],
    },
    module: {},
    plugins: [
        require('@tailwindcss/ui'),
    ],
    externals: {
        '@streams/core': ['streams', 'core'],
    },
    output: {},
};
class LaravelStreamExtension {
    babelConfig() {
        return {
            babelrc: false,
            // configFile    : false,
            // cacheDirectory: false, //wp.isDev,
            compact: !isDev,
            sourceMaps: isDev,
            comments: isDev,
            presets: [['@babel/preset-env']],
            plugins: [
                ['import', {
                        libraryName: 'lodash',
                        libraryDirectory: '',
                        camel2DashComponentName: false,
                    }],
                '@babel/plugin-syntax-dynamic-import',
            ],
        };
    }
    boot() {
        return undefined;
    }
    dependencies() {
        return undefined;
    }
    mix() {
        return {};
    }
    name() {
        return 'streamify';
    }
    register(...args) {
        return;
    }
    webpackConfig(config) {
        config.entry = {
            [Prefix + 'core']: path_1.resolve('packages/streams/core/lib/index.ts'),
            [Prefix + 'ui']: path_1.resolve('packages/streams/ui/lib/index.ts'),
        };
        config.output = Object.assign(Object.assign({}, config.output), { path: path_1.resolve('public'), 
            // filename                             : /** @param  {webpack.ChunkData} chunk */(chunk) => {
            //     let c = chunk.chunk;
            //
            //     if ( Prefix.has(c.name) ) {
            //         c.name = Prefix.clear(c.name);
            //     }
            //     if ( Prefix.has(c.runtime) ) {
            //         return `/vendor/streams/${Prefix.clear(c.runtime)}/js/[name].js`;
            //     }
            //     // for (const group of c._groups.values()) {
            //     //     let entry = c.runtime.substr('streams:'.length);
            //     //     group.name;
            //     //     continue;
            //     // }
            //     return '[name].js';
            //
            // },
            // chunkFilename                        : /** @param  {webpack.ChunkData} chunk */(chunk) => {
            //     let c = chunk.chunk;
            //     if ( Prefix.has(c.runtime) ) {
            //         c.runtime = Prefix.clear(c.runtime);
            //         return `/vendor/streams/${c.runtime}/js/chunk.[name].js`;
            //     }
            //     return 'chunk.[name].js';
            // },
            library: ['streams'], publicPath: 'public/streams/', libraryTarget: 'window', devtoolFallbackModuleFilenameTemplate: 'webpack:///[resource-path]?[hash]', devtoolModuleFilenameTemplate: info => {
                var $filename = 'sources://' + info.resourcePath;
                $filename = 'webpack:///' + info.resourcePath; // +'?' + info.hash;
                if (info.resourcePath.match(/\.vue$/) && !info.allLoaders.match(/type=script/) && !info.query.match(/type=script/)) {
                    $filename = 'webpack-generated:///' + info.resourcePath; // + '?' + info.hash;
                }
                return $filename;
            } });
    }
    webpackEntry(entry) {
        return;
    }
    webpackPlugins() {
        return [];
    }
    webpackRules() {
        return [
            {
                test: /\.tsx?$/,
                exclude: /node_modules\//,
                use: [{
                        loader: 'babel-loader',
                    }, {
                        loader: 'ts-loader',
                        options: {
                            appendTsxSuffixTo: [/.vue$/],
                            configFile: 'tsconfig.json',
                            transpileOnly: true,
                            // experimentalWatchApi: true,
                            // happyPackMode       : true,
                            compilerOptions: {
                                target: 'es5',
                                module: 'esnext',
                                importHelpers: true,
                                sourceMap: isDev,
                                removeComments: !isDev,
                            },
                        },
                    }],
            },
        ];
    }
}
exports.LaravelStreamExtension = LaravelStreamExtension;
//# sourceMappingURL=data:application/json;base64,eyJ2ZXJzaW9uIjozLCJmaWxlIjoiTGFyYXZlbFN0cmVhbUV4dGVuc2lvbi5qcyIsInNvdXJjZVJvb3QiOiIiLCJzb3VyY2VzIjpbIi4uL3NyYy9MYXJhdmVsU3RyZWFtRXh0ZW5zaW9uLnRzIl0sIm5hbWVzIjpbXSwibWFwcGluZ3MiOiI7OztBQUtBLCtCQUE2RDtBQUU3RCxNQUFNLEtBQUssR0FBRyxPQUFPLENBQUMsR0FBRyxDQUFDLFFBQVEsS0FBSyxhQUFhLENBQUM7QUFHckQsTUFBTSxNQUFNO0lBRVIsTUFBTSxDQUFDLEdBQUcsQ0FBQyxHQUFHLElBQUUsT0FBTyxJQUFJLENBQUMsR0FBRyxDQUFDLEdBQUcsQ0FBQyxDQUFBLENBQUEsQ0FBQztJQUFBLENBQUM7SUFDdEMsTUFBTSxDQUFDLEdBQUcsQ0FBQyxHQUFHLElBQUcsT0FBTyxHQUFHLENBQUMsUUFBUSxFQUFFLENBQUMsVUFBVSxDQUFDLElBQUksQ0FBQyxLQUFLLENBQUMsQ0FBQyxDQUFBLENBQUM7SUFBQSxDQUFDO0lBQ2hFLE1BQU0sQ0FBQyxLQUFLLENBQUMsR0FBRyxJQUFHLE9BQU8sR0FBRyxDQUFDLFFBQVEsRUFBRSxDQUFDLEtBQUssQ0FBQyxJQUFJLENBQUMsS0FBSyxDQUFDLE1BQU0sQ0FBQyxDQUFDLENBQUEsQ0FBQztJQUNuRSxNQUFNLENBQUMsUUFBUSxLQUFJLE9BQU8sSUFBSSxDQUFDLEtBQUssQ0FBQyxDQUFBLENBQUM7SUFDdEMsTUFBTSxDQUFDLEdBQUcsQ0FBQyxHQUFHLElBQUcsT0FBTyxJQUFJLENBQUMsS0FBSyxHQUFHLEdBQUcsQ0FBQyxDQUFBLENBQUM7RUFDN0MsZUFBZTtBQU5MLFlBQUssR0FBRyxFQUFFLENBQUM7QUFRdEIsTUFBTSxNQUFNLEdBQTBCO0lBQ2xDLE9BQU8sRUFBSSxLQUFLLENBQUMsQ0FBQyxDQUFDLG1CQUFtQixDQUFDLENBQUMsQ0FBQyxLQUFLO0lBQzlDLE9BQU8sRUFBSTtRQUNQLFFBQVEsRUFBSSxJQUFJO1FBQ2hCLFVBQVUsRUFBRSxDQUFFLEtBQUssRUFBRSxNQUFNLEVB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