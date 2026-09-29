<?php

namespace Tests\Feature;

use App\Support\DocsNav;
use Illuminate\Support\Facades\App;
use Streams\Core\Support\Facades\Streams;
use Symfony\Component\Yaml\Yaml;
use Tests\TestCase;

/**
 * Lints hub guides and package reference against the frontmatter schema
 * in STYLE.md. Agents copy these pages verbatim, so they must stay
 * machine-readable.
 */
class DocsContentTest extends TestCase
{
    protected const SECTIONS = ['get-started', 'guides', 'concepts', 'reference', 'packages', 'contributing'];

    protected const PACKAGES = ['core', 'ui', 'api', 'sdk', 'testing', 'client', 'site', 'all'];

    protected const STATUSES = ['draft', 'review', 'ready', 'deprecated'];

    /**
     * @return array<string, array{frontmatter: array, body: string}>
     */
    protected function pages(): array
    {
        $pages = [];
        $files = glob(base_path('streams/data/docs/*.md')) ?: [];

        foreach (DocsNav::packages() as $package => $ignored) {
            $files = array_merge($files, glob(DocsNav::directory($package).'/*.md') ?: []);
        }

        foreach ($files as $file) {
            $text = file_get_contents($file);

            $this->assertMatchesRegularExpression('/^---\n.*?\n---\n/s', $text, "{$file} has no frontmatter.");

            preg_match('/^---\n(.*?)\n---\n/s', $text, $match);

            $relative = ltrim(str_replace(base_path(), '', $file), '/');

            $pages[substr($relative, 0, -3)] = [
                'frontmatter' => Yaml::parse($match[1]),
                'body' => substr($text, strlen($match[0])),
            ];
        }

        $this->assertNotEmpty($pages);

        return $pages;
    }

    public function test_frontmatter_matches_the_schema()
    {
        $categories = array_keys(json_decode(file_get_contents(base_path('streams/docs_categories.json')), true)['data']);

        foreach ($this->pages() as $page => ['frontmatter' => $frontmatter]) {
            foreach (['title', 'nav_title', 'description', 'section', 'package', 'order', 'tags', 'status'] as $key) {
                $this->assertArrayHasKey($key, $frontmatter, "{$page} is missing \"{$key}\".");
            }

            $this->assertIsString($frontmatter['title'], $page);
            $this->assertNotSame('', trim($frontmatter['description']), "{$page} has an empty description.");
            $this->assertContains($frontmatter['section'], static::SECTIONS, "{$page} section");
            $this->assertContains($frontmatter['package'], static::PACKAGES, "{$page} package");
            $this->assertContains($frontmatter['status'], static::STATUSES, "{$page} status");
            $this->assertIsInt($frontmatter['order'], "{$page} order");
            $this->assertIsArray($frontmatter['tags'], "{$page} tags");
            $this->assertArrayNotHasKey('sort_order', $frontmatter, "{$page} uses sort_order; use order.");

            if (str_starts_with($page, 'streams/data/docs/')) {
                $this->assertContains($frontmatter['category'] ?? null, $categories, "{$page} category");
            } else {
                $this->assertArrayNotHasKey('category', $frontmatter, "{$page}: package pages have no category.");
                $this->assertArrayNotHasKey('group', $frontmatter, "{$page}: package headings live in docs/nav.json.");
                $this->assertSame('packages', $frontmatter['section'], "{$page} section");
            }
        }
    }

    public function test_titles_are_unique()
    {
        $titles = array_map(fn ($page) => $page['frontmatter']['title'], $this->pages());

        $duplicates = array_keys(array_filter(array_count_values($titles), fn ($count) => $count > 1));

        $this->assertSame([], $duplicates, 'Duplicate titles: '.implode(', ', $duplicates));
    }

    public function test_bodies_have_no_h1_fenced_code_has_a_language_and_links_are_root_relative()
    {
        foreach ($this->pages() as $page => ['body' => $body]) {
            $inFence = false;

            foreach (explode("\n", $body) as $number => $line) {
                $where = "{$page} body line ".($number + 1);

                if (preg_match('/^\s*(```|~~~)(.*)$/', $line, $fence)) {
                    if (! $inFence) {
                        $this->assertNotSame('', trim($fence[2]), "{$where}: give the code fence a language (text for plain output).");
                    }

                    $inFence = ! $inFence;

                    continue;
                }

                if ($inFence) {
                    continue;
                }

                $this->assertDoesNotMatchRegularExpression('/^# /', $line, "{$where}: the layout renders the title as the H1.");

                preg_match_all('/\]\(([^)\s]+)/', $line, $links);

                foreach ($links[1] as $url) {
                    $this->assertMatchesRegularExpression('~^(https?:|/|#|mailto:)~', $url, "{$where}: use a root-relative link, not [{$url}].");
                }
            }

            $this->assertFalse($inFence, "{$page} has an unclosed code fence.");
        }
    }

    public function test_every_json_block_parses_and_uses_registered_field_types()
    {
        $count = 0;

        foreach ($this->pages() as $page => ['body' => $body]) {
            preg_match_all('/^```json[^\n]*\n(.*?)^```/ms', $body, $blocks);

            foreach ($blocks[1] as $index => $json) {
                $count++;
                $where = "{$page} json block ".($index + 1);

                $decoded = json_decode($json, true);

                $this->assertSame(JSON_ERROR_NONE, json_last_error(), "{$where}: ".json_last_error_msg()."\n{$json}");

                foreach ($this->fieldTypes($decoded) as $type) {
                    $this->assertTrue(App::has("streams.core.field_type.{$type}"), "{$where}: field type [{$type}] is not registered by Core.");
                }
            }
        }

        $this->assertGreaterThan(100, $count);
    }

    /**
     * Field types used in any "fields" definition inside a decoded block. A
     * list of strings is output (streams:list), not a definition, so it is skipped.
     */
    protected function fieldTypes(mixed $value): array
    {
        if (! is_array($value)) {
            return [];
        }

        $types = [];

        foreach ($value as $key => $child) {
            if ($key === 'fields' && is_array($child)) {
                foreach ($child as $field) {
                    $type = is_array($field) ? ($field['type'] ?? null) : (array_is_list($child) ? null : $field);

                    if (is_string($type) && ! str_starts_with($type, '@')) {
                        $types[] = $type;
                    }
                }
            }

            $types = array_merge($types, $this->fieldTypes($child));
        }

        return $types;
    }

    public function test_package_nav_points_at_package_files()
    {
        foreach (DocsNav::packages() as $name => $package) {
            $this->assertSame(
                'docs/packages/'.$name,
                Streams::make($package['stream'])->config('source.path'),
                "{$name} stream does not point at its package folder."
            );

            $listed = DocsNav::listed($name);

            $this->assertSame($listed, array_values(array_unique($listed)), "{$name} lists a page more than once.");

            foreach ($listed as $id) {
                $this->assertFileExists(DocsNav::directory($name).'/'.$id.'.md', "{$name} nav points at missing {$id}.md.");
            }

            $this->assertEqualsCanonicalizing(
                DocsNav::files($name),
                array_merge($listed, DocsNav::unlisted($name)),
                "{$name} nav does not account for every markdown file."
            );
        }
    }
}
