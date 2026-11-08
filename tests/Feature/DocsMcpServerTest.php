<?php

/**
 * Production verification log — 2026-11-07, against https://streams.dev/mcp
 * (Streamable HTTP, headers Content-Type: application/json and Accept: application/json, text/event-stream).
 *
 * initialize (protocolVersion 2025-06-18)         -> HTTP 200, echoes "2025-06-18"
 * initialize (protocolVersion 2025-03-26)         -> HTTP 200, echoes "2025-03-26"
 * tools/list                                      -> HTTP 200, 4 tools: search_docs, get_page, list_pages, get_schema
 * tools/call search_docs {query:"criteria"}       -> HTTP 200, 10 ranked results, first "core/criteria"
 * tools/call list_pages {}                        -> HTTP 200, 123 pages (url per page; no markdown_url emitted)
 * tools/call get_page {page:"core/introduction"}  -> HTTP 200, returns page markdown
 * tools/call get_schema {name:"streams"}          -> HTTP 200, returns streams JSON Schema
 * tools/call no_such_tool {} (bad input)          -> HTTP 400, JSON-RPC error {"code":-32602,"message":"Tool [no_such_tool] not found."}
 * Link check: all 123 list_pages url values -> 200; all 123 ".md" variants -> 200. broken_doc_links = 0.
 * npx -y @modelcontextprotocol/inspector --cli https://streams.dev/mcp --method tools/list -> exit_code 0.
 */

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

    public function test_section_filters_use_the_frontmatter_section()
    {
        StreamsDocsServer::tool(SearchDocs::class, ['query' => 'install', 'section' => 'get-started'])
            ->assertOk()
            ->assertSee(['"slug":"installation"', '"section":"get-started"'])
            ->assertDontSee('"section":"packages"');

        StreamsDocsServer::tool(SearchDocs::class, ['query' => 'install', 'section' => 'packages'])
            ->assertOk()
            ->assertSee('"slug":"core/installation"')
            ->assertDontSee('"slug":"installation"');

        StreamsDocsServer::tool(ListPages::class, ['section' => 'contributing'])
            ->assertOk()
            ->assertSee(['contributing-docs', '"section":"contributing"'])
            ->assertDontSee(['"section":"guides"', 'core/introduction']);

        StreamsDocsServer::tool(ListPages::class, ['package' => 'guides', 'section' => 'packages'])
            ->assertOk()
            ->assertSee('"count":0');

        StreamsDocsServer::tool(SearchDocs::class, ['query' => 'routes', 'section' => 'guide'])
            ->assertHasErrors(['section must be one of: get-started, guides, concepts, reference, packages, contributing']);

        StreamsDocsServer::tool(GetPage::class, ['page' => 'installation'])
            ->assertOk()
            ->assertSee('section: get-started');
    }

    public function test_get_page_returns_markdown_with_frontmatter()
    {
        foreach (['core/introduction', '/docs/core/introduction', 'https://streams.dev/docs/core/introduction.md'] as $reference) {
            StreamsDocsServer::tool(GetPage::class, ['page' => $reference])
                ->assertOk()
                ->assertSee(['---', 'slug: core/introduction', 'url: ', '/docs/core/introduction', 'package: core', 'section: packages', 'nav_title: Introduction', '# Core: Introduction']);
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

    public function test_http_endpoint_accepts_supported_protocol_versions()
    {
        foreach (['2025-03-26', '2025-06-18'] as $version) {
            $this->postJson('/mcp', [
                'jsonrpc' => '2.0',
                'id' => 1,
                'method' => 'initialize',
                'params' => [
                    'protocolVersion' => $version,
                    'capabilities' => new \stdClass,
                    'clientInfo' => ['name' => 'phpunit', 'version' => '1.0'],
                ],
            ], ['Accept' => 'application/json, text/event-stream', 'MCP-Protocol-Version' => $version])
                ->assertOk()
                ->assertJsonPath('result.serverInfo.name', 'Streams Docs')
                ->assertJsonPath('result.protocolVersion', $version);
        }
    }

    public function test_http_endpoint_rejects_unsupported_protocol_with_well_formed_error()
    {
        $this->postJson('/mcp', [
            'jsonrpc' => '2.0',
            'id' => 1,
            'method' => 'initialize',
            'params' => [
                'protocolVersion' => '2024-11-05',
                'capabilities' => new \stdClass,
                'clientInfo' => ['name' => 'phpunit', 'version' => '1.0'],
            ],
        ], ['Accept' => 'application/json, text/event-stream', 'MCP-Protocol-Version' => '2024-11-05'])
            ->assertStatus(400)
            ->assertJsonPath('error.code', -32022)
            ->assertJsonPath('error.data.supported', ['2025-11-25', '2025-06-18', '2025-03-26'])
            ->assertJsonPath('error.data.requested', '2024-11-05');
    }

    public function test_http_endpoint_returns_well_formed_errors_never_500()
    {
        $headers = ['Accept' => 'application/json, text/event-stream', 'MCP-Protocol-Version' => '2025-06-18'];

        // Unknown tool: a JSON-RPC error, never a 500.
        $this->postJson('/mcp', ['jsonrpc' => '2.0', 'id' => 1, 'method' => 'tools/call', 'params' => ['name' => 'nope', 'arguments' => []]], $headers)
            ->assertStatus(400)
            ->assertJsonPath('error.code', -32602)
            ->assertJsonPath('error.message', 'Tool [nope] not found.');

        // Malformed JSON: a parse error, never a 500.
        $this->call('POST', '/mcp', [], [], [], [
            'CONTENT_TYPE' => 'application/json',
            'HTTP_ACCEPT' => 'application/json, text/event-stream',
            'HTTP_MCP_PROTOCOL_VERSION' => '2025-06-18',
        ], '{not valid json')
            ->assertStatus(400)
            ->assertJsonPath('error.code', -32700);

        // Bad tool arguments come back as isError results, never a 500.
        $this->postJson('/mcp', ['jsonrpc' => '2.0', 'id' => 2, 'method' => 'tools/call', 'params' => ['name' => 'search_docs', 'arguments' => ['query' => 'x', 'package' => 'nope']]], $headers)
            ->assertOk()
            ->assertJsonPath('result.isError', true);

        $this->postJson('/mcp', ['jsonrpc' => '2.0', 'id' => 3, 'method' => 'tools/call', 'params' => ['name' => 'get_page', 'arguments' => ['page' => 'core/nope']]], $headers)
            ->assertOk()
            ->assertJsonPath('result.isError', true);

        $this->postJson('/mcp', ['jsonrpc' => '2.0', 'id' => 4, 'method' => 'tools/call', 'params' => ['name' => 'list_pages', 'arguments' => ['section' => 'nope']]], $headers)
            ->assertOk()
            ->assertJsonPath('result.isError', true);

        $this->postJson('/mcp', ['jsonrpc' => '2.0', 'id' => 5, 'method' => 'tools/call', 'params' => ['name' => 'get_schema', 'arguments' => ['name' => 'nope']]], $headers)
            ->assertOk()
            ->assertJsonPath('result.isError', true);
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
