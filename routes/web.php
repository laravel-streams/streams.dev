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
use App\Support\LlmsText;

Route::get('/search/docs.json', fn () => response()->json(DocsSearchIndex::all()));

/*
 * Machine-readable docs for agents (https://llmstxt.org), built from
 * DocsSearchIndex on request and cached alongside the search index.
 */
$text = ['Content-Type' => 'text/plain; charset=UTF-8'];
$markdown = ['Content-Type' => 'text/markdown; charset=UTF-8'];

Route::get('/llms.txt', fn () => response(LlmsText::index(), 200, $text));
Route::get('/llms-full.txt', fn () => response(LlmsText::full(), 200, $text));

Route::get('/docs/api/openapi.yaml', fn () => response(
    file_get_contents(base_path('streams/data/api_docs/openapi.yaml')),
    200,
    ['Content-Type' => 'application/yaml; charset=UTF-8']
));

// Raw markdown for every docs page: /docs/{id}.md and /docs/{package}/{id}.md
Route::get('/docs/{path}.md', function (string $path) use ($markdown) {
    $segments = explode('/', $path);
    $id = array_pop($segments);
    $prefix = '/docs'.($segments ? '/'.implode('/', $segments) : '');

    abort_unless($document = DocsSearchIndex::find($prefix, $id), 404);

    return response(LlmsText::page($document), 200, $markdown);
})->where('path', '[A-Za-z0-9_-]+(/[A-Za-z0-9_-]+)?');

Route::view('api-test', 'api');
Route::view('ui-test', 'ui');
