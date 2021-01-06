// noinspection ES6UnusedImports
import mix                                       from 'laravel-mix';
import { ClassComponent, Component, Dependency } from 'laravel-mix/types/component';
import { TransformOptions }                      from 'babel-core';
import webpack                                   from 'webpack';
import { resolve }                               from 'path';

const isDev = process.env.NODE_ENV === 'development';


class Prefix {//@formatter:off
    static value = '';
    static val(val){return this.pre(val)};
    static has(val) {return val.toString().startsWith(this.value);};
    static clear(val) {return val.toString().slice(this.value.length);}
    static toString() {return this.value;}
    static pre(val) {return this.value + val;}
}//@formatter:on

const config: webpack.Configuration = {
    devtool  : isDev ? 'hidden-source-map' : false,
    resolve  : {
        symlinks  : true,
        extensions: [ '.js', '.vue', '.json', '.web.ts', '.ts', '.web.tsx', '.tsx', '.styl', '.less', '.scss', '.stylus', '.css', '.mjs', '.web.js', '.json', '.web.jsx', '.jsx' ],
        mainFields: [ 'module', 'browser', 'main' ],
        mainFiles : [ 'index', 'index.ts', 'index.tsx' ],
    },
    module   : {},
    plugins  : [
        require('@tailwindcss/ui'),
        // {
        //     apply(compiler) {
        //         compiler.hooks.entryOption.tap('streams',(ctx, entry) => {
        //             let imports = entry['/index']['import'];
        //             entry['/index']['import'] = imports.reverse();
        //         });
        //     }
        // }
    ],
    externals: {
        '@streams/core': [ 'streams', 'core' ],
    },
    output   : {},

};

export class LaravelStreamExtension implements ClassComponent {
    public passive: boolean;

    public babelConfig(): TransformOptions {
        return {
            babelrc   : false,
            // configFile    : false,
            // cacheDirectory: false, //wp.isDev,
            compact   : !isDev,
            sourceMaps: isDev,
            comments  : isDev,
            presets   : [ [ '@babel/preset-env' ] ],
            plugins   : [
                [ 'import', {
                    libraryName            : 'lodash',
                    libraryDirectory       : '',
                    camel2DashComponentName: false,
                } ],
                '@babel/plugin-syntax-dynamic-import',
            ],
        };
    }

    public boot(): void {
        return undefined;
    }

    public dependencies(): Dependency | Dependency[] {
        return undefined;
    }

    public mix(): Record<string, Component> {
        return {};
    }

    public name(): string | string[] {
        return 'streamify';
    }

    public register(...args: any[]): void {

        return;
    }

    public webpackConfig(config: webpack.Configuration): void {
        config.entry  = {
            [ Prefix + 'core' ]: resolve('packages/streams/core/lib/index.ts'),
            [ Prefix + 'ui' ]  : resolve('packages/streams/ui/lib/index.ts'),
        };
        config.output = {
            ...config.output,

            path                                 : resolve('public'),
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
            library                              : [ 'streams' ],
            publicPath                           : 'public/streams/',
            libraryTarget                        : 'window',
            devtoolFallbackModuleFilenameTemplate: 'webpack:///[resource-path]?[hash]',
            devtoolModuleFilenameTemplate        : info => {
                var $filename = 'sources://' + info.resourcePath;
                $filename     = 'webpack:///' + info.resourcePath; // +'?' + info.hash;
                if ( info.resourcePath.match(/\.vue$/) && !info.allLoaders.match(/type=script/) && !info.query.match(/type=script/) ) {
                    $filename = 'webpack-generated:///' + info.resourcePath; // + '?' + info.hash;
                }
                return $filename;
            },
        };
    }

    public webpackEntry(entry: any): void {
        return ;
    }

    public webpackPlugins(): webpack.WebpackPluginInstance[] {
        return [];
    }

    public webpackRules(): webpack.RuleSetRule | webpack.RuleSetRule[] {
        return [
            {
                test   : /\.tsx?$/,
                exclude: /node_modules\//,
                use    : [ {
                    loader: 'babel-loader',
                }, {
                    loader : 'ts-loader',
                    options: {
                        appendTsxSuffixTo: [ /.vue$/ ],
                        configFile       : 'tsconfig.json',
                        transpileOnly    : true,
                        // experimentalWatchApi: true,
                        // happyPackMode       : true,
                        compilerOptions  : {
                            target        : 'es5',
                            module        : 'esnext',
                            importHelpers : true,
                            sourceMap     : isDev,
                            removeComments: !isDev,
                        },
                    },
                } ],
            },
        ];
    }


}


