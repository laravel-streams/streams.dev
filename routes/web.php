<?php

use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Route;
use Streams\Core\Support\Facades\Streams;
use Streams\Ui\Support\Facades\UI;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::view('api-test', 'api');
Route::view('ui-test', 'ui');
Route::any('form-test', function () {

    /**
     * Restore the component
     * from cache and by ID.
     */
    $cache = json_decode(cache(request('_id')), true);

    $component = UI::make($cache['component'], $cache['attributes']);

    /**
     * Validate the request.
     */
    $validator = $component->validator(Request::except(['_id', '_token']));

    if ($validator->fails()) {
        return response()->json([
            'errors' => $validator->errors()->toArray(),
        ], 422);
    }

    if ($component->success_redirect) {
        return redirect($component->success_redirect . '?messages=' . $component->success_message);
    }

    dd('Valid!');
});
