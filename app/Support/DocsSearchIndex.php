<?php

namespace App\Support;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Str;
use Streams\Core\Support\Facades\Streams;

class DocsSearchIndex
{
    protected static array $docStreams = [
        'docs' => ['package' => 'guides', 'section' => 'guide', 'prefix' => '/docs'],
        'core_docs' => ['package' => 'core', 'section' => 'reference', 'prefix' => '/docs/core'],
        'ui_docs' => ['package' => 'ui', 'section' => 'reference', 'prefix' => '/docs/ui'],
        'api_docs' => ['package' => 'api', 'section' => 'reference', 'prefix' => '/docs/api'],
        'sdk_docs' => ['package' => 'sdk', 'section' => 'reference', 'prefix' => '/docs/sdk'],
        'testing_docs' => ['package' => 'testing', 'section' => 'reference', 'prefix' => '/docs/testing'],
        'client_docs' => ['package' => 'client', 'section' => 'reference', 'prefix' => '/docs/client'],
    ];

    public static function all(): array
    {
        return Cache::remember('docs.search.index', now()->addMinutes(15), function () {
            $items = [];

            foreach (static::$docStreams as $handle => $meta) {
                if (! Streams::exists($handle)) {
                    continue;
                }

                foreach (Streams::entries($handle)->get() as $entry) {
                    $id = $entry->id ?? null;
                    if (! $id) {
                        continue;
                    }

                    $body = (string) ($entry->body ?? '');
                    $items[] = [
                        'title' => (string) ($entry->title ?? $id),
                        'description' => (string) ($entry->description ?? ''),
                        'url' => URL::to($meta['prefix'].'/'.$id),
                        'package' => $meta['package'],
                        'section' => $meta['section'],
                        'excerpt' => static::excerpt($body),
                    ];
                }
            }

            if (Streams::exists('pages')) {
                foreach (Streams::entries('pages')->where('nav_disabled', '!=', true)->get() as $page) {
                    $uri = trim((string) ($page->uri ?? ''), '/');
                    $items[] = [
                        'title' => (string) ($page->title ?? $uri),
                        'description' => 'Site page',
                        'url' => URL::to($uri === '' ? '/' : '/'.$uri),
                        'package' => 'site',
                        'section' => 'page',
                        'excerpt' => static::excerpt((string) ($page->body ?? '')),
                    ];
                }
            }

            return $items;
        });
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
