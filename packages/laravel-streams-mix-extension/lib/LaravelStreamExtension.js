'use strict';
var __importDefault = (this && this.__importDefault) || function (mod) {
    return (mod && mod.__esModule) ? mod : {'default': mod};
};
Object.defineProperty(exports, '__esModule', {value: true});
exports.LaravelStreamExtension = void 0;
const path_1 = require('path');
const mini_css_extract_plugin_1 = __importDefault(require('mini-css-extract-plugin'));
const lodash_1 = __importDefault(require('lodash'));
const fs_1 = require('fs');
const isDev = process.env.NODE_ENV === 'development';

class Vendors {
    static has(val) {
        return this.vendors.includes(val.toString());
    }

    static add(val) {
        this.vendors.push(val.toString());
        this.vendors = lodash_1.default.uniq(this.vendors);
        return this;
    }

    static clear(val) {
        let [prefix, name] = val.toString().split('/');
        if ( this.vendors.includes(prefix) ) {
            return name;
        }
        return val;
    }

    static scan(packages) {
        for (const pkg of packages) {
            let [vendor, name] = pkg.split('/', 2);
            this.add(vendor);
        }
    }
}

Vendors.vendors = [];

class Prefix {
    static set(val) {
        this.value = val;
    }

    static val(val) {
        return this.pre(val);
    }
    ;

    static has(val) {
        return val.toString().startsWith(this.value);
    }
    ;

    static clear(val) {
        return val.toString().slice(this.value.length);
    }

    static toString() {
        return this.value;
    }

    static pre(val) {
        return this.value + val;
    }
} //@formatter:on
Prefix.value = 'streams/';
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
    boot() {
        return undefined;
    }

    dependencies() {
        return undefined;
    }

    // public mix(): Record<string, Component> {
    //     return {};
    // }
    name() {
        return 'streams';
    }

    webpackEntry(entry) {
        Vendors.scan(this.options.packages);
        for (const pkg of this.packages) {
            entry.structure[pkg.name] = pkg.entry;
        }
        return;
    }

    register(options) {
        this.options = options;
        this.packages = options.packages.map(name => {
            const path = (...filepath) => path_1.resolve(`vendor/${name}`, ...filepath);
            const has = (...filepath) => fs_1.existsSync(path(...filepath));
            const read = (...filepath) => fs_1.readFileSync(path(...filepath), 'utf8');
            const write = (data, ...filepath) => fs_1.writeFileSync(path(...filepath), data, 'utf8');
            return {
                name,
                path,
                has,
                read,
                write,
                entry       : path(`lib/index.ts`),
                prefix      : Vendors.clear(name),
                tailwindPath: path(`tailwind.config.js`),
                tailwind    : has('tailwind.config.js'),
            };
        });
        return;
    }

    webpackConfig(config) {
        var _a;
        let rules = (_a = config.module) === null || _a === void 0 ? void 0 : _a.rules;
        let scssRule = rules.find(rule => rule.test.toString() === '/\\.scss$/');
        let originalScssRule = lodash_1.default.cloneDeep(scssRule);
        for (const pkg of this.packages) {
            if ( pkg.tailwind ) {
                if ( Array.isArray(scssRule.exclude) ) {
                    scssRule.exclude.push(pkg.path());
                }
                pkg.scssRule = lodash_1.default.cloneDeep(scssRule);
                pkg.scssRule.include = pkg.path();
                pkg.scssRule.oneOf
                    .filter(oneOf => Array.isArray(oneOf.use))
                    .forEach(oneOf => {
                        var _a, _b;
                        let postCssLoader = oneOf.use.find(use => use.loader === 'postcss-loader');
                        let plugins = (_b = (_a = postCssLoader === null || postCssLoader === void 0 ? void 0 : postCssLoader.options) === null || _a === void 0 ? void 0 : _a.postcssOptions) === null || _b === void 0 ? void 0 : _b.plugins;
                        if ( Array.isArray(plugins) ) {
                            let index = plugins.findIndex(plugin => plugin.postcssPlugin === 'tailwind');
                            if ( index ) {
                                plugins[index] = require('tailwindcss')(pkg.path('tailwind.config.js'));
                            }
                        }
                    });
                config.module.rules.push(pkg.scssRule);
            }
        }
        let markdownRule = rules.find(rule => rule.test.toString().endsWith('markdown.scss'));
        if ( markdownRule ) {
            let loader = markdownRule.use.find(l => {
                var _a;
                return ((_a = l) === null || _a === void 0 ? void 0 : _a.loader) === 'postcss-loader';
            });
            let plugins = loader.options.postcssOptions.plugins;
        }
        let path = 'vendor';
        scssRule.oneOf.forEach(one => {
            let use = one === null || one === void 0 ? void 0 : one.use;
            if ( use ) {
                use.shift();
                use.unshift({loader: mini_css_extract_plugin_1.default.loader});
            }
        });
        let miniCssExtractPlugin = config.plugins.find(plugin => plugin.constructor.name === 'MiniCssExtractPlugin');
        miniCssExtractPlugin.options.filename = (chunk) => {
            let c = chunk.chunk;
            if ( Vendors.has(c.name) ) {
                c.name = Vendors.clear(c.name);
            }
            if ( 'runtime' in c && Vendors.has(c.runtime) ) {
                return `${path}/${Vendors.clear(c.runtime)}/css/[name].css`;
            }
            return '[name].css';
        };
        miniCssExtractPlugin.options.chunkFilename = (chunk) => {
            let c = chunk.chunk;
            if ( 'runtime' in c && Vendors.has(c.runtime) ) {
                let runtime = Vendors.clear(c.runtime);
                return `${path}/${runtime}/css/chunk.[name].css`;
            }
            return 'chunk.[name].css';
        };
        config.output = Object.assign(Object.assign({}, config.output), {
            path                                 : path_1.resolve('public'),
            filename                             : (chunk) => {
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
            chunkFilename                        : (chunk) => {
                let c = chunk.chunk;
                if ( 'runtime' in c && Vendors.has(c.runtime) ) {
                    let runtime = Vendors.clear(c.runtime);
                    return `${path}/${runtime}/js/chunk.[name].js`;
                }
                return 'chunk.[name].js';
            },
            library                              : ['streams', '[name]'],
            publicPath                           : '/',
            libraryTarget                        : 'window',
            devtoolFallbackModuleFilenameTemplate: 'webpack:///[resource-path]?[hash]',
            devtoolModuleFilenameTemplate        : info => {
                var $filename = 'sources://' + info.resourcePath;
                $filename = 'webpack:///' + info.resourcePath; // +'?' + info.hash;
                if ( info.resourcePath.match(/\.vue$/) && !info.allLoaders.match(/type=script/) && !info.query.match(/type=script/) ) {
                    $filename = 'webpack-generated:///' + info.resourcePath; // + '?' + info.hash;
                }
                return $filename;
            }
        });
        config.resolve.extensions.push(...['.ts', '.tsx', '.scss']);
    }

    // public webpackEntry(entry: any): void {
    //     return ;
    // }
    webpackPlugins() {
        return [];
    }

    babelConfig() {
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

    webpackRules() {
        return [
            {
                test   : /\.tsx?$/,
                exclude: /node_modules\//,
                use    : [{
                    loader : 'ts-loader',
                    options: {
                        appendTsxSuffixTo: [/.vue$/],
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
                }],
            },
        ];
    }
}

exports.LaravelStreamExtension = LaravelStreamExtension;
exports.default = LaravelStreamExtension;
//# sourceMappingURL=LaravelStreamExtension.js.map
