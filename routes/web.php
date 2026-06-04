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

use App\Support\DocsSearchIndex;

Route::get('/search/docs.json', fn () => response()->json(DocsSearchIndex::all()));

Route::view('api-test', 'api');
Route::view('ui-test', 'ui');
