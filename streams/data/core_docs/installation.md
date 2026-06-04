---
title: Installation
description: 'Composer, publish config/streams, env vars, and first stream.'
sort_order: 1
status: ready
---

Install Streams Core in any Laravel 10+ application via Composer.

## Require the package

```bash
composer require streams/core
```

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
