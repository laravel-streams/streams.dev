<?php

namespace Tests\Feature;

use DOMDocument;
use DOMXPath;
use Tests\TestCase;

class PlaygroundTest extends TestCase
{
    public function test_playground_uses_the_site_shell_and_theme(): void
    {
        $this->get('/playground')
            ->assertOk()
            ->assertViewIs('playground')
            ->assertSee('<title>UI Playground · Streams</title>', false)
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
