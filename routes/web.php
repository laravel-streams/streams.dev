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
use App\Support\LlmsText;

Route::get('/search/docs.json', fn () => response()->json(DocsSearchIndex::all()));

Route::get('/schema/streams.schema.json', function () {
    $path = public_path('schema/streams.schema.json');
    abort_unless(is_file($path), 404);

    return response(file_get_contents($path), 200, [
        'Content-Type' => 'application/json; charset=UTF-8',
    ]);
});

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

/*
| Explore: /explore redirects to the root node; every node is also served as
| Markdown and JSON so agents can walk the same tree as people.
|
| These paths do not overlap the docs markdown routes above, or the explore
| stream route explore/{id} (that constraint is [a-z0-9-]+, with no dot).
*/
Route::redirect('explore', '/explore/'.ExploreTree::ROOT);

Route::get('explore.json', fn () => response()->json(ExploreTree::toTree(), 200, [], JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT));

Route::get('explore/{id}.{format}', function (string $id, string $format) {
    $entry = ExploreTree::find($id) ?? abort(404);

    return $format === 'json'
        ? response()->json(ExploreTree::toArray($entry), 200, [], JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT)
        : response(ExploreTree::toMarkdown($entry), 200, ['Content-Type' => 'text/markdown; charset=UTF-8']);
})->where(['id' => '[a-z0-9-]+', 'format' => 'md|json']);
