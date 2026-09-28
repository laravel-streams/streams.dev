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
        $this->assertStringContainsString('docs-nav-collapsed', $html);

        // Only the current page is marked active, even though every package has an "introduction".
        $this->assertSame(1, substr_count($html, 'docs-nav-link--sub is-active'));
    }
}
