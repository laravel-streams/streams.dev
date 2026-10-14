<?php

namespace Tests\Feature;

use DOMDocument;
use DOMXPath;
use Tests\TestCase;

class PlaygroundTest extends TestCase
{
    public function test_navigation_lists_every_catalog_component_exactly_once(): void
    {
        // Read the source independently: an incomplete loader must fail this test.
        $catalog = json_decode(file_get_contents(resource_path('playground/catalog.json')), true, 512, JSON_THROW_ON_ERROR);
        $components = $catalog['components'];
        $this->assertNotEmpty($components);

        $html = $this->get('/playground')->assertOk()->getContent();
        $document = new DOMDocument;
        @$document->loadHTML($html);
        $xpath = new DOMXPath($document);
        $links = $xpath->query('//nav[@aria-label="Playground components"]//a[@data-playground-component]');

        $this->assertCount(count($components), $links);

        foreach ($components as $component) {
            $matches = $xpath->query('//nav[@aria-label="Playground components"]//a[@data-playground-component="'.$component['id'].'"]');
            $this->assertSame(1, $matches->length, $component['id']);
            $this->assertSame($component['label'], trim($matches->item(0)->textContent));
            $this->assertSame(route('playground.component', $component['id']), $matches->item(0)->getAttribute('href'));
            $this->get($component['reference'])->assertOk();
        }
    }

    public function test_playground_uses_the_site_shell_and_theme(): void
    {
        $this->get('/playground')
            ->assertOk()
            ->assertViewIs('playground')
            ->assertSee('<title>UI Playground · '.config('app.name', 'Streams').'</title>', false)
            ->assertSee('window.StreamsTheme', false)
            ->assertSee('class="st-mesh"', false)
            ->assertSee('class="st-frame"', false)
            ->assertSee('aria-label="Playground components"', false)
            ->assertSee('UI Playground');
    }

    public function test_site_playground_links_open_in_a_new_tab(): void
    {
        $response = $this->get('/')->assertOk();
        $document = new DOMDocument;
        @$document->loadHTML($response->getContent());
        $links = (new DOMXPath($document))->query('//a[@href="'.route('playground').'"]');

        $this->assertNotFalse($links);
        $this->assertGreaterThan(0, $links->length);

        foreach ($links as $link) {
            $this->assertSame('_blank', $link->getAttribute('target'));
            $this->assertContains('noopener', explode(' ', $link->getAttribute('rel')));
        }
    }
}
