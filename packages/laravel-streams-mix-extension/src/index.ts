import api                                                       from 'laravel-mix';
import { LaravelStreamExtension, LaravelStreamExtensionOptions } from './LaravelStreamExtension';

const extension = new LaravelStreamExtension();
api.extend('streams', extension);
export {
    LaravelStreamExtensionOptions,
};

export default LaravelStreamExtension;

declare global {
    var mix: typeof api & {
        streams(options:LaravelStreamExtensionOptions)
    }
}


