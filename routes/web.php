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
use Anomaly\Streams\Platform\Support\Facades\Streams;

Route::view('/', 'welcome');

Route::any('test/builders', function() {
    dd(Streams::table([
        'stream' => 'docs',
        //'entry' => 'introduction',
        'buttons' => [
            'edit' => [
                'href' => 'admin/{entry.id}'
            ],
        ],
    ])->build());
});

// Route::any('docs/{handle}', [
//     'stream' => 'docs',
//     'uses' => '\Anomaly\Streams\Platform\Http\Controller\EntryController@view'
// ]);
