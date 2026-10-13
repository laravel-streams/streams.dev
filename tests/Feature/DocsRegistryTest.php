<?php

namespace Tests\Feature;

use App\Support\DocsRegistry;
use Illuminate\Support\Facades\Cache;
use PHPUnit\Framework\Attributes\DataProvider;
use Symfony\Component\Yaml\Yaml;
use Tests\TestCase;

class DocsRegistryTest extends TestCase
{
    public static function statuses(): array
    {
        return [['planned', 'Planned'], ['partial', 'Partial'], ['implemented', 'Implemented']];
    }

    #[DataProvider('statuses')]
    public function test_status_badges_render_on_articles_navigation_listings_and_search(string $status, string $label): void
    {
        $file = base_path('docs/packages/core/introduction.md');
        $original = file_get_contents($file);
        $updated = preg_replace('/^(status|tracking_task):.*\n/m', '', $original);
        file_put_contents($file, preg_replace('/\A---\n/', "---\nstatus: {$status}\ntracking_task: 8537\n", $updated));

        try {
            Cache::flush();
            $html = $this->get('/docs/core/introduction')->assertOk()->getContent();
            $badge = 'data-docs-status="'.$status.'"';

            // The article and both the package landing and nested page links carry the badge.
            $this->assertGreaterThanOrEqual(3, substr_count($html, $badge));
            $this->assertStringContainsString('aria-label="Feature status: '.$label.'"', $html);
            $this->assertStringContainsString('data-docs-nav-title', $html);

            $hub = $this->get('/docs')->assertOk()->getContent();
            $this->assertMatchesRegularExpression('~<a href="/docs/core/introduction" class="docs-pill">.*?data-docs-status="'.$status.'".*?</a>~s', $hub);

            $items = $this->getJson('/search/docs.json')->assertOk()->json();
            $core = collect($items)->first(fn ($item) => str_ends_with($item['url'], '/docs/core/introduction'));
            $this->assertSame($status, $core['status']);
        } finally {
            file_put_contents($file, $original);
            Cache::flush();
        }
    }

    public static function invalidStatuses(): array
    {
        return [['ready'], ['unknown'], [null], [true], [8537], [['implemented']]];
    }

    #[DataProvider('invalidStatuses')]
    public function test_invalid_or_editorial_status_is_never_rendered_as_implemented(mixed $status): void
    {
        $html = (string) $this->blade('<x-docs-status :status="$status" />', ['status' => $status]);

        $this->assertStringNotContainsString('Implemented', $html);
        $this->assertStringNotContainsString('data-docs-status', $html);
    }

    public function test_every_docs_stream_declares_the_registry_schema(): void
    {
        foreach (['docs', 'core_docs', 'ui_docs', 'api_docs', 'sdk_docs', 'testing_docs', 'client_docs'] as $stream) {
            $fields = array_column(json_decode(file_get_contents(base_path('streams/'.$stream.'.json')), true)['fields'], null, 'handle');

            $this->assertTrue($fields['status']['required']);
            $this->assertSame(DocsRegistry::STATUSES, $fields['status']['config']['options']);
            $this->assertSame(DocsRegistry::PACKAGES, array_keys($fields['package']['config']['options']));
            $this->assertSame(DocsRegistry::EDITORIAL_STATUSES, array_keys($fields['editorial_status']['config']['options']));
            $this->assertTrue($fields['editorial_status']['required']);
            $this->assertSame('integer', $fields['tracking_task']['type']);
            $this->assertArrayNotHasKey('default', $fields['status']['config']);
        }
    }

    public static function invalidFrontmatter(): array
    {
        $valid = ['status' => 'planned', 'package' => 'core', 'editorial_status' => 'ready'];

        return [
            'missing status' => [array_diff_key($valid, ['status' => true]), 'status'],
            'editorial status in registry' => [array_replace($valid, ['status' => 'ready']), 'status'],
            'missing package' => [array_diff_key($valid, ['package' => true]), 'package'],
            'invalid package' => [array_replace($valid, ['package' => 'unknown']), 'package'],
            'missing editorial status' => [array_diff_key($valid, ['editorial_status' => true]), 'editorial_status'],
            'invalid editorial status' => [array_replace($valid, ['editorial_status' => 'implemented']), 'editorial_status'],
            'task title' => [$valid + ['tracking_task' => 'build forms'], 'tracking_task'],
            'task float' => [$valid + ['tracking_task' => 85.37], 'tracking_task'],
            'task boolean' => [$valid + ['tracking_task' => true], 'tracking_task'],
            'task null' => [$valid + ['tracking_task' => null], 'tracking_task'],
            'task zero' => [$valid + ['tracking_task' => 0], 'tracking_task'],
            'partial needs task' => [array_replace($valid, ['status' => 'partial']), 'tracking_task'],
            'implemented needs task' => [array_replace($valid, ['status' => 'implemented']), 'tracking_task'],
        ];
    }

    #[DataProvider('invalidFrontmatter')]
    public function test_invalid_registry_frontmatter_fails_the_command(array $frontmatter, string $field): void
    {
        $file = tempnam(sys_get_temp_dir(), 'docs-registry-');
        file_put_contents($file, "---\n".Yaml::dump($frontmatter)."---\nFixture body.\n");
        $this->mock(DocsRegistry::class)->shouldReceive('files')->once()->andReturn([$file]);

        try {
            $this->artisan('docs:validate-registry')
                ->expectsOutputToContain($field)
                ->assertFailed();
        } finally {
            unlink($file);
        }
    }

    public function test_valid_registry_frontmatter_passes_the_command(): void
    {
        $file = tempnam(sys_get_temp_dir(), 'docs-registry-');
        $this->mock(DocsRegistry::class)->shouldReceive('files')->times(3)->andReturn([$file]);

        try {
            foreach (array_keys(DocsRegistry::STATUSES) as $status) {
                $task = $status === 'planned' ? '' : "tracking_task: '8537'\n";
                file_put_contents($file, "---\nstatus: {$status}\npackage: core\neditorial_status: ready\n{$task}---\nBody.\n");
                $this->artisan('docs:validate-registry')->expectsOutput('Validated 1 documentation registry entries.')->assertOk();
            }
        } finally {
            unlink($file);
        }
    }
}
