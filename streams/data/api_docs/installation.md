---
title: Installation
description: 'Composer, env vars, and wrapping API::routeStreams() / API::routeEntries().'
sort_order: 1
status: ready
---

Install the API package alongside Core.

## Require the package

```bash
composer require streams/api
```

## Publish configuration

```bash
php artisan vendor:publish --tag=config --provider="Streams\Api\ApiServiceProvider"
```

Config merges as `config/streams/api.php`.

## Register routes

Routes are **not** registered automatically. Add to `routes/api.php`:

```php
use Illuminate\Support\Facades\Route;
use Streams\Api\Support\Facades\API;

Route::middleware(config('streams.api.middleware'))
    ->prefix(config('streams.api.prefix'))
    ->group(function () {
        API::routeStreams();
        API::routeEntries();
    });
```

Default prefix is `api` — endpoints resolve to `/api/streams/...`.

## Environment

```env
STREAMS_API_ENABLED=true
STREAMS_API_PREFIX=api
STREAMS_API_MIDDLEWARE=api
```

`enabled` defaults to `false` in config. Setting the env var documents intent; route registration remains explicit in code.

## Related

- [Configuration](/docs/api/configuration)
- [Routes](/docs/api/routes)
- [Authentication](/docs/api/authentication)
