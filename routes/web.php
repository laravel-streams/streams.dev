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
use Anomaly\Streams\Ui\Form\FormBuilder;

Route::view('/', 'welcome');

Route::any('test/builders', function() {
    dd((new FormBuilder([
        'stream' => 'docs',
    ]))->build());
});

// Route::any('docs/{handle}', [
//     'stream' => 'docs',
//     'uses' => '\Anomaly\Streams\Platform\Http\Controller\EntryController@view'
// ]);
