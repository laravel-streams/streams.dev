## Streams.dev

Streams is a modular ecosystem of Laravel packages (Core, UI, API, SDK) for building configurable, data-driven web applications and control panels.

This repository is the **Streams developer platform** and the **canonical documentation site** for the ecosystem.

### Packages

- **Streams Core** — domain-driven, JSON-configured streams and field types
- **Streams UI** — control panel, forms, tables, and pages
- **Streams API** — REST layer for stream data
- **Streams SDK** — development workflow and automation
- **Streams Testing** — test helpers for Streams packages

### Documentation

All public docs live as flat files under `streams/data/` and are served by URL:

| Section | Path |
|---------|------|
| Hub guides | `/docs/{slug}` → `streams/data/docs/` |
| Core | `/docs/core/{slug}` → `streams/data/core_docs/` |
| UI | `/docs/ui/{slug}` → `streams/data/ui_docs/` |
| API | `/docs/api/{slug}` → `streams/data/api_docs/` |
| SDK | `/docs/sdk/{slug}` → `streams/data/sdk_docs/` |
| Testing | `/docs/testing/{slug}` → `streams/data/testing_docs/` |
| Client | `/docs/client/{slug}` → `streams/data/client_docs/` |

See [STYLE.md](STYLE.md) for voice, frontmatter, and content boundaries.

### Contributing to docs

1. Edit markdown in the appropriate `streams/data/` directory.
2. Follow frontmatter conventions in `STYLE.md`.
3. Hub pages link to section docs; avoid duplicating reference material.
4. Package repos should link to `https://streams.dev/docs/...` rather than maintaining separate doc trees.

### Getting started (local)

```bash
composer install
php artisan serve
```

Browse `/docs` for documentation and `/addons` for the package catalog.
