---
title: Middleware System
nav_title: Middleware System
description: Request, response and error hooks, including ETag revalidation.
section: packages
package: client
order: 80
tags: [client, middleware]
editorial_status: ready
status: implemented
tracking_task: 265
---

Middleware intercepts requests, responses, and errors. Extend `Middleware` and implement any of its `request`, `response`, or `error` methods. Return the target object from each hook.

## Default pipeline

The client installs these defaults:

- `CriteriaMiddleware`: converts supported criteria into API `where[field]`, `where[field][operator]`, and `order_by[field]` parameters.
- `RequestDataMiddleware`: serializes the `data` option as JSON and sets `Content-Type: application/json` unless supplied.
- `QueryMiddleware`: appends bracket query parameters and encodes values, preserving any existing query string.
- `ResponseDataMiddleware`: parses the configured response type. JSON API envelopes are exposed as `data`, `meta`, `links`, and `errors`, with the complete parsed object on `envelope`. Generic JSON remains in `data`; empty 204 responses have null data.

Use `body: new FormData()` or `body: new URLSearchParams()` for native form requests. Leave the multipart Content-Type header unset so fetch supplies its boundary. Do not pass form bodies as `data`, which is the JSON option.

## Registering middleware

```javascript
import { Client, AuthorizationMiddleware, ETagMiddleware } from '@laravel-streams/api-client';

const auth = new AuthorizationMiddleware({ token: 'your-api-token' });
const client = new Client({
    baseURL: 'https://api.example.com/api',
    middlewares: [auth, new ETagMiddleware()],
});

auth.setToken('new-token');
console.log(client.middlewares);
```

`client.use(instance)` adds a middleware. `defaultMiddlewares` can replace the defaults explicitly; setting it to an empty array disables automatic request and response transformations.

## ETag caching

ETag middleware is opt-in. It keeps an in-memory response cache scoped to the client. GET cache keys include the complete URL, response type, credentials mode and request headers, keeping query results and authenticated representations separate.

```javascript
const first = await client.entries.find('posts', 1);
// A successful response with ETag: "v1" is cached.

const second = await client.entries.find('posts', 1);
// Sends If-None-Match: "v1".
// A matching 304 replays the cached response through response parsing.
console.log(second.data);
console.log(second.revalidated); // true when a 304 replay occurred
```

A replay has the cached success status, a fresh body and the current request attached; headers from the 304 update its headers. Modifying returned data does not alter cached data. A 304 without a matching cached representation remains an HTTP error. Successful writes clear that client's cache. Responses with `Cache-Control: no-store` or `Vary: *` and requests with `cache: 'no-store'` are not cached. The cache has no persistence, TTL or offline mode.

## Custom hooks and priorities

```javascript
import { Middleware } from '@laravel-streams/api-client';

class LoggingMiddleware extends Middleware {
    async request(request, client) {
        console.log('Request:', request.method, request.url);
        return request;
    }

    async response(response, client) {
        console.log('Response:', response.status, response.data);
        return response;
    }

    async error(error, client) {
        console.error('Request failed:', error.status, error.response?.errors);
        return error;
    }
}

client.use(new LoggingMiddleware());
```

Hooks run in ascending numeric priority; equal priorities retain registration order. This applies separately to requests, responses, and errors. There is no `handle(request, next)` hook.

The default priority is 50. Criteria requests run at 40; ETag requests run at 90 after query serialization. ETag responses run at 10 before parsing; response data parsing runs at 40 before ordinary custom response hooks.

```javascript
const logger = new LoggingMiddleware().priority(60, 'response');
client.use(logger);
// logger.options.priority.response is 60
```

`priority(value)` sets all three hook priorities. HTTP failures are parsed by response hooks before `FetchError` reaches error hooks; returning an error from the error hook preserves rejection.

## Related

- [Client introduction](/docs/client/introduction)
- [Criteria](/docs/client/criteria)
- [Examples](/docs/client/examples)
