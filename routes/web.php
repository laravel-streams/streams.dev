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
use Anomaly\Streams\Ui\Form\FormBuilder;
use Anomaly\Streams\Platform\Support\Workflow;

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
        // 'callback' => function ($callback, $payload) use ($builder) {
        //     $builder->fire(implode('_', [
        //         $callback['workflow'],
        //         $callback['name']
        //     ]), $payload);
        // }
    ]))->passThrough($builder);

    $workflow->steps = array_merge($workflow->steps, ['third_step' => function() {
        \Log::info('Third (Custom) Step Output');
    }]);
    
    $builder->on('build_after_second_step', function() {
        \Log::info('AFTER Second Step Output');
    });

    $builder->on('build_after_third_step', function() {
        \Log::info('AFTER THIRD (Custom) Step Output');
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
