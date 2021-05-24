<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| This file is where you may define all of the routes that are handled
| by your application. Just tell Laravel the URIs it should respond
| to using a Closure or controller method. Build something great!
|
*/

use Illuminate\Support\Facades\Route;

Route::streams('/', 'welcome');
Route::redirect('discord', 'https://discord.gg/Sh79MvV');

Route::ui('foo/bar', [
    //'verb' => 'get',
    'stream' => 'docs',
    'entry' => false,
    'ui.component' => 'table',
]);

Route::streams('account/settings', [
    'uses' => '\App\Http\Controllers\Account\ShowAccountSettings',
]);
