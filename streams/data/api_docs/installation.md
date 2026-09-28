---
title: 'API: Installation'
nav_title: Installation
description: Require streams/api, enable the gate, and register an interface from a service provider.
section: packages
package: api
order: 20
tags: [api, installation]
status: ready
---

Install the API alongside [Core](/docs/core/installation).

## Require the package

```bash
composer require streams/api:1.0.x-dev
```

`streams/api` has no tagged release yet, so the version constraint is the `1.0` development branch. See [Versions and support](/docs/versions).

Laravel auto-discovers `Streams\Api\ApiServiceProvider`. It registers the `API` facade, the `api:schema` and `api:documentation` commands, and two middleware aliases: `api.gate` (`EnsureApiIsEnabled`) and `api.interface` (`SetUpApiInterface`).

## Publish configuration (optional)

```bash
php artisan vendor:publish --tag=config --provider="Streams\Api\ApiServiceProvider"
```

This writes `config/streams/api.php`. See [Configuration](/docs/api/configuration).

## Enable the gate

Every API route runs through the gate middleware (`gate_middleware`, `EnsureApiIsEnabled` by default). It returns `404 Not Found` until the API is enabled:

```env
STREAMS_API_ENABLED=true
```

## Register routes

No routes exist until you register an interface. Do it in a service provider's `boot()` method:

```php
// app/Providers/AppServiceProvider.php
use Streams\Api\Support\Facades\API;

public function boot(): void
{
    API::routeCrud();      // streams + entries
    // API::routeEntries(); // entries only
    // API::routeStreams(); // stream definitions only
}
```

The helpers register the default interface (`STREAMS_API_DEFAULT_INTERFACE`, default `api`) at the configured prefix (`STREAMS_API_PREFIX`, default `api`) with the configured middleware group (`STREAMS_API_MIDDLEWARE`, default `api`). They do nothing if the default interface is already registered, so call only one of them: `routeEntries()` followed by `routeStreams()` registers the entry routes only.

> Don't call these helpers from `routes/api.php` or inside a `Route::prefix()->group()`. Registration happens immediately, so the surrounding group's prefix and middleware stack on top of the interface's own and you get paths like `/api/api/streams`.

For full control over the path, middleware, and resources, register an interface yourself:

```php
use Streams\Api\ApiInterface;
use Streams\Api\Resources\EntriesResource;
use Streams\Api\Support\Facades\API;

API::interface(
    ApiInterface::make('api')
        ->path('api')
        ->middleware(['auth:sanctum'])
        ->resources([EntriesResource::class])
);
```

## Verify

```bash
php artisan route:list --name=streams.api
curl -s http://localhost/api/streams/posts/entries
```

## Related

- [Configuration](/docs/api/configuration)
- [Routes](/docs/api/routes)
- [Authentication](/docs/api/authentication)
