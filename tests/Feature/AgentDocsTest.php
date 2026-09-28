<?php

namespace Tests\Feature;

use App\Support\DocsSearchIndex;
use Tests\TestCase;

class AgentDocsTest extends TestCase
{
    public function test_llms_txt_lists_every_docs_page_as_markdown()
    {
        $response = $this->get('/llms.txt')->assertOk();

        $this->assertStringStartsWith('text/plain', $response->headers->get('Content-Type'));

        $body = $response->getContent();

        $this->assertStringStartsWith("# Streams\n\n> ", $body);

        foreach (DocsSearchIndex::documents() as $document) {
            $this->assertStringContainsString('('.$document['markdown'].')', $body);
        }
    }

    public function test_llms_full_txt_inlines_pages()
    {
        $this->get('/llms-full.txt')
            ->assertOk()
            ->assertSee('# API reference: Authentication', false)
            ->assertSee('Source: '.url('/docs/core/criteria'), false);
    }

    public function test_raw_markdown_routes()
    {
        $this->get('/docs/installation.md')
            ->assertOk()
            ->assertHeader('Content-Type', 'text/markdown; charset=UTF-8')
            ->assertSee("# Installation\n", false);

        $this->get('/docs/core/streams.md')->assertOk();
        $this->get('/docs/client/introduction.md')->assertOk();

        $this->get('/docs/nope.md')->assertNotFound();
        $this->get('/docs/core/nope.md')->assertNotFound();
    }

    public function test_html_docs_routes_still_render()
    {
        $this->get('/docs/installation')->assertOk();
        $this->get('/docs/core/streams')->assertOk();
    }

    public function test_openapi_reference_is_served()
    {
        $this->get('/docs/api/openapi.yaml')
            ->assertOk()
            ->assertSee('openapi: 3.0.3', false);
    }

    public function test_every_package_section_has_a_landing_page()
    {
        foreach (DocsSearchIndex::packages() as $package) {
            $this->get("/docs/{$package}/introduction")->assertOk();
        }
    }

    public function test_agent_platform_pages_are_linked_from_the_index()
    {
        foreach ([
            '/docs/agents',
            '/docs/mcp',
            '/docs/workflows',
            '/docs/tenancy',
            '/docs/ui/theming',
            '/docs/sdk/commands',
        ] as $url) {
            $this->get($url)->assertOk();
            $this->get($url.'.md')->assertOk()->assertHeader('Content-Type', 'text/markdown; charset=UTF-8');
        }

        $index = $this->get('/llms.txt')->assertOk()->getContent();

        $this->assertStringContainsString(']('.url('/docs/mcp.md').')', $index);
        $this->assertStringContainsString(']('.url('/docs/sdk/commands.md').')', $index);
        $this->assertStringContainsString('not shipped', strtolower($this->get('/docs/mcp')->getContent()));
    }

    public function test_sdk_introduction_only_lists_real_commands()
    {
        $this->get('/docs/sdk/introduction')
            ->assertOk()
            ->assertSee('There is no <code>streams:component</code> or <code>streams:crud</code> command.', false)
            ->assertDontSee('php artisan streams:component', false)
            ->assertDontSee('php artisan streams:crud', false)
            ->assertSee('make:stream', false)
            ->assertDontSee('01-streams.md', false);
    }
}
