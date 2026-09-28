---
title: Contributing documentation
description: 'How to add or edit documentation on streams.dev.'
sort_order: 5
category: this-project
status: ready
---

## Where docs live

All documentation for streams.dev is in **this repository** under `streams/data/`. Package repos link here; they do not maintain parallel doc trees.

| Section | Path | URL |
|---------|------|-----|
| Hub guides | `streams/data/docs/` | `/docs/{id}` |
| Core | `streams/data/core_docs/` | `/docs/core/{id}` |
| UI | `streams/data/ui_docs/` | `/docs/ui/{id}` |
| API | `streams/data/api_docs/` | `/docs/api/{id}` |

See [STYLE.md](https://github.com/laravel-streams/streams.dev/blob/develop/STYLE.md) for voice, frontmatter, and visual guidelines.

## Frontmatter

Every page requires YAML frontmatter:

```yaml
---
sort_order: 10
title: Page Title
description: 'One-line summary for indexes and meta.'
status: ready
category: getting-started   # hub docs only
---
```

**Status workflow:** `drafting` → `editing` → `ready`. Mark pages `ready` only after verifying claims against package source code.

## Hub vs section rules

- **Hub docs** explain workflows, architecture, and when to use a feature. Link to section docs for API depth.
- **Section docs** hold package-specific reference: classes, methods, configuration keys, examples.
- Do not duplicate long reference material in both places.

## Adding a hub page

1. Create `streams/data/docs/{slug}.md` with frontmatter including `category`.
2. Assign `sort_order` within the category.
3. Add the page to a category in `streams/docs_categories.json` if introducing a new group.
4. Preview at `/docs/{slug}`.

## Adding a package reference page

1. Create `streams/data/{package}_docs/{slug}.md`.
2. Set `sort_order` for sidebar ordering (no `category` field needed).
3. Sidebar picks up the page automatically from the doc stream entries.

## Accuracy standard

Document **what works today**. Do not describe APIs that are planned or commented out unless labeled as deferred.

Before marking `status: ready`, trace documented classes and methods to:

- `vendor/streams/core` for Core
- `vendor/streams/ui` for UI
- `submodules/streams-api` or your local `streams/api` clone for API

## Related

- [STYLE.md](https://github.com/laravel-streams/streams.dev/blob/develop/STYLE.md)
- [Project structure](/docs/project-structure)
- [Local development](/docs/local-development)
