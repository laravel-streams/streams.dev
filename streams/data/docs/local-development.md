---
title: Local development
description: 'Install dependencies, run the dev server, and edit content locally.'
sort_order: 4
category: this-project
status: ready
---

## Prerequisites

- PHP 8.0.2+ with extensions required by [Laravel 10](https://laravel.com/docs/10.x/deployment#server-requirements)
- Composer
- Node.js and npm (for Vite and Tailwind)

## First-time setup

```bash
git clone git@github.com:streams/streams.dev.git
cd streams.dev

composer install
cp .env.example .env
php artisan key:generate

npm install
npm run dev
```

In a second terminal:

```bash
php artisan serve
```

Visit `http://127.0.0.1:8000`. Use another port if 8000 is in use:

```bash
php artisan serve --port=8888
```

## Editing content

Documentation and pages are flat files — no database required for content changes.

| Content | Edit path | Preview URL |
|---------|-----------|-------------|
| Hub guide | `streams/data/docs/{id}.md` | `/docs/{id}` |
| Core doc | `streams/data/core_docs/{id}.md` | `/docs/core/{id}` |
| Site page | `streams/data/pages/{id}.html` | entry `uri` |
| Stream config | `streams/{handle}.json` | after cache clear if cached |

In local environment, doc pages show an **Edit this page** link that opens the markdown file in your editor.

## Admin panel

Streams UI registers a panel at `/admin` from `AppServiceProvider`. Use it to browse and edit stream entries when you prefer a UI over flat files.

## Local package development

`composer.json` path repositories point at sibling clones:

```
../_packages/streams-core
../_packages/streams-ui
../_packages/streams-api
...
```

After editing package source, changes are available immediately through symlinks. Run package tests in the package directory; run `php artisan test` here for the site.

## Assets

Front-end assets compile through Vite:

```bash
npm run dev    # watch mode
npm run build  # production build
```

Styles live in `resources/scss/app.scss`. Tailwind scans Blade and stream content paths configured in `tailwind.config.js`.

## Related

- [Contributing documentation](/docs/contributing-docs)
- [This project](/docs/this-project)
- [Configuration](/docs/configuration)
