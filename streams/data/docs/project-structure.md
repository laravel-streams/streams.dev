---
title: Project structure
nav_title: Project structure
description: Where stream definitions, content, views, and app code live.
section: contributing
category: this-project
package: site
order: 20
tags: [site, project, structure]
status: ready
---

## Top-level layout

```text
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
| `core_docs.json` | `core_docs` | Points at `docs/packages/core` for `/docs/core/{id}` |
| `ui_docs.json` | `ui_docs` | Points at `docs/packages/ui` for `/docs/ui/{id}` |
| `api_docs.json` | `api_docs` | Points at `docs/packages/api` for `/docs/api/{id}` |

Stream JSON holds fields, routes, source adapters, and optional UI admin config. See [Streams](/docs/core/streams) for the full schema.

## `streams/data/` — content

Filebase entries live beside stream definitions:

```text
streams/data/
├── docs/              # Hub guides (*.md)
├── pages/             # HTML pages (*.html)
└── packages/          # Package catalog entries (if used)

docs/packages/         # Package reference, one folder per package
├── core/
├── ui/
└── api/
```

`docs/nav.json` is the sidebar headings for those folders. The stream files only point `source.path` at the folder.

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

Most domain logic lives in the packages under `vendor/streams/`, which Composer installs as git clones from GitHub.

## Composer repositories

`composer.json` points each Streams package at its GitHub repository and pins the branch:

```json
{
    "require": {
        "streams/core": "dev-rc/prep as 2.0.x-dev",
        "streams/ui": "1.0.x-dev"
    },
    "repositories": [
        { "type": "vcs", "url": "https://github.com/laravel-streams/streams-core.git", "no-api": true }
    ]
}
```

The `as 2.0.x-dev` alias lets packages that require `streams/core ^2.0` accept the branch. For local package work, `php scripts/composer-local.php` writes a gitignored `composer.local.json` with symlinked path repositories; see [Local development](/docs/local-development).

## Related

- [Site pages](/docs/site-pages) — how `pages.json` drives URLs
- [Content](/docs/content) — filebase formats and frontmatter
- [Contributing documentation](/docs/contributing-docs)
