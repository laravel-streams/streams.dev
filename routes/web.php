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
use Streams\Core\Support\Facades\Application;
use Streams\Core\Support\Facades\Transformer;

Route::streams('/', 'welcome');

Route::get('test', function () {

    Transformer::transform(Application::active(), [
        'config' => [
            'app.name' => 'TEST',
        ]
    ]);
});

Route::redirect('discord', 'https://discord.gg/Sh79MvV');
