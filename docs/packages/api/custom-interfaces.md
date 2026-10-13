---
title: Custom interfaces
nav_title: Custom interfaces
description: Register several ApiInterface instances with their own path, domain, middleware, resources, and tenant.
section: packages
package: api
order: 130
tags: [api, custom, interfaces]
status: ready
---

An `ApiInterface` is one mounted API: a path (and optionally a domain), a middleware stack, and the resources and endpoints it serves. `API::routeCrud()` registers a single default interface for you. Register your own when you need versions, a separate admin API, or different auth for different consumers.

## Register an interface

```php
use Streams\Api\ApiInterface;
use Streams\Api\Resources\EntriesResource;
use Streams\Api\Resources\StreamsResource;
use Streams\Api\Support\Facades\API;

API::interface(
    ApiInterface::make('v1')
        ->path('api/v1')
        ->middleware(['auth:sanctum', 'throttle:api'])
        ->resources([StreamsResource::class, EntriesResource::class])
);
```

Call `API::interface()` from a service provider's `boot()` method. Routes are registered immediately, so don't wrap the call in a route group.

## Builder methods

| Method | Purpose |
|--------|---------|
| `ApiInterface::make(?string $id)` | Create and configure an interface. The ID names routes and must be unique. |
| `path(string $path)` | URL prefix. If empty, the ID is used. |
| `domain(?string $domain)` / `domains(array $domains)` | Register the routes once per domain. |
| `middleware(array $middleware)` | Append middleware for every route on the interface. |
| `resources(array $classes)` | `ApiResource` classes to mount (for example `EntriesResource`). |
| `endpoints(array $endpoints)` | Extra endpoints: `EndpointRouter` instances or `'uri' => action` pairs (registered as GET). |
| `routes(?Closure $routes)` | A closure called with the interface once per domain. Its routes are registered **outside** the interface group, so they don't get the interface path or middleware; add those yourself. |
| `tenant(mixed $tenant)` | A tenant value or resolver closure for this interface. See [Tenancy](/docs/api/tenancy). |

All methods return the interface, and it also uses Laravel's `Conditionable` and `Tappable`, so `->when()` and `->tap()` work.

## Route names

Routes on the default interface (`STREAMS_API_DEFAULT_INTERFACE`, default `api`) are named `streams.api.{resource}.{endpoint}`, for example `streams.api.entries.list`. Routes on any other interface include its ID: `streams.api.v1.entries.list`.

`SetUpApiInterface` reads the route name on each request to decide which interface is current, so `API::currentApiInterface()` and `API::getTenant()` know which interface served the request.

## Configure every interface

`ApiInterface` supports `configureUsing()`, which runs a closure against each interface as it's made:

```php
use Streams\Api\ApiInterface;

ApiInterface::configureUsing(function (ApiInterface $interface) {
    $interface->middleware(['throttle:api']);
});
```

## Subclass for reuse

Override `register()` or `boot()` to package an interface. `register()` runs when you pass it to `API::interface()`; `boot()` runs on the first request that hits one of its routes.

```php
use Streams\Api\ApiInterface;
use Streams\Api\Resources\EntriesResource;

class PartnerApi extends ApiInterface
{
    protected function setUp(): void
    {
        $this->path('api/partners')
            ->middleware(['auth:sanctum'])
            ->resources([EntriesResource::class]);
    }
}

API::interface(PartnerApi::make('partners'));
```

## Related

- [Authentication](/docs/api/authentication)
- [Custom endpoints](/docs/api/custom-endpoints)
- [Routes](/docs/api/routes)
