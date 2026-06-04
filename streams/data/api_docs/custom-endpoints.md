---
title: Custom endpoints
description: 'Manual route wiring patterns that work today.'
sort_order: 13
status: ready
---

Add custom endpoints by registering Laravel routes in the same middleware group as the Streams API routes.

## Pattern

```php
Route::middleware(config('streams.api.middleware'))
    ->prefix(config('streams.api.prefix'))
    ->group(function () {
        API::routeEntries();
        API::routeStreams();

        Route::get('streams/{stream}/stats', StatsController::class)
            ->name('streams.api.custom.stats');
    });
```

## Return ApiResponse

Use `Streams\Api\ApiResponse` for consistent envelopes:

```php
return (new ApiResponse())
    ->setData(['count' => $count])
    ->respond();
```

## Authorization

Apply Sanctum or policy middleware on custom routes the same way you protect standard API routes. See [Authentication](/docs/api/authentication).

## Related

- [Custom interfaces](/docs/api/custom-interfaces)
- [Routes](/docs/api/routes)
