## Streams.dev

Streams is a modular ecosystem of Laravel packages (Core, UI, API, SDK) for building configurable, data-driven web applications and control panels.

This repository is the **Streams developer platform** and the **canonical documentation site** for the ecosystem.

### Get started

Requirements:

- PHP 8.2 or newer (production runs 8.2.4; day-to-day development is on 8.4), with the extensions [Laravel 12 needs](https://laravel.com/docs/12.x/deployment#server-requirements)
- Composer 2
- Node.js 20.19+ or 22.12+ with npm
- Git (Composer clones the Streams packages from GitHub)

No database is needed to run the site. The content lives in flat files.

```bash
git clone https://github.com/laravel-streams/streams.dev.git --branch next
cd streams.dev
composer install
cp .env.example .env
php artisan key:generate
npm install
composer dev
```

Then open http://127.0.0.1:8427.

`composer dev` runs three processes through `concurrently`: `php artisan serve` on 127.0.0.1:8427, `php artisan pail` (a live tail of the application log), and the Vite dev server (with HMR) on 127.0.0.1:5427. Both ports are strict, so a clash fails loudly instead of drifting to another port. For a production-style build, run `npm run build`; the built assets in `public/build` are committed.

Run the tests with `php artisan test` (PHP) and `npm run test:js` (JavaScript).

Working in Cursor or another coding agent? Start with [AGENTS.md](AGENTS.md). It has the repository map, the commands, and a guided tour you can ask the agent to walk you through.

#### Where the Streams packages come from

`composer.json` installs `streams/core`, `streams/ui`, and `streams/sdk` straight from their GitHub repositories (`vcs` repositories with `no-api`, so no GitHub token is needed):

| Package | Branch | Constraint |
|---------|--------|------------|
| `streams/core` | `rc/prep` | `dev-rc/prep as 2.0.x-dev` |
| `streams/ui` | `1.0` | `1.0.x-dev` |
| `streams/sdk` (dev) | `sdk/rc` | `dev-sdk/rc as 1.0.x-dev` |

They install as git clones under `vendor/streams/`, so you can read their history. `config.platform.php` is pinned to 8.2.4 (the production PHP) so the lock only holds versions that run there.

#### Working on the packages locally

If you keep package checkouts next to this repository, symlink them in without touching `composer.json`:

```bash
composer local                       # symlink ../_packages/streams-* and update streams/*
composer local -- --rc=core,sdk      # link those packages from ../_rc/streams-* instead
```

`composer local` runs `scripts/composer-local.php --update`. The script looks for `../_packages/streams-<name>`, then `../streams-<name>`, or a path in `STREAMS_<NAME>_PATH` (for example `STREAMS_CORE_PATH=../streams-core`). `../_rc/streams-<name>` is used only with `--rc` / `--rc=<names>` or `STREAMS_LOCAL_RC`, and the script warns when a checkout lacks the branch `composer.json` asks for. It puts those checkouts in front of the GitHub repositories as symlinked path repositories and writes its own `composer.local.lock`, so `composer.lock` stays as committed. Run a plain `composer install` to go back to the GitHub packages.

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

Browse `/docs` for documentation and `/addons` for the package catalog.
