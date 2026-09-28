---
title: 'API: Caching'
nav_title: Caching
description: 'HTTP caching with the ApiCache middleware: ETags, Cache-Control, and per-stream opt-out.'
section: packages
package: api
order: 170
tags: [api, caching]
status: ready
---

`Streams\Api\Http\Middleware\ApiCache` adds HTTP caching to API responses: an `ETag`, public `Cache-Control` headers, and `304 Not Modified` for clients that already have the current version. It is **not applied by default**. Add it to the interfaces that should use it.

Separately, `ApiResponse` adds a `Last-Modified` header on cacheable (GET/HEAD) requests when the response data has a `lastModified()` method.

## Why

Stream definitions and most entry lists change far less often than they are read. ETags let clients and CDNs skip downloading unchanged data, and the optional server-side cache saves re-running criteria for clients that ask for it.

## Enable it

```php
use Streams\Api\ApiInterface;
use Streams\Api\Http\Middleware\ApiCache;
use Streams\Api\Resources\EntriesResource;
use Streams\Api\Support\Facades\API;

API::interface(
    ApiInterface::make('api')
        ->path('api')
        ->middleware([ApiCache::class])
        ->resources([EntriesResource::class])
);
```

## How it behaves

For each request, the middleware:

1. **Skips** non-cacheable methods (anything but GET and HEAD), requests with `Cache-Control: no-cache` or `Pragma: no-cache`, routes with no `{stream}` parameter (such as `GET /api/streams`), and streams with `config.cache.enabled` set to `false`.
2. If the request sends `Cache-Control: max-age=N`, **caches the whole response server-side** for `N` seconds in the stream's cache store, keyed on URL, method, body, and input. The response is returned without ETag headers.
3. Otherwise, runs the request and sets:
   - `ETag` to a quoted MD5 of the response body
   - `Cache-Control: public, max-age=TTL, s-maxage=TTL`, where TTL is the stream's `config.cache.ttl` (default 3600 seconds)
4. If the request's `If-None-Match` matches the ETag, responds `304 Not Modified` with no body.

## Configure per stream

The middleware reads the same `cache` block as Core's [query cache](/docs/core/caching):

```json
{
    "config": {
        "cache": {
            "enabled": true,
            "ttl": 600,
            "store": "redis"
        }
    }
}
```

| Key | Effect on `ApiCache` |
|-----|----------------------|
| `enabled` | `false` turns HTTP caching off for this stream. Any other value (including unset) leaves it on. |
| `ttl` | `max-age` and `s-maxage` in seconds. Default `3600`. |
| `store` | Laravel cache store for the `max-age` server-side cache. Default is your app's default store. |

## Things to know

- Responses are marked `public`. Don't put `ApiCache` on interfaces that return per-user data behind shared caches or CDNs unless you also vary the cache key (for example with a `Vary: Authorization` header in your own middleware).
- The ETag is computed after the response is built, so a 304 saves bandwidth, not server work.
- The [JavaScript client](/docs/client/introduction) exports an `ETagMiddleware`, but in 3.0.0 it is a placeholder and does not send `If-None-Match` yet.

## Related

- [Core caching](/docs/core/caching)
- [Caching guide](/docs/caching)
- [Custom interfaces](/docs/api/custom-interfaces)
