<?php

namespace Tests\Feature;

use App\Mcp\Resources\DocsIndex;
use App\Mcp\Resources\DocsPage;
use App\Mcp\Servers\StreamsDocsServer;
use App\Mcp\Tools\GetPage;
use App\Mcp\Tools\GetSchema;
use App\Mcp\Tools\ListPages;
use App\Mcp\Tools\SearchDocs;
use Illuminate\Support\Facades\RateLimiter;
use Tests\TestCase;

class DocsMcpServerTest extends TestCase
{
    public function test_search_docs_ranks_matching_pages()
    {
        StreamsDocsServer::tool(SearchDocs::class, ['query' => 'routes', 'package' => 'core', 'limit' => 5])
            ->assertOk()
            ->assertName('search_docs')
            ->assertSee(['"slug":"core/routes"', '/docs/core/routes', '"package":"core"'])
            ->assertStructuredContent(function ($content) {
                $content->where('query', 'routes')
                    ->where('results.0.slug', 'core/routes')
                    ->etc();
            });
    }

    public function test_search_docs_is_accent_insensitive_and_validates_input()
    {
        StreamsDocsServer::tool(SearchDocs::class, ['query' => 'RÔUTES', 'package' => 'core'])
            ->assertOk()
            ->assertSee('core/routes');

        StreamsDocsServer::tool(SearchDocs::class, ['query' => 'routes', 'package' => 'nope'])
            ->assertHasErrors(['package must be one of']);

        StreamsDocsServer::tool(SearchDocs::class, [])
            ->assertHasErrors(['Pass a search query']);
    }

    public function test_get_page_returns_markdown_with_frontmatter()
    {
        foreach (['core/introduction', '/docs/core/introduction', 'https://streams.dev/docs/core/introduction.md'] as $reference) {
            StreamsDocsServer::tool(GetPage::class, ['page' => $reference])
                ->assertOk()
                ->assertSee(['---', 'slug: core/introduction', 'url: ', '/docs/core/introduction', 'package: core', 'nav_title: Introduction', '# Core: Introduction']);
        }

        StreamsDocsServer::tool(GetPage::class, ['page' => 'installation'])
            ->assertOk()
            ->assertSee('slug: installation');
    }

    public function test_get_page_explains_ambiguous_and_missing_pages()
    {
        StreamsDocsServer::tool(GetPage::class, ['page' => 'routes'])
            ->assertHasErrors(['core/routes', 'api/routes']);

        StreamsDocsServer::tool(GetPage::class, ['page' => 'core/no-such-page'])
            ->assertHasErrors(['No docs page matches']);
    }

    public function test_list_pages_returns_the_navigation()
    {
        StreamsDocsServer::tool(ListPages::class, ['package' => 'core'])
            ->assertOk()
            ->assertSee(['Core reference', 'core/introduction', '/docs/core/introduction'])
            ->assertDontSee('ui/forms');

        StreamsDocsServer::tool(ListPages::class)
            ->assertOk()
            ->assertSee(['Guides:', 'ui/forms', 'api/routes']);
    }

    public function test_get_schema_returns_the_published_schema()
    {
        $published = json_decode(file_get_contents(public_path('schema/streams.schema.json')), true);

        StreamsDocsServer::tool(GetSchema::class)
            ->assertOk()
            ->assertSee($published['$id']);

        StreamsDocsServer::tool(GetSchema::class, ['name' => 'nope'])
            ->assertHasErrors();
    }

    public function test_tools_are_listed_as_read_only()
    {
        StreamsDocsServer::tools()
            ->assertRegistered([SearchDocs::class, GetPage::class, ListPages::class, GetSchema::class]);

        foreach ([SearchDocs::class, GetPage::class, ListPages::class, GetSchema::class] as $tool) {
            $annotations = app($tool)->toArray()['annotations'];

            $this->assertTrue($annotations['readOnlyHint']);
            $this->assertTrue($annotations['idempotentHint']);
            $this->assertFalse($annotations['openWorldHint']);
        }
    }

    public function test_docs_pages_are_resources()
    {
        StreamsDocsServer::resource(DocsPage::class, ['package' => 'core', 'page' => 'introduction'])
            ->assertOk()
            ->assertSee('slug: core/introduction');

        StreamsDocsServer::resource(DocsIndex::class)
            ->assertOk()
            ->assertSee('# Streams');
    }

    public function test_http_endpoint_initializes_and_lists_tools()
    {
        $headers = ['Accept' => 'application/json, text/event-stream', 'MCP-Protocol-Version' => '2025-06-18'];

        $this->postJson('/mcp', [
            'jsonrpc' => '2.0',
            'id' => 1,
            'method' => 'initialize',
            'params' => [
                'protocolVersion' => '2025-06-18',
                'capabilities' => new \stdClass,
                'clientInfo' => ['name' => 'phpunit', 'version' => '1.0'],
            ],
        ], $headers)
            ->assertOk()
            ->assertJsonPath('result.serverInfo.name', 'Streams Docs')
            ->assertJsonPath('result.serverInfo.version', '1.0.0')
            ->assertJsonPath('result.protocolVersion', '2025-06-18');

        $response = $this->postJson('/mcp', ['jsonrpc' => '2.0', 'id' => 2, 'method' => 'tools/list'], $headers)
            ->assertOk();

        $this->assertEqualsCanonicalizing(
            ['search_docs', 'get_page', 'list_pages', 'get_schema'],
            array_column($response->json('result.tools'), 'name')
        );

        $page = $this->postJson('/mcp', [
            'jsonrpc' => '2.0',
            'id' => 3,
            'method' => 'tools/call',
            'params' => ['name' => 'get_page', 'arguments' => ['page' => 'core/introduction']],
        ], $headers)
            ->assertOk()
            ->assertJsonPath('result.isError', false)
            ->assertJsonPath('result.content.0.type', 'text');

        $this->assertStringContainsString('slug: core/introduction', $page->json('result.content.0.text'));

        // No CSRF token or session is needed, and GET is not a transport.
        $this->get('/mcp')->assertStatus(405);
    }

    public function test_http_endpoint_is_rate_limited_per_client_ip()
    {
        RateLimiter::clear('mcp');
        $ping = ['jsonrpc' => '2.0', 'id' => 1, 'method' => 'ping'];

        for ($i = 0; $i < 60; $i++) {
            $this->withServerVariables(['REMOTE_ADDR' => '203.0.113.7'])
                ->postJson('/mcp', $ping)
                ->assertOk();
        }

        $this->withServerVariables(['REMOTE_ADDR' => '203.0.113.7'])
            ->postJson('/mcp', $ping)
            ->assertStatus(429)
            ->assertHeader('Retry-After')
            ->assertJsonPath('error.code', -32000);

        $this->withServerVariables(['REMOTE_ADDR' => '203.0.113.8'])
            ->postJson('/mcp', $ping)
            ->assertOk();
    }
}
