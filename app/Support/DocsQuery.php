<?php

namespace App\Support;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use Symfony\Component\Yaml\Yaml;

/**
 * Read-only queries over the docs for the Streams Docs MCP server
 * (app/Mcp). Built on DocsSearchIndex::documents() and LlmsText, the same
 * sources behind /search/docs.json, /llms.txt and /docs/{page}.md.
 *
 * A page's "slug" is its path under /docs/: "installation" for a hub
 * guide, "core/introduction" for a package reference page.
 */
class DocsQuery
{
    /**
     * Package filters accepted by the MCP tools.
     */
    public const PACKAGES = ['guides', 'core', 'ui', 'api', 'sdk', 'testing', 'client'];

    public const SECTIONS = ['guide', 'reference'];

    /**
     * Every docs page with its slug, cached like the other docs indexes.
     */
    public static function documents(): array
    {
        return Cache::remember('docs.mcp.documents', now()->addMinutes(15), function () {
            return array_map(fn (array $document) => $document + [
                'slug' => static::slugFor($document),
            ], DocsSearchIndex::documents());
        });
    }

    /**
     * Rank pages against a free-text query. Every term must appear in the
     * title, description, slug or body; titles weigh most. Matching is
     * case- and accent-insensitive.
     */
    public static function search(string $query, ?string $package = null, ?string $section = null, int $limit = 10): array
    {
        $terms = static::terms($query);

        if ($terms === []) {
            return [];
        }

        $phrase = static::normalize($query);
        $results = [];

        foreach (static::filter(static::documents(), $package, $section) as $document) {
            $title = static::normalize($document['title'].' '.($document['nav_title'] ?? ''));
            $tags = static::normalize(implode(' ', $document['tags'] ?? []));
            $description = static::normalize($document['description']);
            $slug = static::normalize($document['slug']);
            $body = static::normalize($document['body']);

            $score = 0;

            foreach ($terms as $term) {
                $hit = 0;
                $hit += str_contains($title, $term) ? 10 : 0;
                $hit += str_contains($slug, $term) ? 6 : 0;
                $hit += str_contains($tags, $term) ? 5 : 0;
                $hit += str_contains($description, $term) ? 4 : 0;
                $hit += min(substr_count($body, $term), 5);

                if ($hit === 0) {
                    continue 2;
                }

                $score += $hit;
            }

            if (count($terms) > 1 && str_contains($title.' '.$description.' '.$body, $phrase)) {
                $score += 8;
            }

            if (in_array($phrase, [static::normalize($document['title']), static::normalize($document['nav_title'] ?? '')], true)) {
                $score += 20;
            }

            $results[] = [
                'title' => $document['title'],
                'slug' => $document['slug'],
                'url' => $document['url'],
                'markdown_url' => $document['markdown'],
                'package' => $document['package'],
                'section' => $document['section'],
                'description' => $document['description'],
                'tags' => $document['tags'] ?? [],
                'snippet' => static::snippet($document['body'], $terms),
                'score' => $score,
            ];
        }

        usort($results, fn ($a, $b) => [$b['score'], $a['title']] <=> [$a['score'], $b['title']]);

        return array_slice($results, 0, max(1, $limit));
    }

    /**
     * Resolve a slug, docs path or URL ("core/introduction",
     * "/docs/core/introduction", "https://streams.dev/docs/core/introduction.md")
     * to a page. Returns [page|null, candidates] so callers can explain an
     * ambiguous bare id.
     */
    public static function resolve(string $reference): array
    {
        $path = trim($reference);
        $path = preg_replace('#^[a-z][a-z0-9+.-]*://[^/]+#i', '', $path);
        $path = preg_replace('/[?#].*$/', '', $path);
        $path = preg_replace('/\.md$/', '', $path);
        $path = trim($path, '/');
        $path = preg_replace('#^docs(/|$)#', '', $path);

        if ($path === '') {
            return [null, []];
        }

        $documents = static::documents();

        foreach ($documents as $document) {
            if ($document['slug'] === $path) {
                return [$document, []];
            }
        }

        if (! str_contains($path, '/')) {
            $matches = array_values(array_filter($documents, fn ($document) => $document['id'] === $path));

            if (count($matches) === 1) {
                return [$matches[0], []];
            }

            return [null, array_column($matches, 'slug')];
        }

        return [null, []];
    }

    /**
     * One page as markdown with a YAML frontmatter block (title, nav
     * title, description, URLs, package, tags).
     */
    public static function markdown(array $document): string
    {
        $frontmatter = Yaml::dump(array_filter([
            'title' => $document['title'],
            'nav_title' => $document['nav_title'] ?? '',
            'description' => $document['description'],
            'slug' => $document['slug'],
            'url' => $document['url'],
            'markdown_url' => $document['markdown'],
            'package' => $document['package'],
            'section' => $document['section'],
            'tags' => $document['tags'] ?? [],
        ], fn ($value) => $value !== '' && $value !== []), 1);

        return "---\n".$frontmatter."---\n\n# ".$document['title']."\n\n".trim($document['body'])."\n";
    }

    /**
     * The docs navigation: hub guides grouped by category, then one group
     * per package, in the order /llms.txt uses.
     */
    public static function navigation(?string $package = null, ?string $section = null): array
    {
        $slugs = array_column(static::documents(), 'slug', 'url');
        $groups = [];

        foreach (LlmsText::sections() as $label => $documents) {
            $documents = static::filter($documents, $package, $section);

            if ($documents === []) {
                continue;
            }

            $groups[] = [
                'group' => $label,
                'package' => $documents[0]['package'],
                'section' => $documents[0]['section'],
                'pages' => array_map(fn ($document) => [
                    'title' => $document['title'],
                    'nav_title' => $document['nav_title'] ?? $document['title'],
                    'slug' => $slugs[$document['url']] ?? static::slugFor($document),
                    'url' => $document['url'],
                ], $documents),
            ];
        }

        return $groups;
    }

    /**
     * JSON Schemas published under public/schema, keyed by short name
     * ("streams" for streams.schema.json).
     */
    public static function schemas(): array
    {
        $schemas = [];

        foreach (glob(public_path('schema/*.schema.json')) ?: [] as $path) {
            $schemas[basename($path, '.schema.json')] = $path;
        }

        ksort($schemas);

        return $schemas;
    }

    protected static function filter(array $documents, ?string $package, ?string $section): array
    {
        return array_values(array_filter($documents, fn ($document) => (! $package || $document['package'] === $package)
            && (! $section || $document['section'] === $section)));
    }

    protected static function slugFor(array $document): string
    {
        return $document['package'] === 'guides'
            ? $document['id']
            : $document['package'].'/'.$document['id'];
    }

    protected static function normalize(string $text): string
    {
        return Str::lower(Str::ascii($text));
    }

    protected static function terms(string $query): array
    {
        $terms = preg_split('/[^a-z0-9_.:\-\/@]+/', static::normalize($query), -1, PREG_SPLIT_NO_EMPTY);

        return array_values(array_unique(array_filter($terms, fn ($term) => strlen($term) > 1 || ctype_digit($term))));
    }

    protected static function snippet(string $body, array $terms, int $radius = 90): string
    {
        $text = preg_replace('/```[\s\S]*?```/', ' ', $body);
        $text = strip_tags($text);
        $text = preg_replace('/[#*`>\[\]|]/', '', $text);
        $text = preg_replace('/\(([^)\s]*)\)/', '', $text);
        $text = trim(preg_replace('/\s+/', ' ', $text));

        $lower = static::normalize($text);
        $position = null;

        foreach ($terms as $term) {
            $found = strpos($lower, $term);

            if ($found !== false && ($position === null || $found < $position)) {
                $position = $found;
            }
        }

        // Str::ascii can change lengths, so positions map onto the ASCII copy.
        $source = Str::ascii($text);

        if ($position === null) {
            return Str::limit($source, $radius * 2);
        }

        $start = max(0, $position - $radius);
        $snippet = substr($source, $start, $radius * 2);

        return ($start > 0 ? '…' : '').trim($snippet).($start + $radius * 2 < strlen($source) ? '…' : '');
    }
}
