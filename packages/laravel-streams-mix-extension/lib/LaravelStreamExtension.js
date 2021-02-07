"use strict";
var __importDefault = (this && this.__importDefault) || function (mod) {
    return (mod && mod.__esModule) ? mod : { "default": mod };
};
Object.defineProperty(exports, "__esModule", { value: true });
exports.LaravelStreamExtension = void 0;
const path_1 = require("path");
const mini_css_extract_plugin_1 = __importDefault(require("mini-css-extract-plugin"));
const lodash_1 = __importDefault(require("lodash"));
const fs_1 = require("fs");
const isDev = process.env.NODE_ENV === 'development';
class Vendors {
    static has(val) {
        if (val.toString().includes('/')) {
            val = val.toString().split('/')[0];
        }
        return this.vendors.includes(val.toString());
    }
    static add(val) {
        this.vendors.push(val.toString());
        this.vendors = lodash_1.default.uniq(this.vendors);
        return this;
    }
    static prefix(val) {
        let [prefix] = val.toString().split('/', 1);
        return prefix;
    }
    static clear(val) {
        let [prefix, name] = val.toString().split('/');
        if (this.vendors.includes(prefix)) {
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
class LaravelStreamExtension {
    name() {
        return 'streams';
    }
    register(options) {
        this.options = Object.assign({ packages: [], outputPath: 'vendor', applyOptimization: true }, options);
        Vendors.scan(this.options.packages);
        this.packages = options.packages.map(name => {
            const path = (...filepath) => path_1.resolve(`vendor/${name}`, ...filepath);
            const has = (...filepath) => fs_1.existsSync(path(...filepath));
            const read = (...filepath) => fs_1.readFileSync(path(...filepath), 'utf8');
            const write = (data, ...filepath) => fs_1.writeFileSync(path(...filepath), data, 'utf8');
            return {
                name, path, has, read, write,
                entry: path(`lib/index.ts`),
                prefix: Vendors.prefix(name),
                entryName: Vendors.clear(name),
                package: require(path('package.json'))
            };
        });
        return;
    }
    webpackEntry(entry) {
        for (const pkg of this.packages) {
            entry.structure[pkg.name] = pkg.entry;
        }
        return;
    }
    webpackConfig(config) {
        var _a;
        let path = this.options.outputPath;
        let rules = (_a = config.module) === null || _a === void 0 ? void 0 : _a.rules;
        let scssRule = rules.find(rule => rule.test.toString() === '/\\.scss$/');
        scssRule.oneOf.forEach(one => {
            let use = one === null || one === void 0 ? void 0 : one.use;
            if (use) {
                use.shift();
                use.unshift({ loader: mini_css_extract_plugin_1.default.loader });
            }
        });
        let miniCssExtractPlugin = config.plugins.find(plugin => plugin.constructor.name === 'MiniCssExtractPlugin');
        miniCssExtractPlugin.options.filename = (chunk) => {
            let c = chunk.chunk;
            if (Vendors.has(c.name)) {
                c.name = Vendors.clear(c.name);
            }
            if ('runtime' in c && Vendors.has(c.runtime)) {
                return `${path}/${c.runtime}/css/[name].css`;
            }
            return '[name].css';
        };
        miniCssExtractPlugin.options.chunkFilename = (chunk) => {
            let c = chunk.chunk;
            if ('runtime' in c && Vendors.has(c.runtime)) {
                let runtime = Vendors.clear(c.runtime);
                return `${path}/${c.runtime}/css/chunk.[name].css`;
            }
            return 'chunk.[name].css';
        };
        config.output = Object.assign(Object.assign({}, config.output), { path: path_1.resolve('public'), filename: (chunk) => {
                let c = chunk.chunk;
                if (Vendors.has(c.name)) {
                    c.name = Vendors.clear(c.name);
                }
                if ('runtime' in c && Vendors.has(c.runtime)) {
                    return `${path}/${c.runtime}/js/${Vendors.clear(c.name)}.js`;
                }
                // for (const group of c._groups.values()) {
                //     let entry = c.runtime.substr('streams:'.length);
                //     group.name;
                //     continue;
                // }
                return '[name].js';
            }, chunkFilename: (chunk) => {
                let c = chunk.chunk;
                if ('runtime' in c && Vendors.has(c.runtime)) {
                    return `${path}/${c.runtime}/js/chunk.[name].js`;
                }
                return 'chunk.[name].js';
            }, library: ['streams', '[name]'], publicPath: '/', libraryTarget: 'window', devtoolFallbackModuleFilenameTemplate: 'webpack:///[resource-path]?[hash]', devtoolModuleFilenameTemplate: info => {
                var $filename = 'sources://' + info.resourcePath;
                $filename = 'webpack:///' + info.resourcePath; // +'?' + info.hash;
                if (info.resourcePath.match(/\.vue$/) && !info.allLoaders.match(/type=script/) && !info.query.match(/type=script/)) {
                    $filename = 'webpack-generated:///' + info.resourcePath; // + '?' + info.hash;
                }
                return $filename;
            } });
        config.externals = config.externals || {};
        this.packages.forEach(pkg => {
            config.externals[pkg.package.name] = ['streams', pkg.entryName];
        });
        config.resolve.extensions.push(...['.ts', '.tsx', '.scss']);
    }
    boot() {
        return undefined;
    }
    dependencies() {
        return undefined;
    }
    // public mix(): Record<string, Component> {
    //     return {};
    // }
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
                test: /\.tsx?$/,
                exclude: /node_modules\//,
                use: [
                    {
                        loader: 'babel-loader',
                        options: {
                            babelrc: false,
                            configFile: false,
                            cacheDirectory: false,
                            compact: !isDev,
                            sourceMaps: isDev,
                            comments: isDev,
                            // presets   : [ ['@babel/preset-env'] ],
                            plugins: [
                                ['import', {
                                        libraryName: 'lodash',
                                        libraryDirectory: '',
                                        camel2DashComponentName: false,
                                    }],
                                '@babel/plugin-syntax-dynamic-import',
                            ],
                        }
                    },
                    {
                        loader: 'ts-loader',
                        options: {
                            appendTsxSuffixTo: [/.vue$/],
                            configFile: 'webpack.tsconfig.json',
                            transpileOnly: true,
                            // experimentalWatchApi: true,
                            // happyPackMode       : true,
                            compilerOptions: {
                                target: 'es6',
                                module: 'esnext',
                                importHelpers: false,
                                sourceMap: isDev,
                                removeComments: !isDev,
                            },
                        },
                    }
                ],
            },
        ];
    }
}
exports.LaravelStreamExtension = LaravelStreamExtension;
exports.default = LaravelStreamExtension;
//# sourceMappingURL=LaravelStreamExtension.js.map