<?php

namespace App\Mcp\Tools;

use App\Support\DocsQuery;
use Illuminate\Contracts\JsonSchema\JsonSchema;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\ResponseFactory;
use Laravel\Mcp\Server\Attributes\Description;
use Laravel\Mcp\Server\Attributes\Name;
use Laravel\Mcp\Server\Attributes\Title;
use Laravel\Mcp\Server\Tool;
use Laravel\Mcp\Server\Tools\Annotations\IsIdempotent;
use Laravel\Mcp\Server\Tools\Annotations\IsOpenWorld;
use Laravel\Mcp\Server\Tools\Annotations\IsReadOnly;

#[Name('search_docs')]
#[Title('Search the Streams docs')]
#[Description('Full-text search over every streams.dev docs page (hub guides and the core, ui, api, sdk, testing and client references). Returns ranked results with title, slug, URL, markdown URL, package and a snippet around the first match. Pass a result\'s slug to get_page to read the whole page.')]
#[IsReadOnly]
#[IsIdempotent]
#[IsOpenWorld(false)]
class SearchDocs extends Tool
{
    public function handle(Request $request): Response|ResponseFactory
    {
        $validated = $request->validate([
            'query' => ['required', 'string', 'max:200'],
            'package' => ['nullable', 'string', 'in:'.implode(',', DocsQuery::PACKAGES)],
            'section' => ['nullable', 'string', 'in:'.implode(',', DocsQuery::SECTIONS)],
            'limit' => ['nullable', 'integer', 'min:1', 'max:50'],
        ], [
            'query.required' => 'Pass a search query, for example "criteria where" or "field types".',
            'package.in' => 'package must be one of: '.implode(', ', DocsQuery::PACKAGES).'.',
            'section.in' => 'section must be "guide" (hub guides) or "reference" (package references).',
        ]);

        $results = DocsQuery::search(
            $validated['query'],
            $validated['package'] ?? null,
            $validated['section'] ?? null,
            (int) ($validated['limit'] ?? 10),
        );

        return Response::structured([
            'query' => $validated['query'],
            'count' => count($results),
            'results' => $results,
        ]);
    }

    public function schema(JsonSchema $schema): array
    {
        return [
            'query' => $schema->string()
                ->description('Words to search for, e.g. "repository cache" or "Route::streams". Case- and accent-insensitive; every word must match.')
                ->required(),
            'package' => $schema->string()
                ->enum(DocsQuery::PACKAGES)
                ->description('Only search one package\'s pages. "guides" is the hub guides under /docs/{slug}.'),
            'section' => $schema->string()
                ->enum(DocsQuery::SECTIONS)
                ->description('"guide" for hub guides, "reference" for package reference pages.'),
            'limit' => $schema->integer()
                ->min(1)
                ->max(50)
                ->description('Maximum number of results (default 10).')
                ->default(10),
        ];
    }
}
