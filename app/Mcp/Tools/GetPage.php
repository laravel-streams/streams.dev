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

#[Name('get_page')]
#[Title('Read a Streams docs page')]
#[Description('Returns one streams.dev docs page as markdown, with a YAML frontmatter block (title, description, slug, url, markdown_url, package). Accepts a slug ("installation", "core/introduction"), a docs path ("/docs/core/introduction") or a full URL, with or without ".md".')]
#[IsReadOnly]
#[IsIdempotent]
#[IsOpenWorld(false)]
class GetPage extends Tool
{
    public function handle(Request $request): Response|ResponseFactory
    {
        $validated = $request->validate([
            'page' => ['required', 'string', 'max:300'],
        ], [
            'page.required' => 'Pass a page slug such as "core/introduction", or a streams.dev docs URL.',
        ]);

        [$document, $candidates] = DocsQuery::resolve($validated['page']);

        if ($document) {
            return Response::text(DocsQuery::markdown($document));
        }

        if ($candidates !== []) {
            return Response::error('"'.$validated['page'].'" matches more than one page. Use one of these slugs: '.implode(', ', $candidates).'.');
        }

        return Response::error('No docs page matches "'.$validated['page'].'". Use search_docs or list_pages to find the slug.');
    }

    public function schema(JsonSchema $schema): array
    {
        return [
            'page' => $schema->string()
                ->description('Page slug ("installation", "core/introduction"), docs path ("/docs/ui/forms") or URL ("https://streams.dev/docs/api/routes.md").')
                ->required(),
        ];
    }
}
