// noinspection ES6UnusedImports
import mix                                         from 'laravel-mix';
import { ClassComponent, Dependency }              from 'laravel-mix/types/component';
import { TransformOptions }                        from 'babel-core';
import * as webpack                                from 'webpack';
import { resolve }                                 from 'path';
import MiniCssExtractPlugin                        from 'mini-css-extract-plugin';
import _                                           from 'lodash';
import { existsSync, readFileSync, writeFileSync } from 'fs';

declare interface LoaderItem {
    loader: string;
    options: any;
    ident: null | string;
    type: null | string;
}

const isDev = process.env.NODE_ENV === 'development';

class Vendors {
    static vendors: string[] = [];

    static has(val) {return this.vendors.includes(val.toString());}

    static add(val) {
        this.vendors.push(val.toString());
        this.vendors = _.uniq(this.vendors);
        return this;
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

class Prefix {//@formatter:off
    static value = 'streams/';
    static set(val){this.value=val;}
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

export interface LaravelStreamExtensionPacakge {
    name: string
    entry: string
    prefix: string
    tailwindPath: string
    tailwind: boolean
    scssRule?: webpack.RuleSetRule

    path(...parts): string

    has(...parts): boolean

    read(...parts): string

    write(data, ...parts): void
}

export interface LaravelStreamExtensionOptions {
    packages: Array<string>
}

export class LaravelStreamExtension implements ClassComponent {
    public passive: boolean;
    options: LaravelStreamExtensionOptions;
    packages: LaravelStreamExtensionPacakge[];

    public boot(): void {
        return undefined;
    }

    public dependencies(): Dependency | Dependency[] {
        return undefined;
    }

    // public mix(): Record<string, Component> {
    //     return {};
    // }

    public name(): string | string[] {
        return 'streams';
    }

    webpackEntry?(entry: any) {
        Vendors.scan(this.options.packages);
        for ( const pkg of this.packages ) {
            entry.structure[ pkg.name ] = pkg.entry;
        }
        return;
    }

    public register(options: LaravelStreamExtensionOptions): void {
        this.options  = options;
        this.packages = options.packages.map(name => {
            const path  = (...filepath) => resolve(`vendor/${name}`, ...filepath);
            const has   = (...filepath) => existsSync(path(...filepath));
            const read  = (...filepath) => readFileSync(path(...filepath), 'utf8');
            const write = (data, ...filepath) => writeFileSync(path(...filepath), data, 'utf8');
            return {
                name, path, has, read, write,
                entry       : path(`lib/index.ts`),
                prefix      : Vendors.clear(name),
                tailwindPath: path(`tailwind.config.js`),
                tailwind    : has('tailwind.config.js'),
            };
        });

        return;
    }

    public webpackConfig(config: webpack.Configuration): void {

        let rules            = (config.module?.rules as webpack.RuleSetRule[]);
        let scssRule         = rules.find(rule => rule.test.toString() === '/\\.scss$/');
        let originalScssRule = _.cloneDeep(scssRule);
        for ( const pkg of this.packages ) {
            if ( pkg.tailwind ) {
                if ( Array.isArray(scssRule.exclude) ) {
                    scssRule.exclude.push(pkg.path());
                }
                pkg.scssRule         = _.cloneDeep(scssRule);
                pkg.scssRule.include = pkg.path();
                pkg.scssRule.oneOf
                    .filter(oneOf => Array.isArray(oneOf.use as Array<LoaderItem>))
                    .forEach(oneOf => {
                        let postCssLoader = (oneOf.use as Array<LoaderItem>).find(use => use.loader === 'postcss-loader');
                        let plugins       = postCssLoader?.options?.postcssOptions?.plugins;
                        if ( Array.isArray(plugins) ) {
                            let index = plugins.findIndex(plugin => plugin.postcssPlugin === 'tailwind');
                            if ( index ) {
                                plugins[ index ] = require('tailwindcss')(pkg.path('tailwind.config.js'));
                            }
                        }
                    });
                config.module.rules.push(pkg.scssRule);
            }
        }
        let markdownRule = rules.find(rule => rule.test.toString().endsWith('markdown.scss'));
        if ( markdownRule ) {
            let loader  = (markdownRule.use as { loader: string, options: any }[]).find(l => (l as any)?.loader === 'postcss-loader');
            let plugins = loader.options.postcssOptions.plugins;
        }
        let path = 'vendor';
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
                return `${path}/${Vendors.clear(c.runtime)}/css/[name].css`;
            }
            return '[name].css';
        };
        (miniCssExtractPlugin.options.chunkFilename as any)                       = (chunk) => {
            let c = chunk.chunk;
            if ( 'runtime' in c && Vendors.has(c.runtime) ) {
                let runtime = Vendors.clear(c.runtime);
                return `${path}/${runtime}/css/chunk.[name].css`;
            }
            return 'chunk.[name].css';
        };

        config.output = {
            ...config.output,

            path         : resolve('public'),
            filename     : (chunk) => {
                let c = chunk.chunk;

                if ( Vendors.has(c.name) ) {
                    c.name = Vendors.clear(c.name);
                }
                if ( 'runtime' in c && Vendors.has(c.runtime) ) {
                    return `${path}/${Vendors.clear(c.runtime)}/js/[name].js`;
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
                    let runtime = Vendors.clear(c.runtime);
                    return `${path}/${runtime}/js/chunk.[name].js`;
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
        config.resolve.extensions.push(...[ '.ts', '.tsx', '.scss' ]);
    }

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
                use    : [ {
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


export default LaravelStreamExtension;
