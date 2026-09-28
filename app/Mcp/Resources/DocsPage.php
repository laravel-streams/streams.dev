<?php

namespace App\Mcp\Resources;

use App\Support\DocsQuery;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\Server\Attributes\Description;
use Laravel\Mcp\Server\Attributes\MimeType;
use Laravel\Mcp\Server\Attributes\Name;
use Laravel\Mcp\Server\Attributes\Title;
use Laravel\Mcp\Server\Contracts\HasUriTemplate;
use Laravel\Mcp\Server\Resource;
use Laravel\Mcp\Support\UriTemplate;

#[Name('docs-page')]
#[Title('Streams docs page')]
#[Description('One streams.dev docs page as markdown. {package} is "guides" for hub guides or core, ui, api, sdk, testing, client; {page} is the page id, e.g. streams-docs://pages/core/introduction.')]
#[MimeType('text/markdown')]
class DocsPage extends Resource implements HasUriTemplate
{
    public function uriTemplate(): UriTemplate
    {
        return new UriTemplate('streams-docs://pages/{package}/{page}');
    }

    public function handle(Request $request): Response
    {
        $package = (string) $request->get('package');
        $page = (string) $request->get('page');

        [$document] = DocsQuery::resolve($package === 'guides' ? $page : $package.'/'.$page);

        return $document
            ? Response::text(DocsQuery::markdown($document))
            : Response::error('No docs page at streams-docs://pages/'.$package.'/'.$page.'.');
    }
}
