<?php

namespace App\Support;

use Illuminate\Support\Arr;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Str;
use Streams\Core\Support\Facades\Streams;

/**
 * The Explore "choose your path" tree (streams/data/explore/*.md).
 *
 * Each node's front matter drives navigation: `menu` (stacked choices),
 * `options` (inline choices), and `links` (more resources). `parent` builds
 * the breadcrumb. The same data is served to people (the explore view) and
 * to agents (/explore.json, /explore/{id}.json, /explore/{id}.md).
 */
class ExploreTree
{
    public const ROOT = 'idea';

    public const GROUPS = ['menu', 'options', 'links'];

    public static function find(string $id): ?object
    {
        return Streams::entries('explore')->find($id);
    }

    /**
     * @return \Illuminate\Support\Collection<int, object>
     */
    public static function all()
    {
        return Streams::entries('explore')->orderBy('sort_order', 'ASC')->get();
    }

    public static function label(object $entry): string
    {
        return (string) ($entry->label ?? null ?: $entry->title ?? $entry->id);
    }

    public static function parentId(object $entry): ?string
    {
        $parent = $entry->getAttributes()['parent'] ?? null;

        if (is_object($parent)) {
            $parent = $parent->id ?? null;
        }

        return filled($parent) ? (string) $parent : null;
    }

    /**
     * Root-first path to the entry, including the entry itself.
     *
     * @return array<int, array{id: string, label: string, url: string}>
     */
    public static function breadcrumbs(object $entry): array
    {
        $trail = [];
        $seen = [];
        $node = $entry;

        while ($node && ! isset($seen[$node->id])) {
            $seen[$node->id] = true;
            array_unshift($trail, [
                'id' => (string) $node->id,
                'label' => static::label($node),
                'url' => URL::to('explore/'.$node->id),
            ]);

            $parentId = static::parentId($node);
            $node = $parentId ? static::find($parentId) : null;
        }

        return $trail;
    }

    /**
     * Normalized choices for one front-matter group.
     *
     * @return array<int, array{text: string, href: string, type: string, target: ?string, external: bool}>
     */
    public static function choices(object $entry, string $group): array
    {
        $items = $entry->getAttributes()[$group] ?? [];

        if (! is_array($items)) {
            return [];
        }

        $default = $group === 'links' ? 'link' : 'primary';

        return array_values(array_filter(array_map(function ($item) use ($default) {
            if (! is_array($item) || blank($item['text'] ?? null) || blank($item['href'] ?? null)) {
                return null;
            }

            $type = in_array($item['type'] ?? null, ['primary', 'secondary', 'link']) ? $item['type'] : $default;
            $href = (string) $item['href'];

            return [
                'text' => trim((string) $item['text']),
                'href' => $href,
                'type' => $type,
                'target' => $item['target'] ?? null,
                'external' => Str::startsWith($href, ['http://', 'https://']),
            ];
        }, $items)));
    }

    /**
     * Machine-readable node for /explore/{id}.json.
     */
    public static function toArray(object $entry): array
    {
        $absolute = fn (array $choice) => Arr::except($choice, ['external']) + [
            'url' => $choice['external'] ? $choice['href'] : URL::to($choice['href']),
            'node' => static::nodeIdFromHref($choice['href']),
        ];

        return [
            'id' => (string) $entry->id,
            'title' => (string) $entry->title,
            'label' => static::label($entry),
            'description' => $entry->description ?? null,
            'parent' => static::parentId($entry),
            'url' => URL::to('explore/'.$entry->id),
            'markdown_url' => URL::to('explore/'.$entry->id.'.md'),
            'json_url' => URL::to('explore/'.$entry->id.'.json'),
            'breadcrumbs' => array_map(fn ($crumb) => Arr::only($crumb, ['id', 'label']), static::breadcrumbs($entry)),
            'body' => trim((string) ($entry->body ?? '')),
            'menu' => array_map($absolute, static::choices($entry, 'menu')),
            'options' => array_map($absolute, static::choices($entry, 'options')),
            'links' => array_map($absolute, static::choices($entry, 'links')),
        ];
    }

    /**
     * The whole tree for /explore.json: every node plus its outgoing edges.
     */
    public static function toTree(): array
    {
        $nodes = static::all()->map(function ($entry) {
            $edges = collect(static::GROUPS)
                ->flatMap(fn ($group) => static::choices($entry, $group))
                ->map(fn ($choice) => static::nodeIdFromHref($choice['href']))
                ->filter()
                ->unique()
                ->values()
                ->all();

            return [
                'id' => (string) $entry->id,
                'label' => static::label($entry),
                'title' => (string) $entry->title,
                'parent' => static::parentId($entry),
                'url' => URL::to('explore/'.$entry->id),
                'markdown_url' => URL::to('explore/'.$entry->id.'.md'),
                'json_url' => URL::to('explore/'.$entry->id.'.json'),
                'next' => $edges,
            ];
        })->values()->all();

        return [
            'name' => 'Streams Explore',
            'description' => 'A choose-your-path walk through Streams. Start at the root node and follow `next`; each node is also available as Markdown and JSON.',
            'root' => static::ROOT,
            'nodes' => $nodes,
        ];
    }

    /**
     * Agent-friendly Markdown for /explore/{id}.md.
     */
    public static function toMarkdown(object $entry): string
    {
        $lines = [];
        $crumbs = array_map(fn ($crumb) => $crumb['label'], static::breadcrumbs($entry));

        $lines[] = '# '.$entry->title;
        $lines[] = '';
        $lines[] = '> Explore path: '.implode(' > ', $crumbs).' · Node `'.$entry->id.'` · Tree: '.URL::to('explore.json');
        $lines[] = '';

        if ($body = trim((string) ($entry->body ?? ''))) {
            $lines[] = preg_replace_callback('#\]\((/[^)\s]*)\)#', fn ($m) => ']('.URL::to($m[1]).')', $body);
            $lines[] = '';
        }

        $headings = ['menu' => 'Choices', 'options' => 'Choices', 'links' => 'More resources'];

        foreach (static::GROUPS as $group) {
            $choices = static::choices($entry, $group);

            if (! $choices) {
                continue;
            }

            if (! in_array('## '.$headings[$group], $lines)) {
                $lines[] = '## '.$headings[$group];
                $lines[] = '';
            }

            foreach ($choices as $choice) {
                $url = $choice['external'] ? $choice['href'] : URL::to($choice['href']);
                $node = static::nodeIdFromHref($choice['href']);
                $lines[] = '- ['.$choice['text'].']('.$url.')'.($node ? ' (node `'.$node.'`, Markdown: '.URL::to('explore/'.$node.'.md').')' : '');
            }

            $lines[] = '';
        }

        return rtrim(implode("\n", $lines))."\n";
    }

    protected static function nodeIdFromHref(string $href): ?string
    {
        return preg_match('#^/explore/([a-z0-9-]+)$#', $href, $match) ? $match[1] : null;
    }
}
