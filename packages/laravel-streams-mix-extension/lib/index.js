"use strict";
var __importDefault = (this && this.__importDefault) || function (mod) {
    return (mod && mod.__esModule) ? mod : { "default": mod };
};
Object.defineProperty(exports, "__esModule", { value: true });
const laravel_mix_1 = __importDefault(require("laravel-mix"));
const LaravelStreamExtension_1 = require("./LaravelStreamExtension");
const extension = new LaravelStreamExtension_1.LaravelStreamExtension();
laravel_mix_1.default.extend('streams', extension);
exports.default = LaravelStreamExtension_1.LaravelStreamExtension;
//# sourceMappingURL=index.js.map