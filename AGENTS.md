# AGENTS.md

streams.dev is the documentation site for Laravel Streams. This repository is the source of truth for that documentation. Package repositories should link here rather than keep a second `docs/` tree.

## Layout

Laravel 10 application. There are no HTTP controllers for the public site.

- `streams/*.json` defines streams (routes, fields, source).
- `streams/pages.json` maps URLs to HTML in `streams/data/pages/`.
- Docs pages are markdown with YAML frontmatter:
  - `streams/data/docs/` → `/docs/{id}`
  - `streams/data/core_docs/` → `/docs/core/{id}`
  - `streams/data/ui_docs/` → `/docs/ui/{id}`
  - `streams/data/api_docs/` → `/docs/api/{id}`
  - `streams/data/sdk_docs/` → `/docs/sdk/{id}`
  - `streams/data/testing_docs/` → `/docs/testing/{id}`
  - `streams/data/client_docs/` → `/docs/client/{id}`
- The entry id is the filename without `.md`. Hub pages set `category` to a key in `streams/docs_categories.json`. Package pages do not.
- `app/Support/DocumentationMarkdown.php` renders the body.
- `app/Support/DocsSearchIndex.php` builds `/search/docs.json`, `/llms.txt`, `/llms-full.txt`, and the raw markdown routes in `routes/web.php`.
- The addon catalog is the `data` array in `streams/packages.json`.

A new markdown file is picked up by the sidebar, search, and `llms.txt` on the next cache miss (15 minutes: `docs.search.index`, `docs.llms.index`, `docs.llms.full`).

## Commands

```bash
composer install
npm install
php artisan serve --port=8002
php artisan test
npm run build
```

Use port **8002** for this app. Do not bind port 8000.

`composer.json` path-repositories symlink `streams/core`, `streams/ui`, and `streams/sdk` from `../_packages/`. `streams/api`, `streams/testing`, `streams/mongodb`, and the JavaScript client live in those checkouts too but are not all installed in this app. Read them. Do not edit files through `vendor/streams/*` or the package checkouts when your task is this site; record a package change in the work report instead.

## Conventions

- Voice and frontmatter: `STYLE.md`.
- Every docs page has `title` and `description`. Those become the HTML `<title>` and meta description.
- Document what the code does. Label anything unregistered, commented out, or not shipped. Do not invent Artisan commands, routes, or release tags.
- Install lines use the development branch constraints that exist today (`streams/core:2.0.x-dev`, other packages `1.0.x-dev`). There is no stable 2.0 tag. Version support is summarized at `/docs/versions`.
- The GitHub organization is `laravel-streams`.
- "SDK" means `streams/sdk` (PHP generators). "Client" means `@laravel-streams/api-client`.

## Verify

```bash
php artisan test
curl -sI http://127.0.0.1:8002/docs/installation
curl -sI http://127.0.0.1:8002/llms.txt
curl -sI http://127.0.0.1:8002/docs/installation.md
```

A docs URL with `.md` must return `text/markdown`. A missing addon such as `/addons/no-such/package` must return 404, not 500.

## Do not commit

`.env`, `vendor/`, `node_modules/`, and local briefing files (`DOSSIER.md`, `.dossier/`, `TRACK_BRIEF.md`, `TRACK_REPORT.md`).
