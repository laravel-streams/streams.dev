<?php

namespace Tests\Feature;

use Tests\TestCase;

/**
 * The sidebar filter (resources/js/docs-filter.js) works on the rendered nav,
 * so the markup has to carry every package's pages, the filter field, and a
 * single active item.
 */
class DocsSidebarFilterTest extends TestCase
{
    public function test_sidebar_renders_the_inline_filter()
    {
        $this->get('/docs/core/introduction')
            ->assertOk()
            ->assertSee('data-docs-filter', false)
            ->assertSee('aria-controls="docs-nav-tree"', false)
            ->assertSee('data-docs-filter-empty', false)
            // ⌘K stays available from the sidebar as the full search.
            ->assertSee('data-docs-search-open', false);
    }

    public function test_sidebar_includes_every_packages_pages_for_filtering()
    {
        $html = $this->get('/docs/core/introduction')->assertOk()->getContent();

        // Other packages' pages are present (collapsed) so the filter can find them.
        $this->assertStringContainsString('href="/docs/api/routes"', $html);
        $this->assertStringContainsString('href="/docs/ui/forms"', $html);
        $this->assertStringContainsString('<p class="docs-nav-label">Builders</p>', $html);
        $this->assertStringContainsString('docs-nav-collapsed', $html);

        // Only the current page is marked active, even though every package has an "introduction".
        $this->assertSame(1, substr_count($html, 'docs-nav-link--sub is-active'));
    }

    public function test_sidebar_sections_are_get_started_packages_guides_then_contributing()
    {
        $html = $this->get('/docs/installation')->assertOk()->getContent();

        $positions = array_map(fn ($label) => strpos($html, "<span>{$label}</span>"), ['Get started', 'Guides', 'Contributing']);
        $positions[] = strpos($html, '<p class="docs-nav-label">Packages</p>');

        $this->assertNotContains(false, $positions);
        [$getStarted, $guides, $contributing, $packages] = $positions;

        $this->assertTrue($getStarted < $packages && $packages < $guides && $guides < $contributing);

        // Get started is expanded unless the reader collapsed it.
        $this->assertStringContainsString("getStartedOpen: localStorage.getItem('docs-nav-getstarted-open') !== '0'", $html);
        $this->assertStringContainsString('x-show="getStartedOpen">', $html);

        // Contributing lists the streams.dev pages, and old URLs still work.
        $this->assertGreaterThan($contributing, strpos($html, 'href="/docs/this-project"'));
        $this->get('/docs/this-project')->assertOk();
        $this->get('/docs/local-development')->assertOk();

        $this->assertSame(1, substr_count($html, 'docs-nav-link--sub is-active'));
    }
}
