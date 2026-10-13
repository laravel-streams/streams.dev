---
title: Site pages
nav_title: Site pages
description: How pages.json and HTML entries drive site URLs without controllers.
section: contributing
category: this-project
package: site
order: 30
tags: [site, pages]
status: ready
---

## Overview

Public site URLs (`/`, `/docs`, `/addons`) are served by the **pages** stream. No route files or controllers define these paths — the stream's `routes` block and entry frontmatter do.

Read this page when you need to add or change a marketing or landing page in this repository.

## Stream definition

From `streams/pages.json`:

```json
{
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

Key options:

- **`parse: true`** — registers one Laravel route per entry using each entry's `uri` field
- **`{uri}`** — route parameter bound to the entry's `uri` value
- **`{layout}`** — resolves the Blade layout from entry frontmatter

## Page entry format

Pages are HTML files in `streams/data/pages/` with YAML frontmatter:

```html
---
title: Documentation
uri: docs
layout: page
sort_order: 1
---

@include('partials.topbar')

<section class="container mx-auto pt-24 px-8">
    <h1 class="text-5xl font-extrabold">{{ $entry->title }}</h1>
</section>
```

| Frontmatter key | Purpose |
|-----------------|---------|
| `uri` | URL path (`/` for homepage uses `uri: /` or root value) |
| `layout` | Blade layout name (`page`, `blank`, etc.) |
| `title` | Entry title, available as `$entry->title` in the body |

The homepage (`welcome.html`) uses `uri: /` and `layout: blank`.

## How routing works

1. At boot, Core reads `pages.json` and registers routes for each entry because `parse` is true.
2. A request to `/docs` matches the entry whose `uri` is `docs`.
3. `EntryController` renders the entry body through the layout named in frontmatter.

For programmatic routes outside the pages stream, use Laravel's `Route` facade or [Route::streams()](/docs/core/routes).

## Documentation vs site pages

| Stream | URL pattern | Content |
|--------|-------------|---------|
| `pages` | `/`, `/docs`, `/addons` | HTML with Blade |
| `docs` | `/docs/{id}` | Markdown hub guides |
| `core_docs` | `/docs/core/{id}` | Markdown in `docs/packages/core` |

The `/docs` **index** is a pages entry (`docs.html`). Individual guide pages use the `docs` stream.

## Related

- [Content](/docs/content) — filebase formats
- [Routing](/docs/routing) — Laravel vs stream routes
- [Core routes reference](/docs/core/routes)
