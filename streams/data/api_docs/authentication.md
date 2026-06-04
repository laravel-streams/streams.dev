---
title: Authentication
description: 'Sanctum or middleware on the API route group — app responsibility.'
sort_order: 14
status: ready
---

Streams API does not ship authentication. Protect endpoints by wrapping the route group in your application's middleware.

## Sanctum example

```php
Route::middleware(['api', 'auth:sanctum'])
    ->prefix(config('streams.api.prefix'))
    ->group(function () {
        API::routeEntries();
        API::routeStreams();
    });
```

## Token abilities

Scope Sanctum token abilities per stream or operation using Laravel policies and middleware in your controllers.

## Default middleware

Config default `middleware` is `api` (Laravel's api group). Replace or extend in your route registration — the config value is a starting point, not enforced automatically.

## Related

- [Installation](/docs/api/installation)
- [Configuration](/docs/api/configuration)
