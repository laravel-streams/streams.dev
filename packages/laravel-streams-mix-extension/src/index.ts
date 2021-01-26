import mix                                                       from 'laravel-mix';
import { LaravelStreamExtension, LaravelStreamExtensionOptions } from './LaravelStreamExtension';

const extension = new LaravelStreamExtension();
mix.extend('streams', extension);
export {
    LaravelStreamExtensionOptions,
};
export default LaravelStreamExtension;
