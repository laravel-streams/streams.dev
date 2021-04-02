<?php

use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\URL;
use Streams\Core\Support\Facades\Streams;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::get('/search', function () {

    $q = Request::get('q');

    if (strlen($q) < 3) {
        abort(400, 'Not enough characters.');
    }

    $results = new Collection();

    $words = explode(' ', $q);

    $streams = ['docs', 'docs_core', 'docs_ui', 'docs_api', 'docs_cli'];

    foreach ($streams as $stream) {

        $callback = function ($entry) use ($stream, $results) {
            $results->put($stream . '.' . $entry->id, $entry);
        };

        Streams::entries($stream)
            ->where('title', 'like', $q)
            ->where('enabled', true)
            ->limit(15)
            ->get()
            ->each($callback);
    }

    foreach ($streams as $stream) {

        $callback = function ($entry) use ($stream, $results) {
            $results->put($stream . '.' . $entry->id, $entry);
        };

        Streams::entries($stream)
            ->where('body', 'like', $q)
            ->where('enabled', true)
            ->limit(15)
            ->get()
            ->each($callback);
    }

    foreach ($streams as $stream) {

        $callback = function ($entry) use ($stream, $results) {
            $results->put($stream . '.' . $entry->id, $entry);
        };

        foreach ($words as $word) {
            Streams::entries($stream)
                ->where('enabled', true)
                ->where('title', 'like', $word)
                ->orWhere('body', 'like', $word)
                ->limit(15)
                ->get()
                ->each($callback);
        }
    }

    $results->each(function ($entry) {
        $entry->href = URL::to($entry->stream()->url_prefix . '/' . $entry->id);
        $entry->breadcrumb = $entry->stream()->name . ' > ' . $entry->title;
    });

    return $results->values();
});
