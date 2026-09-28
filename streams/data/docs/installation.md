---
title: Installation
nav_title: Installation
description: 'Install Streams in a new or existing Laravel app: requirements, the version matrix, Composer constraints, and publishing config.'
section: get-started
category: getting-started
package: all
order: 20
tags: [installation]
status: ready
---

## Server requirements

Streams requires a standard [Laravel-compatible environment](https://laravel.com/docs/deployment#server-requirements) with **PHP 8.2 or newer**. Core supports Laravel 10, 11, and 12, and this site (streams.dev) runs Laravel 12. The starter project and the `streams/testing` harness are still Laravel 10 only, and the SDK's MCP server needs Laravel 11.45+ or 12.41+. The [version matrix](#version-matrix) below has the details.

For image handling, install GD or the Imagick PHP extension. See [Images](/docs/images) and [Core images](/docs/core/images).

## Version matrix

| Package | Laravel 10 | Laravel 11 | Laravel 12 | Notes |
|---------|:----------:|:----------:|:----------:|-------|
| `streams/core` | Yes | Yes | Yes | Declares `^10\|^11\|^12`. Its CI runs on Laravel 10, through `streams/testing`. |
| `streams/ui` | Yes | Yes | Yes | Follows Core. Requires Livewire 3. |
| `streams/api` | Yes | Yes | Yes | Follows Core. |
| `streams/sdk` generators and `streams:validate` | Yes | Yes | Yes | Its own test suite runs on Laravel 12. |
| `streams/sdk` MCP server | No | 11.45+ | 12.41+ | Needs `laravel/mcp ^1.0`. On Laravel 10 it is skipped; use `streams:list --json` and `streams:validate --json`. |
| `streams/testing` | Yes | Not yet | Not yet | Requires `orchestra/testbench ^8.36` (Laravel 10 only). Being widened to `^8.36\|^9.15\|^10.8` for Laravel 11 and 12. |
| `streams/streams` starter | Yes | No | No | Pins `laravel/framework ^10.0`. |
| streams.dev (this site) | No | No | Yes | Runs Laravel 12 on PHP `^8.2`, with Core (`rc/prep`), UI, and the SDK as a dev dependency. |

Every PHP package needs **PHP 8.2 or newer**: Core, API, and SDK declare `php: ^8.2`, and UI and Testing require Core. Until `streams/testing` supports Laravel 11 and 12, the package test suites for Core, UI, and API run on Laravel 10 only, so treat 11 and 12 as supported but not yet verified in CI. See [Versions and support](/docs/versions) for install constraints and branches.

## New projects

The fastest path is the official Streams starter:

```bash
composer create-project streams/streams:1.0.x-dev my-app

cd my-app

php artisan serve
```

The starter ([laravel-streams/streams](https://github.com/laravel-streams/streams)) is a Laravel 10 application that requires Core, UI, and API. It has no tagged release yet, so the version is the `1.0` development branch.

## This repository (streams.dev)

**streams.dev** is not the generic starter — it is the documentation site. Clone it to work on docs or reference patterns:

```bash
git clone git@github.com:laravel-streams/streams.dev.git
cd streams.dev
composer install
npm install && npm run dev
php artisan serve
```

Production dependencies in this repo:

- [streams/core](/docs/core/introduction)
- [streams/ui](/docs/ui/introduction)

Dev dependency:

- [streams/sdk](/docs/sdk/introduction)

**streams/api** is not required here. Add it when you need REST endpoints (see [API installation](/docs/api/installation)).

See [This project](/docs/this-project) and [Local development](/docs/local-development).

## Existing Laravel projects

Add only the packages you need:

```bash
composer require streams/core:2.0.x-dev
composer require streams/core:2.0.x-dev streams/ui:1.0.x-dev
composer require streams/core:2.0.x-dev streams/api:1.0.x-dev
```

Core is the only required package. UI and API are optional layers.

Use the explicit `x-dev` constraints. None of these packages has a stable 2.x (Core) or 1.x (UI, API) tag yet, and a bare `composer require streams/core` installs the old **Core 1.10.4**. Alternatively, set `"minimum-stability": "dev"` and `"prefer-stable": true` in your `composer.json`. See [Versions and support](/docs/versions).

### Publish and configure

Publishing is optional; Core works with its defaults. To change the config, publish it from Core's provider:

```bash
php artisan vendor:publish --provider="Streams\Core\StreamsServiceProvider" --tag=config
```

Core registers three tags: `config` (`config/streams/core.php`), `streams` (Core's own stream definitions, copied into `streams/`), and `public` (Core's public assets, into `public/vendor/streams/core`). UI and API also use the `config` tag, so pass `--provider` to publish only one package's file. See [Core installation](/docs/core/installation) and [Configuration](/docs/configuration).

### Team workflow

Commit `streams/` and `streams/data/` to version control so stream definitions and filebase content stay in sync across developers and environments. Keep secrets in `.env` only.

## Local package development

To contribute to Streams packages, use Composer path repositories pointing at local clones (see [Addons](/docs/addons) and [Project structure](/docs/project-structure)).

## Updating

Update individual packages:

```bash
composer update streams/core --with-dependencies
composer update streams/ui --with-dependencies
composer update streams/api --with-dependencies
```

Or update the full project:

```bash
composer update
```

## Related

- [Versions and support](/docs/versions)
- [Configuration](/docs/configuration)
- [Architecture](/docs/architecture)
- [Use cases](/docs/use-cases)
