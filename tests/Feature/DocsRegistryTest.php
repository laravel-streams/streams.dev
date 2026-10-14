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
        $this->get('/docs/sdk/tap')->assertOk()
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

    public function test_core_feature_reconciliation_matches_task_255_snapshot(): void
    {
        $rows = json_decode(file_get_contents(base_path('docs/registry.json')), true, flags: JSON_THROW_ON_ERROR);
        $rows = array_column($rows, null, 'page');
        $commit = 'streams-core@fa8dffbff61570b789893c3d20c86b9a88476813:';

        foreach ([
            'docs/packages/core/authorization.md',
            'docs/packages/core/field-decorators.md',
            'docs/packages/core/images.md',
            'streams/data/docs/images.md',
            'streams/data/docs/streams.md',
        ] as $page) {
            $this->assertSame('implemented', $rows[$page]['status'], $page);
            foreach ($rows[$page]['proof'] as $proof) {
                $this->assertStringStartsWith($commit, $proof, $page);
            }

            preg_match('/^---\n(.*?)\n---\n/s', file_get_contents(base_path($page)), $match);
            $this->assertSame(255, Yaml::parse($match[1])['tracking_task'], $page);
        }

        $authorization = file_get_contents(base_path('docs/packages/core/authorization.md'));
        $this->assertStringContainsString('Streams\\Core\\Stream\\StreamPolicy', $authorization);
        $this->assertStringContainsString('Streams can share an entry class and still use different policy classes.', $authorization);
        $this->assertStringContainsString('class-only checks are denied', $authorization);
        $this->assertStringNotContainsString('Streams that share one entry class share one policy.', $authorization);

        $streams = file_get_contents(base_path('streams/data/docs/streams.md'));
        $this->assertStringContainsString('StreamPolicy', $streams);
        $this->assertStringContainsString('class-only checks are denied', $streams);

        foreach (['docs/packages/core/images.md', 'streams/data/docs/images.md'] as $page) {
            $images = file_get_contents(base_path($page));
            $this->assertStringContainsString('InvalidArgumentException', $images, $page);
            $this->assertStringContainsString('before any candidate is generated', $images, $page);
            $this->assertStringContainsString('does not add a `sizes` attribute', $images, $page);
            $this->assertStringContainsString('including its alterations', $images, $page);
        }
    }
}
