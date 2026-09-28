---
title: Installation
description: 'Composer, publish config/streams, env vars, and first stream.'
sort_order: 1
status: ready
---

Install Streams Core in a Laravel 10, 11, or 12 application via Composer. Core declares `laravel/framework ^10|^11|^12` and no PHP constraint of its own, so the floor is Laravel's (PHP 8.1 for Laravel 10). Its test suite runs on Laravel 10 today; see [Versions and support](/docs/versions).

## Require the package

```bash
composer require streams/core:2.0.x-dev
```

Core 2.0 has no stable tag yet. Without the `2.0.x-dev` constraint (or `"minimum-stability": "dev"` in your `composer.json`), Composer installs the old Core 1.10.4 instead.

Laravel auto-discovers `Streams\Core\StreamsServiceProvider`.

## Publish assets

```bash
php artisan vendor:publish --tag=streams-config
php artisan vendor:publish --tag=streams-data
```

| Tag | Output |
|-----|--------|
| `streams-config` | `config/streams/core.php` |
| `streams-data` | `streams/` directory scaffold |

Optional public assets:

```bash
php artisan vendor:publish --tag=public --provider="Streams\Core\StreamsServiceProvider"
```

## Environment variables

| Variable | Purpose |
|----------|---------|
| `STREAMS_DATA_PATH` | Filebase data directory (default `streams/data`) |
| `STREAMS_SOURCE` | Default source adapter (default `filebase`) |
| `STREAMS_DEFAULT_FORMAT` | Default file format (default `json`) |

## Verify installation

Create `streams/posts.json` and query entries:

```php
Streams::entries('posts')->count();
```

## Related

- [Configuration](/docs/core/configuration)
- [Streams](/docs/core/streams)
- [Hub: Installation](/docs/installation)
