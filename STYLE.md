# Streams.dev Style Guide

Hub guides live in this repository. Package reference is copied into `docs/packages/{name}/` and synced with the package repository that owns it (`php artisan docs:sync`). Installed and not-installed packages use that same folder.

## Documentation locations

| URL path | Filesystem | Purpose |
|----------|------------|---------|
| `/docs/{id}` | `streams/data/docs/{id}.md` | Hub guides, architecture, use cases |
| `/docs/{package}/{id}` | `docs/packages/{package}/{id}.md` | Package reference (core, ui, api, sdk, testing, client) |

Sidebar headings for a package are pointers in `docs/nav.json`. They are not frontmatter, and `docs:sync` does not copy that file. `order` in frontmatter still sorts search and `/llms.txt`. The sidebar follows the nav map.

## Frontmatter

Every doc page uses YAML frontmatter:

```yaml
---
title: 'Core: Installation'      # required; unique across the site (the page H1)
nav_title: Installation          # short sidebar and browser-tab label
description: One sentence, plain text, for indexes and meta.
section: packages                # get-started | guides | concepts | reference | packages | contributing
category: getting-started        # hub docs only: a key in streams/docs_categories.json
package: core                    # core | ui | api | sdk | testing | client | site | all
order: 20                        # hub sidebar and search order; package sidebar is docs/nav.json
tags: [core, installation]
status: ready                    # draft | review | ready | deprecated
---
```

Entry `id` is the filename without `.md`. `tests/Feature/DocsContentTest.php` enforces these keys, the enums, unique titles, a language on every code fence, root-relative links, parseable `json` blocks, and no `# H1` in the body (the layout renders `title` as the H1).

## Hub vs section content

- **Hub docs** explain workflows, architecture, and when to use a feature. Link to section docs for API reference.
- **Section docs** hold package-specific depth: configuration, methods, examples.
- Do not duplicate long reference material in both places.

## Voice

Write for **Laravel developers and teams** building production applications.

- **Clean** — short sentences, scannable headings
- **Confident** — state what Streams does directly
- **Smart** — assume Laravel competence; explain why patterns fit team workflows
- **Human** — plain language; address the reader as "you" or "your team"
- **Laravel-native** — link to Laravel docs for generic config/routing unless Streams adds something specific

Avoid: emoji in body copy, CMS-only positioning, feature lists without a concrete example, re-documenting Laravel basics.

## Messaging pillars

1. **For Laravel developers** — Composer, Artisan, config, optional Eloquent. Streams extends Laravel.
2. **For teams** — Stream JSON in `streams/` is reviewable configuration in git.
3. **Unified, not monolithic** — Require Core alone or compose Core + UI + API.
4. **Data-source agnostic** — Model once; store in files, databases, or APIs.
5. **Ship real products** — Admin panels, SaaS backends, CMS content, product dashboards.

## Visual (site)

- Black, white, and subtle grays only — no color accent
- CSS tokens in `resources/scss/_tokens.scss` (`--color-page`, `--color-text`, etc.)
- System font stack; documentation body ~65ch (`max-w-[42rem]`)
- Generous whitespace, minimal chrome
- Code blocks use monochrome Prism theme (`resources/scss/_prism.scss`)

## Documentation layout

- Shared shell: `resources/views/layouts/docs.blade.php` (left nav, article, sticky right TOC on `xl+`)
- Hub index: `layout: docs-hub` on `streams/data/pages/docs.html`
- Markdown articles: `resources/views/docs.blade.php` via `DocumentationMarkdown` helper
- Search: `⌘K` / `Ctrl+K` and topbar trigger; index at `/search/docs.json` (`DocsSearchIndex`)
- Sidebar IA: **Reference** (packages) first; **New here?** and **Guides** collapsed by default
