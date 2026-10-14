<?php

namespace Tests\Feature;

use DOMDocument;
use DOMXPath;
use Tests\TestCase;

class UiPlaygroundDocsTest extends TestCase
{
    public function test_playground_guide_is_available_and_marks_unbuilt_workflows_as_planned(): void
    {
        $html = $this->get('/docs/ui-playground')
            ->assertOk()
            ->assertSee('UI Playground')
            ->assertSee('not available yet')
            ->getContent();

        $xpath = $this->xpath($html);

        foreach (['Test an example'] as $heading) {
            $badges = $xpath->query(
                '//article//h2[normalize-space(.)="'.$heading.'"]/following-sibling::*[1]//span[@class="st-pill" and normalize-space(.)="Planned"]'
            );

            $this->assertSame(1, $badges->length, $heading.' must carry a visible Planned badge.');
        }

        $shareBadge = $xpath->query(
            '//article//h2[normalize-space(.)="Share links"]/following-sibling::*[1]//span[@class="st-pill" and normalize-space(.)="Planned"]'
        );
        $this->assertSame(0, $shareBadge->length);
        $this->assertStringContainsString('settings only', $html);
        $this->assertStringContainsString('class name', $html);

        $editorBadge = $xpath->query(
            '//article//h2[normalize-space(.)="Edit JSON"]/following-sibling::*[1]//span[@class="st-pill" and normalize-space(.)="Planned"]'
        );
        $this->assertSame(0, $editorBadge->length);
        $this->assertStringContainsString('/schema/catalog.schema.json', $html);
        $this->assertStringContainsString('Unknown keys are rejected.', $html);

        $links = $xpath->query('//article//a[@href="/playground"]');
        $this->assertGreaterThan(0, $links->length);
        $this->assertStringNotContainsString('There is no working Playground URL', $html);
    }

    public function test_docs_navigation_links_to_the_playground_guide_under_frontend(): void
    {
        $html = $this->get('/docs/installation')->assertOk()->getContent();
        $links = $this->xpath($html)->query(
            '//nav//details[summary/span[normalize-space(.)="Frontend"]]//a[@href="/docs/ui-playground" and normalize-space(.)="UI Playground"]'
        );

        $this->assertSame(1, $links->length);
    }

    private function xpath(string $html): DOMXPath
    {
        $document = new DOMDocument;
        $previous = libxml_use_internal_errors(true);

        try {
            $document->loadHTML($html);
        } finally {
            libxml_clear_errors();
            libxml_use_internal_errors($previous);
        }

        return new DOMXPath($document);
    }
}
