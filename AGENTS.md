# AGENTS.md

Guide for coding agents (Cursor, Claude Code, Codex, and friends) and the people they help. Read this first. It tells you what Streams is, where everything lives, how to run the site, and how to walk a newcomer through it.

## What this is

**Laravel Streams** is a set of Laravel packages for building data-driven apps from JSON configuration. You describe a *stream* (a content type: its fields, validation, storage source, and routes) in a JSON file, and the packages give you a repository and query builder, entries, forms, tables, an admin panel, and a REST API for it. Storage is swappable: flat files, a database, or an API.

**streams.dev** (this repository) is the project's website and its canonical documentation. It is itself a Streams app: pages and docs are flat files described by streams, so the site dogfoods the packages. Package repositories link here instead of keeping their own `docs/` trees.

| Package | Composer name | What it does |
|---------|---------------|--------------|
| Core | `streams/core` | Streams, fields, entries, repositories, criteria (queries), sources (filebase, database), routing from stream JSON |
| UI | `streams/ui` | Livewire control panels, forms, tables, pages |
| API | `streams/api` | REST API over streams, with an OpenAPI description |
| SDK | `streams/sdk` | Generators, stubs, schema validation, and an MCP server for agents |
| Testing | `streams/testing` | Shared TestCase and helpers for package tests |
| Client | `@laravel-streams/api-client` | Zero-dependency JavaScript client for the API |

The GitHub organization is [`laravel-streams`](https://github.com/laravel-streams).

## Setup (walk a newcomer through this)

Requirements: PHP 8.2+ with the usual Laravel extensions, Composer 2, Node.js 20.19+ or 22.12+, and git. No database is needed.

```bash
git clone https://github.com/laravel-streams/streams.dev.git --branch next
cd streams.dev
composer install          # clones streams/core, streams/ui, streams/sdk from GitHub into vendor/streams/
cp .env.example .env
php artisan key:generate
npm install
composer dev              # app on http://127.0.0.1:8427, log tail (pail), Vite + HMR on 127.0.0.1:5427
```

Open http://127.0.0.1:8427. If a port is taken, `composer dev` fails instead of drifting; free the port, or run the processes by hand with `php artisan serve --port=<free port>`, `php artisan pail`, and `npm run dev` (Vite is pinned to 5427 in `vite.config.js`).

Checks that everything works:

```bash
php artisan test                                   # PHP feature tests
npm run test:js                                    # JS unit tests (node:test)
curl -sI http://127.0.0.1:8427/docs/installation    # 200
curl -sI http://127.0.0.1:8427/docs/installation.md # 200, text/markdown
curl -s  http://127.0.0.1:8427/llms.txt | head
```

Common snags:

- *Composer says a package needs a newer PHP*: `config.platform.php` is pinned to 8.2.4 (the production PHP). Use PHP 8.2 or newer locally; do not remove the pin to "fix" it.
- *Blank styles*: either `composer dev` (Vite) is not running and `public/hot` is stale, or you need `npm run build`. Delete `public/hot` if Vite is stopped.
- *A new docs page does not show up*: the search and llms indexes cache for 15 minutes. Run `php artisan cache:clear`.

## Repository map

```
streams/                  Stream definitions (JSON). One file per stream.
  pages.json              Site pages: URL -> HTML file in streams/data/pages/
  docs.json               Hub guides        -> streams/data/docs/*.md         (/docs/{id})
  core_docs.json ...      Package reference -> streams/data/{core,ui,api,sdk,testing,client}_docs/*.md
  docs_categories.json    Hub sidebar groups
  packages.json           Addon catalog (/addons)
  explore.json            The /explore tree
  data/                   The content itself (markdown, HTML, JSON)
app/Support/              Site helpers
  DocumentationMarkdown   Renders docs markdown
  DocsSearchIndex         /search/docs.json and the docs lookup
  LlmsText                /llms.txt, /llms-full.txt, raw .md routes
  ExploreTree             /explore, /explore/{id}.md|.json
app/Providers/AppServiceProvider.php   Registers the UI panel and the /ui form component
routes/web.php            The few hand-written routes (search index, llms, schema, explore)
resources/views/          Blade: layouts/ (shell, docs), partials/ (topbar, sidebar, search), components/
resources/css/            Design system (see below); app.css imports the rest
resources/js/             app.js, docs-search.js (Cmd+K), docs-filter.js (sidebar filter), reveal.js
public/build/             Built Vite assets. Committed; rebuild with `npm run build`
public/schema/streams.schema.json   JSON Schema for stream definitions (served at /schema/streams.schema.json)
tests/Feature/            PHP feature tests;  tests/js/  JS unit tests
Envoy.blade.php           Deployment tasks (Laravel Envoy); DEPLOY_* keys in .env.example
STYLE.md                  Voice and frontmatter rules for docs
docs.bak/, submodules/    Old material, not used by the site. Leave them alone.
```

How a request is served: most URLs have no controller. Core reads each `streams/*.json`, registers the `routes` it declares (for example `docs/{id}` renders the `docs` view with the matching entry), and loads entries from the stream's source (`streams/data/<handle>/` by default, format `md`, `html`, or `json`). Blade views then render the entry.

## Where the packages come from

`composer.json` resolves the Streams packages from GitHub (`vcs` repositories with `no-api: true`, so no token is needed), installed as git clones:

| Package | Branch | Constraint |
|---------|--------|------------|
| `streams/core` | `rc/prep` | `dev-rc/prep as 2.0.x-dev` |
| `streams/ui` | `1.0` | `1.0.x-dev` |
| `streams/sdk` (dev) | `sdk/rc` | `dev-sdk/rc as 1.0.x-dev` |

The `as` aliases let packages that require `streams/core ^2.0` accept the release-candidate branch. `streams/api` and `streams/testing` are documented here but not installed in this app.

To hack on a package and see it in the site, check it out next to this repository and run `php scripts/composer-local.php`, then `COMPOSER=composer.local.json composer update "streams/*"`. That symlinks your checkout into `vendor/streams/` through a gitignored `composer.local.json` and `composer.local.lock`. A plain `composer install` switches back. Never commit `composer.json` changes that point at local paths.

When your task is the site, do not edit files under `vendor/streams/*`. If a package needs a change, say so in your summary.

## Design system (the look is frozen)

The visual design is signed off. **Do not change colours, spacing, sizing, type, shapes, patterns or motion unless the maintainer asks.** Build new UI from the existing tokens and classes.

- `resources/css/tokens.css`: every design token (`--st-*`): colours for light and dark, glass surfaces, the type scale at an 18px root, spacing, radii, chamfers, motion durations and easings.
- `resources/css/base.css`, `components.css`: base elements and the component classes (`.st-btn`, `.st-card`, `.st-pill`, `.st-glass`, `.st-code` ...). Blade wrappers live in `resources/views/components/`.
- `resources/css/geometry.css`: the shape language, taken from the logo. The mark is an isometric line drawing: every edge is vertical or at 30 degrees. So corners are near-square (2 to 5px radii), and key surfaces (cards, buttons, code, search fields, chips) cut their top-right and bottom-left corners at 30 degrees through `--st-chamfer*` tokens. Also here: page rails and full-width section rules with plus marks (`.st-frame`, `.st-rule`), the isometric lattice (`.st-lattice`), and card corner ticks on hover.
- `docs.css` (docs layout, sidebar filter, TOC), `explore.css`, `prism.css`, `tocbot.css`.
- Monochrome and glassy; colour is only for accents. Keep WCAG AA contrast. All motion respects `prefers-reduced-motion`.
- Tailwind v4 through `@tailwindcss/vite`; there is no `tailwind.config.js`. Utilities are fine for layout, but visual decisions go through tokens.

`STYLE.md`'s "Visual" section predates this system; for visuals, this file and `tokens.css` win.

## Conventions

- Docs voice and frontmatter: `STYLE.md`. Every docs page needs the frontmatter keys listed there: `title` (unique across the site; it becomes the H1 and `<title>`), `nav_title` (the short sidebar label), `description` (the meta description), `section`, `package`, `order` (sidebar order, in steps of 10), `tags` and `status` (`draft`, `review`, `ready` or `deprecated`). Hub pages in `streams/data/docs/` also set `category` (a key in `streams/docs_categories.json`); package pages do not. The entry id is the filename without `.md`. Don't repeat the title as a `# H1` in the body. `tests/Feature/DocsContentTest.php` enforces all of this.
- Document what the code does. Label anything unregistered, commented out, or not shipped. Do not invent Artisan commands, routes, config keys, or release tags.
- Install lines in the docs use the development constraints that exist today (`streams/core:2.0.x-dev`, other packages `1.0.x-dev`). There is no stable 2.0 tag. Version support is summarized at `/docs/versions`.
- "SDK" means `streams/sdk` (PHP). "Client" means `@laravel-streams/api-client`.
- A docs URL with `.md` returns `text/markdown`. An unknown addon such as `/addons/no-such/package` must return 404, not 500.
- After CSS or JS changes, run `npm run build` and commit `public/build` with the change.
- Keep `php artisan test` and `npm run test:js` green. Add a feature test for new routes or pages.
- Commits: small, imperative subject lines ("Add ...", "Fix ..."). Do not commit `.env`, `vendor/`, `node_modules/`, `composer.local.*`, or local briefing files (`DOSSIER.md`, `.dossier/`, `TRACK_BRIEF.md`, `TRACK_REPORT.md`, `INTEGRATION_REPORT.md`).

## Guided tour (offer this to a newcomer)

Walk through these in order, opening each file and the matching URL side by side. Keep each stop to a couple of sentences and ask whether to go deeper.

1. **The home page.** Open `/` and `streams/data/pages/welcome.html`, then `streams/pages.json`. Point out that the page is an *entry* in the `pages` stream, and that its `uri` and `layout` fields decide where and how it renders.
2. **A stream definition.** `streams/docs.json`: fields, the `source` (markdown files), and the `routes` block that creates `/docs/{id}` without a controller.
3. **A docs page.** `/docs/core/introduction` next to `streams/data/core_docs/introduction.md`. Frontmatter becomes fields; the body renders through `app/Support/DocumentationMarkdown.php`.
4. **Search and filtering.** Press Cmd+K (full search, `resources/js/docs-search.js`, index at `/search/docs.json`). Then type in the sidebar filter (`resources/js/docs-filter.js`): it filters the nav, highlights matches in the page and the "On this page" list, and supports arrow keys, Enter and Esc.
5. **Docs for agents.** `/llms.txt`, `/llms-full.txt`, any page with `.md` appended, and `/explore` (also as `.json` and `.md`). See `app/Support/LlmsText.php` and `ExploreTree.php`.
6. **The stream schema.** `/schema/streams.schema.json`: the JSON Schema for stream files, from `streams/sdk`. Editors can use it to validate `streams/*.json`.
7. **The packages.** Open `vendor/streams/core` (start at its README and `src/`), then `vendor/streams/ui` and `vendor/streams/sdk`. Match each to its reference section at `/docs/core`, `/docs/ui`, `/docs/sdk`, and the catalog at `/addons`.
8. **The design system.** `resources/css/tokens.css`, then `geometry.css` next to `public/img/logo.svg`: the 30-degree cuts and the lattice come from the logo's own angles.
9. **Make a change.** Add `streams/data/docs/hello.md` with the full frontmatter from `STYLE.md`, for example:

   ```yaml
   ---
   title: Hello
   nav_title: Hello
   description: A first page.
   section: guides
   category: basics
   package: all
   order: 90
   tags: [example]
   status: draft
   ---
   ```

   Run `php artisan cache:clear`, open `/docs/hello`, and run `php artisan test` (the docs content tests check the frontmatter). Then delete it, or keep going and open a pull request.

## Deploying

`Envoy.blade.php` holds the deploy tasks (`envoy run deploy`, `envoy run rollback`). Install Envoy with `composer global require laravel/envoy`, then fill the `DEPLOY_*` keys in `.env` (documented in `.env.example`). Composer runs with `--ignore-platform-reqs` there for now because the current host (PHP 8.2.4) lacks ext-intl and ext-zip; the `config.platform.php` pin keeps the lock compatible with it. Only deploy when the maintainer asks.
