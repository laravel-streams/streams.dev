<?php

namespace App\Support;

use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\URL;
use Streams\Core\Support\Facades\Streams;

/**
 * Builds /llms.txt (an index of every docs page) and /llms-full.txt (every
 * docs page inlined) from DocsSearchIndex, following https://llmstxt.org.
 */
class LlmsText
{
    public const SUMMARY = 'Streams is an agentic-first Laravel package system (Tailwind, Alpine, Laravel, Livewire) for data modeling, admin UI, REST APIs, and developer tooling. Streams are defined as JSON in streams/*.json and queried through a criteria and repository abstraction, so storage can change without changing application code.';

    public static function index(): string
    {
        return Cache::remember('docs.llms.index', now()->addMinutes(15), function () {
            $lines = [
                '# Streams',
                '',
                '> '.static::SUMMARY,
                '',
                'Packages: streams/core (data platform), streams/ui (Livewire admin panels and components), streams/api (REST API), streams/sdk (Artisan generators; dev only), streams/testing (test harness), and @laravel-streams/api-client (JavaScript client). Every link below points at raw markdown; drop the .md suffix for the HTML page. '
                    .'The full text of every page is at '.URL::to('/llms-full.txt').'.',
                '',
            ];

            foreach (static::sections() as $label => $documents) {
                $lines[] = '## '.$label;
                $lines[] = '';

                foreach ($documents as $document) {
                    $line = '- ['.$document['nav_title'].']('.$document['markdown'].')';

                    if ($document['description'] !== '') {
                        $line .= ': '.$document['description'];
                    }

                    $lines[] = $line;
                }

                $lines[] = '';
            }

            $lines[] = '## Optional';
            $lines[] = '';
            $lines[] = '- [OpenAPI reference]('.URL::to('/docs/api/openapi.yaml').'): Generic OpenAPI 3 spec of the built-in streams/api endpoints';
            $lines[] = '- [Search index]('.URL::to('/search/docs.json').'): JSON index of every page with title, description, URL, and excerpt';
            $lines[] = '';

            return implode("\n", $lines);
        });
    }

    public static function full(): string
    {
        return Cache::remember('docs.llms.full', now()->addMinutes(15), function () {
            $parts = [
                '# Streams',
                '',
                '> '.static::SUMMARY,
                '',
            ];

            foreach (static::sections() as $label => $documents) {
                foreach ($documents as $document) {
                    $parts[] = '---';
                    $parts[] = '';
                    $parts[] = static::page(['title' => $document['nav_title']] + $document, $label);
                }
            }

            return implode("\n", $parts);
        });
    }

    /**
     * Render one page as standalone markdown.
     */
    public static function page(array $document, ?string $label = null): string
    {
        $lines = ['# '.($label ? $label.': ' : '').$document['title'], ''];

        if ($document['description'] !== '') {
            $lines[] = '> '.$document['description'];
            $lines[] = '';
        }

        $lines[] = 'Source: '.$document['url'];
        $lines[] = '';
        $lines[] = static::demoteHeadings(trim($document['body']));
        $lines[] = '';

        return implode("\n", $lines);
    }

    /**
     * Group documents under llms.txt section headings: hub guides by
     * category, then one section per package.
     */
    protected static function sections(): array
    {
        $documents = DocsSearchIndex::documents();

        $categories = Streams::exists('docs_categories')
            ? Streams::entries('docs_categories')->orderBy('order', 'ASC')->get()->pluck('name', 'id')->all()
            : [];

        $sections = [];

        foreach ($categories as $id => $name) {
            foreach ($documents as $document) {
                if ($document['package'] === 'guides' && $document['category'] === $id) {
                    $sections['Guides: '.$name][] = $document;
                }
            }
        }

        foreach ($documents as $document) {
            if ($document['package'] === 'guides' && ! isset($categories[$document['category']])) {
                $sections['Guides'][] = $document;
            }
        }

        foreach ($documents as $document) {
            if ($document['package'] !== 'guides') {
                $sections[$document['label'].' reference'][] = $document;
            }
        }

        // Pages about the streams.dev repo itself matter least to agents
        // building Streams apps, so they go last.
        $site = 'Guides: '.($categories['this-project'] ?? '');

        if (isset($sections[$site])) {
            $sections += [$site => Arr::pull($sections, $site)];
        }

        return $sections;
    }

    /**
     * Pages are concatenated under a single H1 each, so a page body's own
     * H1s are demoted to H2. Fenced code blocks are left alone.
     */
    protected static function demoteHeadings(string $markdown): string
    {
        $inFence = false;

        return implode("\n", array_map(function (string $line) use (&$inFence) {
            if (preg_match('/^\s*(```|~~~)/', $line)) {
                $inFence = ! $inFence;
            }

            if (! $inFence && preg_match('/^# /', $line)) {
                return '#'.$line;
            }

            return $line;
        }, explode("\n", $markdown)));
    }
}
