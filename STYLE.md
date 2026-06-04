# Streams.dev Style Guide

Documentation and site copy for streams.dev live in this repository. Package repos link here; they do not maintain parallel doc trees.

## Documentation locations

| URL path | Filesystem | Purpose |
|----------|------------|---------|
| `/docs/{id}` | `streams/data/docs/{id}.md` | Hub guides, architecture, use cases |
| `/docs/core/{id}` | `streams/data/core_docs/{id}.md` | Streams Core reference |
| `/docs/ui/{id}` | `streams/data/ui_docs/{id}.md` | Streams UI reference |
| `/docs/api/{id}` | `streams/data/api_docs/{id}.md` | Streams API reference |
| `/docs/sdk/{id}` | `streams/data/sdk_docs/{id}.md` | Streams SDK reference |
| `/docs/testing/{id}` | `streams/data/testing_docs/{id}.md` | Streams Testing reference |
| `/docs/client/{id}` | `streams/data/client_docs/{id}.md` | API client reference |

## Frontmatter

Every doc page uses YAML frontmatter:

```yaml
---
sort_order: 1
title: Page Title
description: 'One-line summary for indexes and meta.'
category: getting-started   # hub docs only; omit in package sections
status: ready               # drafting | editing | ready
---
```

Entry `id` is the filename without `.md`.

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
- System font stack
- Generous whitespace, minimal chrome
- Code blocks use monochrome-friendly syntax highlighting
