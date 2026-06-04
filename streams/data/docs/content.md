---
title: Content
description: 'Filebase pages and content fragments in Streams.'
sort_order: 4
category: basics
status: ready
---

## Introduction

Streams stores content as **entries** in configured sources. This hub page covers common content patterns; field types and adapters are documented in [Core](/docs/core/introduction).

## Pages stream

A typical pages stream uses the **filebase** source (default) with HTML or Markdown files:

```json
{
    "id": "pages",
    "config": {
        "source": {
            "type": "filebase",
            "format": "html"
        }
    },
    "routes": [
        {
            "handle": "view",
            "uri": "{uri}",
            "parse": true,
            "view": "{layout}"
        }
    ],
    "fields": [
        { "handle": "id", "type": "slug", "required": true, "unique": true },
        { "handle": "title", "type": "string", "required": true },
        { "handle": "uri", "type": "string", "required": true, "unique": true },
        { "handle": "body", "type": "string" }
    ]
}
```

### Entry frontmatter

Each file in `streams/data/pages/` carries YAML frontmatter plus a Blade/HTML body:

```html
---
title: Welcome
uri: /
layout: blank
---

@include('partials.topbar')
<h1>{{ $entry->title }}</h1>
```

| Key | Role |
|-----|------|
| `uri` | URL path for `parse: true` routes |
| `layout` | Blade layout passed to `{layout}` in the route definition |
| `title` | Stored field; available on `$entry` |

See [Site pages](/docs/site-pages) for how this repo wires `/`, `/docs`, and `/addons`.

## Markdown documentation

Doc streams set `format: md` and route to a shared view:

```json
{
    "routes": [
        {
            "uri": "docs/{id}",
            "view": "docs"
        }
    ]
}
```

Files live in `streams/data/docs/` (or `{package}_docs/`). The entry `id` matches the filename without extension.

## Posts and structured content

Blog or article streams follow the same pattern with different fields — for example `slug`, `published_at`, and a `relationship` to authors. Model fields in stream JSON; store entries in filebase or [database sources](/docs/core/sources-and-adapters).

## Blocks

Block content is an array field whose items map to structured fragments:

```json
{
    "handle": "content",
    "type": "array",
    "config": {
        "allowed": [
            { "stream": "gallery_blocks" },
            {
                "structure": [
                    { "handle": "title", "type": "string" },
                    { "handle": "body", "type": "string" }
                ]
            }
        ]
    }
}
```

Each block type can reference another stream or an inline field structure.

## Partials via Includes

For reusable view fragments, use Core's [Includes](/docs/core/views-and-includes) API rather than duplicating Blade `@include` paths in JSON:

```php
Includes::include('sidebar', 'partials.sidebar');
```

## Related

- [Site pages](/docs/site-pages)
- [Routing](/docs/routing)
- [Core streams](/docs/core/streams)
- [Core entries](/docs/core/entries)
