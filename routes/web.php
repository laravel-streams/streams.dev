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

Route::streams('/', [
    'entry' => 'homepage',
    'stream' => 'pages',
]);

Route::redirect('discord', 'https://discord.gg/Sh79MvV');
