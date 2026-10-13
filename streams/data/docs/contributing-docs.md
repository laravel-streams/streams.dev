---
title: Contributing documentation
nav_title: Contributing documentation
description: How to add or edit documentation on streams.dev.
section: contributing
category: this-project
package: site
order: 60
tags: [site, contributing, docs]
status: ready
---

## Where docs live

Hub guides live in this repository under `streams/data/docs/`. Package reference lives in `docs/packages/{name}/` — one folder per package, whether or not this site installs it. `php artisan docs:sync` copies that folder to and from the package repository. Sidebar headings are pointers in `docs/nav.json`, not a second copy of the prose.

| Section | Path | URL |
|---------|------|-----|
| Hub guides | `streams/data/docs/` | `/docs/{id}` |
| Package reference | `docs/packages/{name}/` | `/docs/{name}/{id}` |

See [STYLE.md](https://github.com/laravel-streams/streams.dev/blob/develop/STYLE.md) for voice, frontmatter, and visual guidelines.

## Frontmatter

Every page requires YAML frontmatter:

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

The layout renders `title` as the page H1, so don't start the body with a `#` heading. Give every code fence a language, link to other pages with root-relative URLs (`/docs/core/fields`), and keep `json` blocks valid JSON. `tests/Feature/DocsContentTest.php` checks all of this.

**Status workflow:** `draft` → `review` → `ready` (and `deprecated` for pages kept only for old versions). Mark pages `ready` only after verifying claims against package source code.

## Hub vs section rules

- **Hub docs** explain workflows, architecture, and when to use a feature. Link to section docs for API depth.
- **Section docs** hold package-specific reference: classes, methods, configuration keys, examples.
- Do not duplicate long reference material in both places.

## Adding a hub page

1. Create `streams/data/docs/{slug}.md` with frontmatter including `category`.
2. Assign `order` within the category.
3. Add the page to a category in `streams/docs_categories.json` if introducing a new category.
4. Preview at `/docs/{slug}`.

## Adding a package reference page

1. Create `streams/data/{package}_docs/{slug}.md`.
2. Set `order` for sidebar ordering (no `category` field needed).
3. Sidebar picks up the page automatically from the doc stream entries.

## Accuracy standard

Document **what works today**. Do not describe APIs that are planned or commented out unless labeled as deferred.

Before marking `status: ready`, trace documented classes and methods to the package source. In this app, `vendor/streams/core`, `vendor/streams/ui`, and `vendor/streams/sdk` are symlinks into the package checkouts. `streams/api`, `streams/testing`, and the JavaScript client are not installed under `vendor/streams` here; read those checkouts directly. Do not edit files through `vendor/streams/*`. The `submodules/` directory in this repo is empty.

## Related

- [STYLE.md](https://github.com/laravel-streams/streams.dev/blob/develop/STYLE.md)
- [Project structure](/docs/project-structure)
- [Local development](/docs/local-development)
