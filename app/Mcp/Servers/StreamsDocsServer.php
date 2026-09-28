<?php

namespace App\Mcp\Servers;

use App\Mcp\Resources\DocsIndex;
use App\Mcp\Resources\DocsPage;
use App\Mcp\Tools\GetPage;
use App\Mcp\Tools\GetSchema;
use App\Mcp\Tools\ListPages;
use App\Mcp\Tools\SearchDocs;
use Laravel\Mcp\Server;
use Laravel\Mcp\Server\Attributes\Instructions;
use Laravel\Mcp\Server\Attributes\Name;
use Laravel\Mcp\Server\Attributes\Version;

/**
 * Read-only MCP server over the streams.dev documentation.
 *
 * Remote: POST /mcp (routes/ai.php, rate limited by the "mcp" limiter).
 * Local:  php artisan mcp:start streams-docs
 */
#[Name('Streams Docs')]
#[Version('1.0.0')]
#[Instructions(<<<'MARKDOWN'
    Streams (laravel-streams) is a set of Laravel packages for building data-driven apps from JSON configuration. A stream is a JSON file in streams/ that declares fields, validation, a storage source (flat files, database, API) and routes; the packages provide repositories and criteria queries (streams/core), Livewire admin panels, forms and tables (streams/ui), a REST API (streams/api), generators and schema validation (streams/sdk), test helpers (streams/testing) and a JavaScript client (@laravel-streams/api-client).

    This server gives read-only access to the official docs at streams.dev:
    - search_docs: find pages by keywords (filter by package: guides, core, ui, api, sdk, testing, client; or by section: get-started, guides, concepts, reference, packages, contributing).
    - get_page: read a whole page as markdown by slug ("core/introduction", "installation") or URL.
    - list_pages: the docs navigation, to browse a package or see what exists.
    - get_schema: the JSON Schema for streams/*.json stream definitions.

    Search first, then read the pages you need with get_page. Cite page URLs when you answer. Prefer these docs over memory: the packages are pre-release (streams/core 2.0.x-dev, other packages 1.0.x-dev), so older examples may be wrong.
    MARKDOWN)]
class StreamsDocsServer extends Server
{
    protected array $tools = [
        SearchDocs::class,
        GetPage::class,
        ListPages::class,
        GetSchema::class,
    ];

    protected array $resources = [
        DocsIndex::class,
        DocsPage::class,
    ];

    protected array $prompts = [];
}
