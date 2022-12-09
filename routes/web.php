<?php

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

Route::get('docs/{vendor}/{package}', function ($vendor, $package) {
    
    $package = Streams::entries('packages')->where('composer.name', "$vendor/$package")->first();

    dd($package);
});
