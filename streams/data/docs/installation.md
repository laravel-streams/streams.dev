---
sort_order: 1
category: getting-started
title: Installation
description: 'Install Streams on new or existing Laravel projects.'
status: ready
---

## Server requirements

Streams requires a standard [Laravel-compatible environment](https://laravel.com/docs/deployment#server-requirements). Core supports Laravel 10, 11, and 12 (PHP 8.1+); the starter project and the test harness are Laravel 10 today. See [Versions and support](/docs/versions) for each package.

For image handling, install GD or the Imagick PHP extension. See [Images](/docs/images) and [Core images](/docs/core/images).

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

After requiring Core:

```bash
php artisan vendor:publish --tag=streams-config
php artisan vendor:publish --tag=streams-data
```

See [Core installation](/docs/core/installation) and [Configuration](/docs/configuration).

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
