<?php

namespace App\Mcp\Resources;

use App\Support\LlmsText;
use Laravel\Mcp\Response;
use Laravel\Mcp\Server\Attributes\Description;
use Laravel\Mcp\Server\Attributes\MimeType;
use Laravel\Mcp\Server\Attributes\Name;
use Laravel\Mcp\Server\Attributes\Title;
use Laravel\Mcp\Server\Attributes\Uri;
use Laravel\Mcp\Server\Resource;

#[Name('docs-index')]
#[Title('Streams docs index (llms.txt)')]
#[Description('The /llms.txt index: a one-paragraph summary of Streams and a link to every docs page, grouped like the site navigation.')]
#[Uri('streams-docs://llms.txt')]
#[MimeType('text/markdown')]
class DocsIndex extends Resource
{
    public function handle(): Response
    {
        return Response::text(LlmsText::index());
    }
}
