---
title: Introduction
description: 'A criteria-scoped REST API for streams and entries. You opt in to routes and own authentication.'
sort_order: 0
status: ready
---

Streams API (`streams/api`) turns your stream definitions into a REST API. It builds JSON responses for stream definitions and entries from the same [criteria](/docs/core/criteria) you use in PHP, so an endpoint returns exactly what `Streams::entries('posts')->where(...)->get()` would.

## What it is

- **Resources and endpoints.** `StreamsResource` (list, show, create, update, patch, delete) and `EntriesResource` (list, show, create, update, patch, delete, query).
- **Interfaces.** An `ApiInterface` groups resources under a path, domain, and middleware stack. You can register several (for example `v1` and `admin`).
- **A consistent envelope.** Every response has `data`, `meta`, `links`, and `errors` keys. See [Responses](/docs/api/responses).
- **Self-describing.** Responses carry a `self` link and `meta` (the query, payload, route parameters, and stream handle), and `GET /api/streams` returns the stream definitions themselves, which is what lets the [JavaScript client](/docs/client/introduction) discover the API.
- **Optional HTTP caching** through the `ApiCache` middleware (ETags and `Cache-Control`). See [Caching](/docs/api/caching).
- **Schema commands.** `php artisan api:schema` and `php artisan api:documentation` for OpenAPI output. See [OpenAPI](/docs/api/openapi).

## Why it exists

Streams already knows the shape of your data: fields, types, validation rules, and relationships live in `streams/*.json`. Writing CRUD controllers by hand repeats that knowledge and drifts from it. The API reads the stream definition at request time, so adding a field to a stream adds it to the API with no controller changes.

## What it does not do

- **It does not authenticate anyone.** The package has no users, tokens, or policies. Your application owns the middleware. See [Authentication](/docs/api/authentication).
- **It does not mount routes on its own.** Nothing is routed until you register an interface.
- **It is not JSON:API.** Request bodies are flat field maps. See [Requests](/docs/api/requests).

## How to use it

```bash
composer require streams/api:1.0.x-dev
```

```env
STREAMS_API_ENABLED=true
```

```php
// app/Providers/AppServiceProvider.php
use Streams\Api\Support\Facades\API;

public function boot(): void
{
    API::routeCrud(); // streams + entries under /api
}
```

`GET /api/streams/posts/entries` now lists entries. Protect it before you deploy: see [Authentication](/docs/api/authentication).

## How to extend it

- Register more interfaces with their own paths and middleware: [Custom interfaces](/docs/api/custom-interfaces).
- Add endpoints and resources: [Custom endpoints](/docs/api/custom-endpoints).
- Resolve a tenant per request: [Tenancy](/docs/api/tenancy).

## Related

- [Installation](/docs/api/installation)
- [Routes](/docs/api/routes)
- [Responses](/docs/api/responses)
