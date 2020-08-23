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
use Anomaly\Streams\Platform\Support\Facades\Streams;

Route::view('/', 'welcome');

Route::any('test/builders', function() {
    // Streams::make('docs')->table($table = 'default');
    // 'Default' is the 'ui.form' configuration
    return Streams::table([
        'stream' => 'docs',
        'columns' => [
            'title',
            'category',
            'stage',
        ],
        'options' => [
            'title' => 'Example Title',
            'description' => 'Example Description',
            'order_by' => [
                'category' => 'asc'
            ]
        ],
        'buttons' => [
            'edit' => [
                'href' => 'admin/{entry.id}'
            ],
        ],
    ])->response();
});

// Route::any('docs/{handle}', [
//     'stream' => 'docs',
//     'uses' => '\Anomaly\Streams\Platform\Http\Controller\EntryController@view'
// ]);
