<?php

namespace App\Support;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Str;
use Streams\Core\Support\Facades\Streams;

class DocsSearchIndex
{
    protected static array $docStreams = [
        'docs' => ['package' => 'guides', 'label' => 'Guides', 'section' => 'guide', 'prefix' => '/docs'],
        'core_docs' => ['package' => 'core', 'label' => 'Core', 'section' => 'reference', 'prefix' => '/docs/core'],
        'ui_docs' => ['package' => 'ui', 'label' => 'UI', 'section' => 'reference', 'prefix' => '/docs/ui'],
        'api_docs' => ['package' => 'api', 'label' => 'API', 'section' => 'reference', 'prefix' => '/docs/api'],
        'sdk_docs' => ['package' => 'sdk', 'label' => 'SDK', 'section' => 'reference', 'prefix' => '/docs/sdk'],
        'testing_docs' => ['package' => 'testing', 'label' => 'Testing', 'section' => 'reference', 'prefix' => '/docs/testing'],
        'client_docs' => ['package' => 'client', 'label' => 'Client', 'section' => 'reference', 'prefix' => '/docs/client'],
    ];

    /**
     * Search index consumed by the Cmd+K search at /search/docs.json.
     */
    public static function all(): array
    {
        return Cache::remember('docs.search.index', now()->addMinutes(15), function () {
            $items = array_map(fn (array $document) => [
                'title' => $document['title'],
                'description' => $document['description'],
                'url' => $document['url'],
                'markdown' => $document['markdown'],
                'package' => $document['package'],
                'section' => $document['section'],
                'excerpt' => static::excerpt($document['body']),
            ], static::documents());

            if (Streams::exists('pages')) {
                foreach (Streams::entries('pages')->where('nav_disabled', '!=', true)->get() as $page) {
                    $uri = trim((string) ($page->uri ?? ''), '/');
                    $items[] = [
                        'title' => (string) ($page->title ?? $uri),
                        'description' => 'Site page',
                        'url' => URL::to($uri === '' ? '/' : '/'.$uri),
                        'markdown' => null,
                        'package' => 'site',
                        'section' => 'page',
                        'excerpt' => static::excerpt((string) ($page->body ?? '')),
                    ];
                }
            }

            return $items;
        });
    }

    /**
     * Every markdown docs page, grouped by package in sidebar order and
     * sorted by sort_order within each package. Used by the search index,
     * /llms.txt, /llms-full.txt, and the raw markdown routes.
     */
    public static function documents(): array
    {
        $documents = [];

        foreach (static::$docStreams as $handle => $meta) {
            if (! Streams::exists($handle)) {
                continue;
            }

            $entries = Streams::entries($handle)->get()
                ->filter(fn ($entry) => filled($entry->id ?? null))
                ->sortBy(fn ($entry) => [(int) ($entry->sort_order ?? 0), (string) $entry->id]);

            foreach ($entries as $entry) {
                $path = $meta['prefix'].'/'.$entry->id;

                $documents[] = [
                    'stream' => $handle,
                    'id' => (string) $entry->id,
                    'title' => (string) ($entry->title ?? $entry->id),
                    'description' => (string) ($entry->description ?? ''),
                    'category' => (string) ($entry->category ?? ''),
                    'url' => URL::to($path),
                    'markdown' => URL::to($path.'.md'),
                    'package' => $meta['package'],
                    'label' => $meta['label'],
                    'section' => $meta['section'],
                    'body' => (string) ($entry->body ?? ''),
                ];
            }
        }

        return $documents;
    }

    /**
     * Find one docs page by URL prefix ("/docs", "/docs/core", ...) and id.
     */
    public static function find(string $prefix, string $id): ?array
    {
        foreach (static::$docStreams as $handle => $meta) {
            if ($meta['prefix'] !== $prefix || ! Streams::exists($handle)) {
                continue;
            }

            if (! $entry = Streams::entries($handle)->find($id)) {
                return null;
            }

            return [
                'title' => (string) ($entry->title ?? $entry->id),
                'description' => (string) ($entry->description ?? ''),
                'url' => URL::to($prefix.'/'.$entry->id),
                'body' => (string) ($entry->body ?? ''),
            ];
        }

        return null;
    }

    /**
     * Package prefixes ("core", "ui", ...) that have their own docs stream.
     */
    public static function packages(): array
    {
        return collect(static::$docStreams)
            ->reject(fn ($meta) => $meta['prefix'] === '/docs')
            ->pluck('package')
            ->values()
            ->all();
    }

    protected static function excerpt(string $text): string
    {
        $text = preg_replace('/^---[\s\S]*?---\s*/', '', $text);
        $text = strip_tags($text);
        $text = preg_replace('/[#*`\[\]()]/', '', $text);
        $text = preg_replace('/\s+/', ' ', trim($text));

        return Str::limit($text, 200);
    }
}
