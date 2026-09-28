---
title: Local development
nav_title: Local development
description: Install dependencies, run the dev server, and edit content locally.
section: contributing
category: this-project
package: site
order: 50
tags: [site, local, development]
status: ready
---

## Prerequisites

- PHP 8.2+ with the extensions required by [Laravel 12](https://laravel.com/docs/12.x/deployment#server-requirements) (this site runs Laravel 12)
- Composer 2
- Node.js 20.19+ or 22.12+ and npm (for Vite and Tailwind)
- Git (Composer clones the Streams packages from GitHub)

## First-time setup

```bash
git clone https://github.com/laravel-streams/streams.dev.git --branch develop
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
composer local
```

`composer local` runs `scripts/composer-local.php`, which finds checkouts at `../_packages/streams-<name>` or `../streams-<name>` (or the path in `STREAMS_<NAME>_PATH`), writes a gitignored `composer.local.json` and `composer.local.lock` that symlink them, and then runs `composer update "streams/*"` against that file. `composer.json` and `composer.lock` stay as committed. Edits in the checkout then show up immediately.

The script warns when a checkout does not contain the branch `composer.json` asks for (for example `_packages/streams-core` on `2.0` while the site needs `rc/prep`). To link release-candidate worktrees from `../_rc/streams-<name>` instead, ask for them by name:

```bash
composer local -- --rc=core,sdk      # or: STREAMS_LOCAL_RC=core,sdk composer local
composer local -- --rc               # every package from ../_rc
```

To only write `composer.local.json`, run `php scripts/composer-local.php` and then `COMPOSER=composer.local.json composer update "streams/*"` yourself. Run a plain `composer install` to switch back to the GitHub packages. Run package tests in the package directory; run `php artisan test` here for the site.

## Assets

Front-end assets compile through Vite:

```bash
npm run dev    # watch mode
npm run build  # production build
```

Styles live in `resources/css/` (`app.css` imports the tokens and components). Tailwind v4 runs through the `@tailwindcss/vite` plugin. The built files in `public/build` are committed, so run `npm run build` after changing CSS or JavaScript.

## Branches and deploys

Work on `develop` (or a short-lived branch off it that you merge back). When `develop` is ready, merge it into `master` and push both. `master` is the live site and GitHub's default branch, and `envoy run deploy` fast-forwards the server to it. The old `next`, `integration/rc` and `production` branches no longer exist.

## Related

- [Contributing documentation](/docs/contributing-docs)
- [This project](/docs/this-project)
- [Configuration](/docs/configuration)
