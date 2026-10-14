<?php

namespace Tests\Feature;

use Symfony\Component\Yaml\Yaml;
use Tests\TestCase;

class DocsRegistryTest extends TestCase
{
    public function test_audited_rows_match_frontmatter_and_have_proof(): void
    {
        $rows = json_decode(file_get_contents(base_path('docs/registry.json')), true, flags: JSON_THROW_ON_ERROR);
        $this->assertNotEmpty($rows);
        foreach ($rows as $row) {
            preg_match('/^---\n(.*?)\n---\n/s', file_get_contents(base_path($row['page'])), $match);
            $frontmatter = Yaml::parse($match[1]);
            $this->assertSame($row['status'], $frontmatter['status'], $row['page']);
            $this->assertSame($row['package'], $frontmatter['package'], $row['page']);
            $this->assertContains($row['status'], ['implemented', 'partial', 'planned']);
            $this->assertNotEmpty($row['proof']);
            $this->assertNotEmpty($row['finding']);
        }
    }

    public function test_partial_page_is_published_with_a_visible_status(): void
    {
        $this->get('/docs/core/addons')->assertOk()
            ->assertSee('data-docs-status="partial"', false)
            ->assertSee('partially implemented');
    }

    public function test_implemented_page_is_not_labeled_partial(): void
    {
        $this->get('/docs/core/introduction')->assertOk()
            ->assertDontSee('data-docs-status="partial"', false)
            ->assertDontSee('data-docs-status="planned"', false);
    }

    public function test_planned_page_is_published_with_a_visible_status(): void
    {
        $this->get('/docs/core/authorization')->assertOk()
            ->assertSee('data-docs-status="planned"', false)
            ->assertSee('not implemented yet');
    }

    public function test_every_docs_page_is_in_the_registry(): void
    {
        $rows = json_decode(file_get_contents(base_path('docs/registry.json')), true, flags: JSON_THROW_ON_ERROR);
        $registered = array_column($rows, 'page');
        $files = glob(base_path('streams/data/docs/*.md')) ?: [];

        foreach (['core', 'ui', 'api', 'sdk', 'testing', 'client'] as $package) {
            $files = array_merge($files, glob(base_path('docs/packages/'.$package.'/*.md')) ?: []);
        }

        $this->assertNotEmpty($files);

        foreach ($files as $file) {
            $relative = ltrim(str_replace(base_path(), '', $file), '/');
            $this->assertContains($relative, $registered, $relative);
        }
    }
}
