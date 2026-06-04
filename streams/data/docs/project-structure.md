---
title: Project structure
description: 'Where stream definitions, content, views, and app code live.'
sort_order: 1
category: this-project
status: ready
---

## Top-level layout

```
streams.dev/
├── app/                    # Minimal Laravel app code
├── streams/                # Stream JSON definitions
├── streams/data/           # Filebase content for streams
├── resources/views/        # Blade templates
├── routes/                 # Laravel routes (mostly defaults)
├── config/                 # Laravel + streams config
└── composer.json           # Core, UI, path repos
```

## `streams/` — configuration

Each `.json` file defines one stream. Examples in this repo:

| File | Stream handle | Purpose |
|------|---------------|---------|
| `pages.json` | `pages` | Site pages at `/`, `/docs`, `/addons` |
| `docs.json` | `docs` | Hub documentation at `/docs/{id}` |
| `docs_categories.json` | `docs_categories` | Sidebar and index groupings |
| `packages.json` | `packages` | Add-on catalog (`type: self`) |
| `core_docs.json` | `core_docs` | Core reference at `/docs/core/{id}` |
| `ui_docs.json` | `ui_docs` | UI reference at `/docs/ui/{id}` |
| `api_docs.json` | `api_docs` | API reference at `/docs/api/{id}` |

Stream JSON holds fields, routes, source adapters, and optional UI admin config. See [Streams](/docs/core/streams) for the full schema.

## `streams/data/` — content

Filebase entries live beside stream definitions:

```
streams/data/
├── docs/              # Hub guides (*.md)
├── core_docs/         # Core package docs
├── ui_docs/           # UI package docs
├── api_docs/          # API package docs
├── pages/             # HTML pages (*.html)
└── packages/          # Package catalog entries (if used)
```

Entry filenames become entry IDs (for example `introduction.md` → `/docs/introduction`).

## `resources/views/`

| Path | Used by |
|------|---------|
| `docs.blade.php` | All documentation routes |
| `page.blade.php`, `blank.blade.php` | Site pages stream |
| `partials/sidebar.blade.php` | Docs sidebar navigation |
| `partials/topbar.blade.php` | Site header |

## Application code

`AppServiceProvider` registers the admin panel:

```php
UI::panel(
    Panel::make('admin')
        ->default()
        ->path('admin')
        ->brandName('Streams')
        ->middleware(['web'])
);
```

Most domain logic lives in packages under `vendor/streams/` (symlinked from `../_packages/` when using path repositories).

## Composer path repositories

`composer.json` defines path repos for local package development:

```json
"repositories": [
    { "type": "path", "url": "../_packages/streams-core", "options": { "symlink": true } }
]
```

Run `composer update streams/core` after changing package source to refresh symlinks.

## Related

- [Site pages](/docs/site-pages) — how `pages.json` drives URLs
- [Content](/docs/content) — filebase formats and frontmatter
- [Contributing documentation](/docs/contributing-docs)
