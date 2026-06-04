---
sort_order: 1
category: getting-started
title: Installation
description: 'Install Streams on new or existing Laravel projects.'
status: ready
---

## Server requirements

Streams requires a standard [Laravel-compatible environment](https://laravel.com/docs/deployment#server-requirements).

For image handling, install GD or the Imagick PHP extension. See [Images](/docs/images).

## New projects

The fastest path is the Streams starter project:

```bash
composer create-project streams/streams:1.0.x-dev

cd streams

php artisan serve
```

### Included packages

The starter typically includes:

- [streams/core](/docs/core/introduction)
- [streams/api](/docs/api/introduction)
- [streams/ui](/docs/ui/introduction)
- [streams/sdk](/docs/sdk/introduction) (dev)

### Team workflow

Commit `streams/` and `streams/data/` to version control so stream definitions and filebase content stay in sync across developers and environments. Keep secrets in `.env` only.

Next: [Configuration](/docs/configuration) and [Architecture](/docs/architecture).

## Existing Laravel projects

Add only the packages you need:

```bash
composer require streams/core
composer require streams/ui
composer require streams/api
```

Core is the only required package. UI and API are optional layers.

### Local package development

To contribute to Streams packages from this repo, use Composer path repositories pointing at your local clones (see [Addons](/docs/addons)).

## Updating

Update individual packages:

```bash
composer update streams/core --with-dependencies
composer update streams/api --with-dependencies
composer update streams/ui --with-dependencies
```

Or update the full project:

```bash
composer update
```
