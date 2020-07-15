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

use Anomaly\Streams\Platform\Ui\Form\FormBuilder;
use Anomaly\Streams\Platform\Ui\Table\TableBuilder;
use Anomaly\Streams\Platform\Support\Facades\Application;
use Anomaly\Streams\Platform\Application\ApplicationManager;

Route::view('/', 'welcome');

Route::any('test/seo', function() {
    dd(Streams::entries('pages')->find('about')->expand('docs'));
});

// Route::any('docs/{handle}', [
//     'stream' => 'docs',
//     'uses' => '\Anomaly\Streams\Platform\Http\Controller\EntryController@view'
// ]);

// Route::any('/test', function () {

    
//     $builder = (new TableBuilder([
//         'stream' => 'plants',
//         'columns' => [
//             'name',
//         ],
//         'buttons' => [
//             'view',
//         ],
//     ]));

//     return $builder->response();
// });

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
