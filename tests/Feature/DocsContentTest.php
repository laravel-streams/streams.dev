<?php

namespace Tests\Feature;

use Symfony\Component\Yaml\Yaml;
use Tests\TestCase;

/**
 * Lints every docs page in streams/data/*docs against the frontmatter
 * schema in STYLE.md. Agents copy these pages verbatim, so they must stay
 * machine-readable.
 */
class DocsContentTest extends TestCase
{
    protected const STREAMS = ['docs', 'core_docs', 'ui_docs', 'api_docs', 'sdk_docs', 'testing_docs', 'client_docs'];

    protected const SECTIONS = ['get-started', 'guides', 'concepts', 'reference', 'packages', 'contributing'];

    protected const PACKAGES = ['core', 'ui', 'api', 'sdk', 'testing', 'client', 'site', 'all'];

    protected const STATUSES = ['draft', 'review', 'ready', 'deprecated'];

    /**
     * @return array<string, array{frontmatter: array, body: string}>
     */
    protected function pages(): array
    {
        $pages = [];

        foreach (static::STREAMS as $stream) {
            foreach (glob(base_path("streams/data/{$stream}/*.md")) as $file) {
                $text = file_get_contents($file);

                $this->assertMatchesRegularExpression('/^---\n.*?\n---\n/s', $text, "{$file} has no frontmatter.");

                preg_match('/^---\n(.*?)\n---\n/s', $text, $match);

                $pages[$stream.'/'.basename($file, '.md')] = [
                    'frontmatter' => Yaml::parse($match[1]),
                    'body' => substr($text, strlen($match[0])),
                ];
            }
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

            if (str_starts_with($page, 'docs/')) {
                $this->assertContains($frontmatter['category'] ?? null, $categories, "{$page} category");
            } else {
                $this->assertArrayNotHasKey('category', $frontmatter, "{$page}: package pages have no category.");
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
}
