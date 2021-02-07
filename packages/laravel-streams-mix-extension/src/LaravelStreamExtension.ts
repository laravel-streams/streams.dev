// noinspection ES6UnusedImports
import mix                                         from 'laravel-mix';
import { ClassComponent, Dependency }              from 'laravel-mix/types/component';
import { TransformOptions }                        from 'babel-core';
import * as webpack                                from 'webpack';
import { resolve }                                 from 'path';
import MiniCssExtractPlugin                        from 'mini-css-extract-plugin';
import _                                           from 'lodash';
import { existsSync, readFileSync, writeFileSync } from 'fs';
import sass                                        from 'sass';
import * as postcss                                from 'postcss';

declare interface LoaderItem {
    loader: string;
    options: any;
    ident: null | string;
    type: null | string;
}

declare interface LoaderItemSass extends LoaderItem {
    loader: 'sass-loader'
    options: sass.Options
}

declare interface LoaderItemPostcss extends LoaderItem {
    loader: 'postcss-loader'
    options: PostcssOptions
}

declare interface PostcssOptions<PLUGIN = postcss.Plugin> {
    /**
     * Options to pass through to `Postcss`.
     */
    postcssOptions?: postcss.ProcessOptions & {
        plugins: PLUGIN[]
    }
    /**
     * Enables/Disables PostCSS parser support in 'CSS-in-JS' (https://github.com/postcss/postcss-loader#execute)
     */
    execute?: boolean;
    /**
     * Enables/Disables generation of source maps (https://github.com/postcss/postcss-loader#sourcemap)
     */
    sourceMap?: boolean;
}

const isDev = process.env.NODE_ENV === 'development';

class Vendors {
    static vendors: string[] = [];

    static has(val) {
        if ( val.toString().includes('/') ) {
            val = val.toString().split('/')[ 0 ];
        }
        return this.vendors.includes(val.toString());
    }

    static add(val) {
        this.vendors.push(val.toString());
        this.vendors = _.uniq(this.vendors);
        return this;
    }

    static prefix(val) {
        let [ prefix ] = val.toString().split('/', 1);
        return prefix;
    }


    static clear(val) {
        let [ prefix, name ] = val.toString().split('/');
        if ( this.vendors.includes(prefix) ) {
            return name;
        }
        return val;
    }

    static scan(packages) {
        for ( const pkg of packages ) {
            let [ vendor, name ] = pkg.split('/', 2);
            this.add(vendor);
        }
    }
}


export interface LaravelStreamExtensionPacakge {
    name: string
    entry: string
    entryName: string
    prefix: string
    scssRule?: webpack.RuleSetRule
    package:any
    path(...parts): string

    has(...parts): boolean

    read(...parts): string

    write(data, ...parts): void
}


export interface LaravelStreamExtensionOptions {
    packages?: Array<string>
    outputPath?: string
    applyOptimization?: boolean
}

export class LaravelStreamExtension implements ClassComponent {
    public passive: boolean;
    options: LaravelStreamExtensionOptions;
    packages: LaravelStreamExtensionPacakge[];

    public name(): string | string[] {
        return 'streams';
    }

    public register(options: LaravelStreamExtensionOptions): void {
        this.options  = {
            packages         : [],
            outputPath       : 'vendor',
            applyOptimization: true,
            ...options,
        };
        Vendors.scan(this.options.packages);
        this.packages = options.packages.map(name => {
            const path  = (...filepath) => resolve(`vendor/${name}`, ...filepath);
            const has   = (...filepath) => existsSync(path(...filepath));
            const read  = (...filepath) => readFileSync(path(...filepath), 'utf8');
            const write = (data, ...filepath) => writeFileSync(path(...filepath), data, 'utf8');
            return {
                name, path, has, read, write,
                entry    : path(`lib/index.ts`),
                prefix   : Vendors.prefix(name),
                entryName: Vendors.clear(name),
                package: require(path('package.json'))
            };
        });

        return;
    }

    webpackEntry?(entry: any) {
        for ( const pkg of this.packages ) {
            entry.structure[ pkg.name ] = pkg.entry;
        }
        return;
    }

    public webpackConfig(config: webpack.Configuration): void {
        let path = this.options.outputPath;

        let rules    = (config.module?.rules as webpack.RuleSetRule[]);
        let scssRule = rules.find(rule => rule.test.toString() === '/\\.scss$/');
        scssRule.oneOf.forEach(one => {
            let use = (one?.use as { loader: string, options?: any }[]);
            if ( use ) {
                use.shift();
                use.unshift({ loader: MiniCssExtractPlugin.loader });
            }
        });
        let miniCssExtractPlugin: { options: MiniCssExtractPlugin.PluginOptions } = config.plugins.find(plugin => plugin.constructor.name === 'MiniCssExtractPlugin') as any;
        miniCssExtractPlugin.options.filename                                     = (chunk) => {
            let c = chunk.chunk;
            if ( Vendors.has(c.name) ) {
                c.name = Vendors.clear(c.name);
            }
            if ( 'runtime' in c && Vendors.has(c.runtime) ) {
                return `${path}/${c.runtime}/css/[name].css`;
            }
            return '[name].css';
        };
        (miniCssExtractPlugin.options.chunkFilename as any)                       = (chunk) => {
            let c = chunk.chunk;
            if ( 'runtime' in c && Vendors.has(c.runtime) ) {
                let runtime = Vendors.clear(c.runtime);
                return `${path}/${c.runtime}/css/chunk.[name].css`;
            }
            return 'chunk.[name].css';
        };

        config.output    = {
            ...config.output,

            path         : resolve('public'),
            filename     : (chunk) => {
                let c = chunk.chunk;

                if ( Vendors.has(c.name) ) {
                    c.name = Vendors.clear(c.name);
                }
                if ( 'runtime' in c && Vendors.has(c.runtime) ) {
                    return `${path}/${c.runtime}/js/${Vendors.clear(c.name)}.js`;
                }
                // for (const group of c._groups.values()) {
                //     let entry = c.runtime.substr('streams:'.length);
                //     group.name;
                //     continue;
                // }
                return '[name].js';
            },
            chunkFilename: (chunk) => {
                let c = chunk.chunk;
                if ( 'runtime' in c && Vendors.has(c.runtime) ) {
                    return `${path}/${c.runtime}/js/chunk.[name].js`;
                }
                return 'chunk.[name].js';
            },

            library                              : [ 'streams', '[name]' ],
            publicPath                           : '/',
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
        config.externals = config.externals || {};
        this.packages.forEach(pkg => {
            config.externals[pkg.package.name] = ['streams', pkg.entryName]
        });
        config.resolve.extensions.push(...[ '.ts', '.tsx', '.scss' ]);
    }

    public boot(): void {
        return undefined;
    }

    public dependencies(): Dependency | Dependency[] {
        return undefined;
    }

    // public mix(): Record<string, Component> {
    //     return {};
    // }


    // public webpackEntry(entry: any): void {
    //     return ;
    // }

    public webpackPlugins(): webpack.WebpackPluginInstance[] {
        return [];
    }

    public babelConfig(): TransformOptions {
        return {
            // babelrc   : false,
            // // configFile    : false,
            // // cacheDirectory: false, //wp.isDev,
            // compact   : !isDev,
            // sourceMaps: isDev,
            // comments  : isDev,
            // presets   : [ '@babel/preset-env' ],
            // plugins   : [
            //     [ 'import', {
            //         libraryName            : 'lodash',
            //         libraryDirectory       : '',
            //         camel2DashComponentName: false,
            //     } ],
            //     '@babel/plugin-syntax-dynamic-import',
            // ],
        };
    }

    public webpackRules(): webpack.RuleSetRule | webpack.RuleSetRule[] {
        return <webpack.RuleSetRule[]>[
            {
                test   : /\.tsx?$/,
                exclude: /node_modules\//,
                use    : [
                        {
                        loader: 'babel-loader',
                        options: {

                            babelrc   : false,
                            configFile    : false,
                            cacheDirectory: false,
                            compact   : !isDev,
                            sourceMaps: isDev,
                            comments  : isDev,
                            // presets   : [ ['@babel/preset-env'] ],
                            plugins   : [
                                [ 'import', {
                                    libraryName            : 'lodash',
                                    libraryDirectory       : '',
                                    camel2DashComponentName: false,
                                } ],
                                '@babel/plugin-syntax-dynamic-import',
                            ],
                        }
                    },
                    {
                        loader : 'ts-loader',
                        options: {
                            appendTsxSuffixTo: [ /.vue$/ ],
                            configFile       : 'webpack.tsconfig.json',
                            transpileOnly    : true,
                            // experimentalWatchApi: true,
                            // happyPackMode       : true,
                            compilerOptions: {
                                target        : 'es6',
                                module        : 'esnext',
                                importHelpers : false,
                                sourceMap     : isDev,
                                removeComments: !isDev,
                            },
                        },
                    } ],
            },
        ];
    }


}


export default LaravelStreamExtension;
