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
use Illuminate\Support\Facades\View;
use Streams\Core\View\ViewTemplate;

Route::streams('/', [
    'entry' => 'homepage',
    'stream' => 'pages',
]);

Route::redirect('discord', 'https://discord.gg/Sh79MvV');

Route::streams('{entry.path}', [
    'stream' => 'pages',
]);

Route::get('/blade/test', function () {
    return View::make('cp');
});
