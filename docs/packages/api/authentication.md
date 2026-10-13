---
title: Authentication
nav_title: Authentication
description: The API ships no auth. Your app owns access through the gate middleware and interface middleware.
section: packages
package: api
order: 150
tags: [api, authentication]
status: ready
---

Streams API does not authenticate or authorize anyone. It has no users, tokens, or policies. **Your application owns access control**, and the package gives you three places to put it.

This is deliberate: every app already has an auth system (Sanctum, Passport, session auth, signed URLs, an API gateway), and the API should use yours rather than add another.

> Registered API routes are public unless you add middleware. `STREAMS_API_ENABLED=true` opens them; it does not protect them.

## 1. The gate: `gate_middleware`

Every API route runs `config('streams.api.gate_middleware')`, which defaults to `Streams\Api\Http\Middleware\EnsureApiIsEnabled` (aliased as `api.gate`). The default gate only checks `streams.api.enabled` and `gate_except`, then aborts with `gate_status` (404) and `gate_message`.

Extend it the way you extend Laravel's `VerifyCsrfToken`:

```php
// app/Http/Middleware/EnsureApiIsEnabled.php
namespace App\Http\Middleware;

use Illuminate\Http\Request;
use Streams\Api\Http\Middleware\EnsureApiIsEnabled as Middleware;

class EnsureApiIsEnabled extends Middleware
{
    protected function shouldEnable(Request $request): bool
    {
        if (! parent::shouldEnable($request)) {
            return false;
        }

        return $request->user()?->can('use-api') ?? false;
    }
}
```

```php
// config/streams/api.php
'gate_middleware' => \App\Http\Middleware\EnsureApiIsEnabled::class,
```

Denied requests get `gate_status`, which is `404` by default so the API's existence isn't revealed. Set `STREAMS_API_GATE_STATUS=403` if you'd rather be explicit.

Because the gate runs after the `middleware` group, `$request->user()` is available when that group authenticates (for example `['api', 'auth:sanctum']`).

## 2. Interface middleware

Attach authentication to an interface. It applies to every resource and endpoint on that interface:

```php
use Streams\Api\ApiInterface;
use Streams\Api\Resources\EntriesResource;
use Streams\Api\Resources\StreamsResource;
use Streams\Api\Support\Facades\API;

// Public, read-mostly API
API::interface(
    ApiInterface::make('api')
        ->path('api')
        ->middleware(['throttle:api'])
        ->resources([EntriesResource::class])
);

// Authenticated admin API, including stream definition CRUD
API::interface(
    ApiInterface::make('admin')
        ->path('api/admin')
        ->middleware(['auth:sanctum', 'can:manage-streams'])
        ->resources([StreamsResource::class, EntriesResource::class])
);
```

Register interfaces in a service provider's `boot()` method, not inside a route group (see [Installation](/docs/api/installation)).

## 3. Resource and endpoint middleware

For finer control, subclass a resource and set its middleware:

```php
use Streams\Api\Resources\EntriesResource;

class ProtectedEntriesResource extends EntriesResource
{
    protected static string|array $middleware = ['auth:sanctum'];
}
```

Endpoints built with the endpoint builder also accept `routeMiddleware()` and `withoutRouteMiddleware()`.

## Sanctum example

```php
// config/streams/api.php
'middleware' => ['api', 'auth:sanctum'],
```

```bash
curl -H "Authorization: Bearer $TOKEN" -H "Accept: application/json" \
  https://example.com/api/streams/posts/entries
```

With the [JavaScript client](/docs/client/introduction):

```javascript
import { Client, AuthorizationMiddleware } from '@laravel-streams/api-client';

const client = new Client({
    baseURL: 'https://example.com/api',
    middlewares: [new AuthorizationMiddleware({ token })],
});
```

## Checklist before you deploy

- Every interface you register has authentication, or you've decided it is public.
- `StreamsResource` (create, update, and delete stream definitions) is only on an admin-only interface, if at all.
- `POST /streams/{stream}/query` is protected like a write endpoint. It can call criteria write methods such as `create`. See [Query endpoint](/docs/api/query-endpoint).
- Rate limiting (`throttle:...`) is in the stack.
- The gate is your own class if access depends on more than a feature flag.

## Related

- [Configuration](/docs/api/configuration)
- [Custom interfaces](/docs/api/custom-interfaces)
- [Tenancy](/docs/api/tenancy)
