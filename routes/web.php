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
use App\Support\ExploreTree;

Route::get('/search/docs.json', fn () => response()->json(DocsSearchIndex::all()));

Route::view('api-test', 'api');
Route::view('ui-test', 'ui');

/*
| Explore: /explore redirects to the root node; every node is also served as
| Markdown and JSON so agents can walk the same tree as people.
*/
Route::redirect('explore', '/explore/'.ExploreTree::ROOT);

Route::get('explore.json', fn () => response()->json(ExploreTree::toTree(), 200, [], JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT));

Route::get('explore/{id}.{format}', function (string $id, string $format) {
    $entry = ExploreTree::find($id) ?? abort(404);

    return $format === 'json'
        ? response()->json(ExploreTree::toArray($entry), 200, [], JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT)
        : response(ExploreTree::toMarkdown($entry), 200, ['Content-Type' => 'text/markdown; charset=UTF-8']);
})->where(['id' => '[a-z0-9-]+', 'format' => 'md|json']);
