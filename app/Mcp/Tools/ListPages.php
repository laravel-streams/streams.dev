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

#[Name('list_pages')]
#[Title('List the Streams docs pages')]
#[Description('Returns the streams.dev docs navigation: hub guides grouped by category, then each package reference (core, ui, api, sdk, testing, client), with every page\'s title, slug and URL in reading order. Filter by package or section to keep it short.')]
#[IsReadOnly]
#[IsIdempotent]
#[IsOpenWorld(false)]
class ListPages extends Tool
{
    public function handle(Request $request): Response|ResponseFactory
    {
        $validated = $request->validate([
            'package' => ['nullable', 'string', 'in:'.implode(',', DocsQuery::PACKAGES)],
            'section' => ['nullable', 'string', 'in:'.implode(',', DocsQuery::SECTIONS)],
        ], [
            'package.in' => 'package must be one of: '.implode(', ', DocsQuery::PACKAGES).'.',
            'section.in' => 'section must be "guide" (hub guides) or "reference" (package references).',
        ]);

        $groups = DocsQuery::navigation($validated['package'] ?? null, $validated['section'] ?? null);

        return Response::structured([
            'count' => array_sum(array_map(fn ($group) => count($group['pages']), $groups)),
            'groups' => $groups,
        ]);
    }

    public function schema(JsonSchema $schema): array
    {
        return [
            'package' => $schema->string()
                ->enum(DocsQuery::PACKAGES)
                ->description('Only list one package. "guides" is the hub guides.'),
            'section' => $schema->string()
                ->enum(DocsQuery::SECTIONS)
                ->description('"guide" for hub guides, "reference" for package reference pages.'),
        ];
    }
}
