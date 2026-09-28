---
title: Local development
description: 'Install dependencies, run the dev server, and edit content locally.'
sort_order: 4
category: this-project
status: ready
---

## Prerequisites

- PHP 8.2+ with the extensions required by [Laravel 12](https://laravel.com/docs/12.x/deployment#server-requirements) (this site runs Laravel 12)
- Composer 2
- Node.js 20.19+ or 22.12+ and npm (for Vite and Tailwind)
- Git (Composer clones the Streams packages from GitHub)

## First-time setup

```bash
git clone https://github.com/laravel-streams/streams.dev.git --branch next
cd streams.dev

composer install
cp .env.example .env
php artisan key:generate

npm install
composer dev
```

`composer dev` serves the app on `http://127.0.0.1:8427`, tails the application log with `php artisan pail`, and runs the Vite dev server (with hot reload) on port 5427. Both ports are strict: if one is taken the command stops instead of picking another. No database is needed.

Run the tests with `php artisan test` and `npm run test:js`.

## Editing content

Documentation and pages are flat files — no database required for content changes.

| Content | Edit path | Preview URL |
|---------|-----------|-------------|
| Hub guide | `streams/data/docs/{id}.md` | `/docs/{id}` |
| Core doc | `streams/data/core_docs/{id}.md` | `/docs/core/{id}` |
| Site page | `streams/data/pages/{id}.html` | entry `uri` |
| Stream config | `streams/{handle}.json` | after cache clear if cached |

In local environment, doc pages show an **Edit this page** link to that file on GitHub (`laravel-streams/streams.dev`, branch `develop`).

## Admin panel

Streams UI registers a panel at `/admin` from `AppServiceProvider`. Use it to browse and edit stream entries when you prefer a UI over flat files.

## Local package development

`composer.json` installs `streams/core` (branch `rc/prep`), `streams/ui` (`1.0`) and `streams/sdk` (`sdk/rc`) from their GitHub repositories as git clones in `vendor/streams/`.

To work on a package and see the change in this site, check it out next to this repository and link it in without editing `composer.json`:

```bash
php scripts/composer-local.php
COMPOSER=composer.local.json composer update "streams/*"
```

The script finds checkouts at `../_rc/streams-<name>`, `../_packages/streams-<name>`, or `../streams-<name>` (or `STREAMS_<NAME>_PATH`), and writes a gitignored `composer.local.json` that symlinks them. Edits in the checkout then show up immediately. Run a plain `composer install` to switch back. Run package tests in the package directory; run `php artisan test` here for the site.

## Assets

Front-end assets compile through Vite:

```bash
npm run dev    # watch mode
npm run build  # production build
```

Styles live in `resources/css/` (`app.css` imports the tokens and components). Tailwind v4 runs through the `@tailwindcss/vite` plugin. The built files in `public/build` are committed, so run `npm run build` after changing CSS or JavaScript.

## Related

- [Contributing documentation](/docs/contributing-docs)
- [This project](/docs/this-project)
- [Configuration](/docs/configuration)
