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

use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Route;
use Anomaly\Streams\Platform\Support\Facades\Streams;
use Anomaly\Streams\Ui\ControlPanel\ControlPanelBuilder;
use Illuminate\Support\Facades\Request;

Route::streams('/', 'welcome');
// Route::get('/', function() {

//     dd(Streams::repository('pages')->find('test')->validator()->errors()->all());
// });

Route::streams('/routing/{stream}/{entry.id}', [
    'view' => 'welcome',
    //'stream' => 'docs',
    //'entry' => 'introduction',
    'as' => 'streams::docs.index',
    //'redirect' => 'foo/{stream.handle}/{entry.id}',
]);

Route::any('validate/{stream}/{entry}', function($stream, $entry) {

    if (!Request::isLocal()) {
        abort(404);
    }

    dd(Streams::entries($stream)->find($entry)->validator()->passes());
});

Route::any('ui/{stream}/table', function($stream) {
    
    if (!Request::isLocal()) {
        abort(404);
    }

    $stream = Streams::make($stream);

    return $stream
        ->table()
        ->response();
});

Route::any('ui/{stream}/form/{entry?}', function($stream, $entry = null) {
    
    if (!Request::isLocal()) {
        abort(404);
    }
    
    $stream = Streams::make($stream);

    return $stream->form([
        'entry' => $entry,
    ])->response();
});
