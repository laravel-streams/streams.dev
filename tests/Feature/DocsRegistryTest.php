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

    public function test_landed_tap_page_is_no_longer_published_as_planned(): void
    {
        $this->get('/docs/sdk/tap')->assertOk()
            ->assertDontSee('data-docs-status="planned"', false)
            ->assertDontSee('not implemented yet')
            ->assertSee('Redirects are not followed');
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

    public function test_landed_package_handoffs_have_matching_registry_and_content(): void
    {
        $rows = json_decode(file_get_contents(base_path('docs/registry.json')), true, flags: JSON_THROW_ON_ERROR);
        $rows = array_column($rows, null, 'page');

        foreach ([
            'docs/packages/sdk/tall-components.md' => [261, 'streams-sdk@175e46bb:', 'It does not generate controllers or middleware'],
            'docs/packages/sdk/tap.md' => [261, 'streams-sdk@175e46bb:', 'any non-2xx response'],
            'docs/packages/sdk/streams.md' => [262, 'streams-sdk@82c161cc:', 'No top-level properties are required'],
            'docs/packages/testing/configuration.md' => [264, 'streams-core@7172c9ca4:', 'configured repository'],
            'streams/data/docs/localization.md' => [271, 'streams-ui@28d44328:', 'lang/vendor/ui'],
        ] as $page => [$task, $proofPrefix, $wording]) {
            $text = file_get_contents(base_path($page));
            preg_match('/^---\n(.*?)\n---\n/s', $text, $match);
            $frontmatter = Yaml::parse($match[1]);

            $this->assertSame('implemented', $frontmatter['status'], $page);
            $this->assertSame($task, $frontmatter['tracking_task'], $page);
            $this->assertSame('implemented', $rows[$page]['status'], $page);
            foreach ($rows[$page]['proof'] as $proof) {
                $this->assertStringStartsWith($proofPrefix, $proof, $page);
            }
            $this->assertStringContainsString($wording, $text, $page);
        }

        $tap = file_get_contents(base_path('docs/packages/sdk/tap.md'));
        $this->assertStringContainsString('its keys override matching keys already in the URL', $tap);
        $this->assertStringContainsString('a 3xx response fails', $tap);
        $this->assertStringContainsString('The tap must be an http or https URL.', $tap);

        $commands = file_get_contents(base_path('docs/packages/sdk/commands.md'));
        $this->assertStringContainsString('### streams:tap', $commands);
        $this->assertStringNotContainsString('Not implemented (empty handler)', $commands);

        $streams = file_get_contents(base_path('docs/packages/sdk/streams.md'));
        $this->assertStringContainsString('"id": "blog_posts"', $streams);
        $this->assertStringNotContainsString('"handle": "blog_posts"', $streams);
        $this->assertStringNotContainsString('"pagination":', $streams);
        $this->assertStringNotContainsString('"sort":', $streams);
        $this->assertStringContainsString('->paginate(20)', $streams);
        $this->assertStringContainsString("->orderBy('created_at', 'desc')", $streams);
    }

    public function test_unlanded_package_handoffs_remain_partial(): void
    {
        foreach ([
            'docs/packages/ui/livewire.md' => 259,
            'docs/packages/testing/test-data.md' => 263,
            'docs/packages/testing/troubleshooting.md' => 263,
            'docs/packages/testing/writing-tests.md' => 263,
            'docs/packages/client/client.md' => 266,
        ] as $page => $task) {
            preg_match('/^---\n(.*?)\n---\n/s', file_get_contents(base_path($page)), $match);
            $frontmatter = Yaml::parse($match[1]);
            $this->assertSame('partial', $frontmatter['status'], $page);
            $this->assertSame($task, $frontmatter['tracking_task'], $page);
        }
    }
}
