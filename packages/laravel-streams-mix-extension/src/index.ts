import mix                        from 'laravel-mix';
import { LaravelStreamExtension } from './LaravelStreamExtension';
const extension = new LaravelStreamExtension();
mix.extend('streamify', extension)

