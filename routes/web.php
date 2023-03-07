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
    'middleware' => ['ttl:600']
]);

Route::get('/packages/category/{category}', function ($category) {

    $packages = Streams::packages()
        ->where('categories', 'contains', $category)
        ->paginate();

    return view('packages', [
        'packages' => $packages,
        'title' => Streams::make('packages')->fields->categories->options()[$category],
        'description' => null,
    ]);
});

Route::get('/packages/{vendor}', function ($vendor) {

    $packages = Streams::packages()
        ->where('name', 'like', "$vendor/%")
        ->paginate();

        return view('packages', [
            'packages' => $packages,
            'title' => $vendor,
            'description' => 'Username and description and such here.',
        ]);
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
