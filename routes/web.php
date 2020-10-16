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

use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Route;
use Streams\Core\Support\Facades\Streams;

Route::streams('/', 'welcome');

Route::any('cp/{stream}', function ($stream) {

    if (App::environment() !== 'local') {
        abort(404);
    }

    $stream = Streams::make($stream);

    return $stream
        ->table()
        ->response();
});

Route::any('cp/{stream}/{entry?}', function ($stream, $entry = null) {

    if (App::environment() !== 'local') {
        abort(404);
    }

    $stream = Streams::make($stream);

    return $stream
        ->form([
            'entry' => $entry
        ])
        ->response();
});
