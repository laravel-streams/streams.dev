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
use Anomaly\Streams\Ui\Support\Builder;
use Illuminate\Support\Facades\Response;
use Anomaly\Streams\Platform\Support\Workflow;
use Anomaly\Streams\Platform\Ui\Table\TableBuilder;
use Anomaly\Streams\Platform\Support\Facades\Streams;

Route::view('/', 'welcome');

// Route::any('test/array', function() {
//     return Response::json(Streams::entries('docs')->find('directory-structure')->toArray());
// });

// Route::any('docs/{handle}', [
//     'stream' => 'docs',
//     'uses' => '\Anomaly\Streams\Platform\Http\Controller\EntryController@view'
// ]);

Route::any('/test/foo', function () {

    $builder = new Builder;

    $workflow = (new Workflow([
        'name' => 'build',
        'steps' => [
            'first_step' => function () {
                \Log::info('First Step Output');
            },
            'second_step' => function () {
                \Log::info('Second Step Output');
            }
        ],
        'callback' => function ($callback, $payload) use ($builder) {
            $builder->fire(implode('_', [
                $callback['workflow'],
                $callback['name']
            ]), $payload);
        }
    ]));

    $builder->on('build_after_second_step', function() {
        \Log::info('AFTER Second Step Output');
    });

    $workflow->process();
});

// Route::get('/garden', function () {

//     return (new TableBuilder([
//         'stream' => 'plants',
//         'filters' => [
//             'name',
//             'type',
//         ],
//         'columns' => [
//             'name',
//             'type',
//         ],
//         'buttons' => [
//             'edit' => [
//                 'href' => 'garden/edit/{entry.id}'
//             ],
//             'view' => [
//                 'href' => 'garden/{entry.id}'
//             ],
//         ],
//     ]))->response();
// });

// Route::get('/garden/edit/{id}', function ($id) {
//     return (new FormBuilder([
//         'stream' => 'plants',
//         'entry' => $id,
//     ]))->response();
// });
