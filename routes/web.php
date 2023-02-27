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
use Streams\Core\Support\Facades\Streams;

Route::streams('/', [
    'entry' => 'homepage',
    'stream' => 'pages',
]);

Route::get('/packages/category/{category}', function () {
    return view('packages');
});

Route::get('/packages/{vendor}/{package}', function ($vendor, $package) {

    $package = Streams::packages()->where('name', "$vendor/$package")->first();

    return view('package', [
        'package' => $package,
    ]);
});

Route::redirect('discord', 'https://discord.gg/Sh79MvV');

Route::streams('{entry.path}', [
    'stream' => 'pages',
]);
