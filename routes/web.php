<?php

use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Route;
use Streams\Core\Support\Facades\Streams;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::view('api-test', 'api');
Route::view('ui-test', 'ui');
Route::any('form-test', function ()
{
    dump(Request::method());
    dd(Request::input());
});
