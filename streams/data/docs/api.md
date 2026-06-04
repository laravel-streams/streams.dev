---
sort_order: 11
title: API
description: 'When and how to add a REST API to your Streams application.'
category: advanced
status: ready
---

## When to add Streams API

Add `streams/api` when your team needs to expose stream data to SPAs, mobile apps, partners, or webhooks—without writing CRUD controllers by hand.

Keep business logic in Laravel as usual. You register API routes manually in a route group.

## Installation

```bash
composer require streams/api
```

Register routes in `routes/api.php`:

```php
Route::middleware(config('streams.api.middleware'))
    ->prefix(config('streams.api.prefix'))
    ->group(function () {
        API::routeEntries();
        API::routeStreams();
    });
```

See [API installation](/docs/api/installation).

## What you get

Standard REST endpoints under `/api/streams/{stream}/entries`. Response shape follows the Streams envelope in [Responses](/docs/api/responses)—not JSON:API.

The API is **disabled by default** until you register routes and apply middleware.

## Learn more

- [API introduction](/docs/api/introduction)
- [Routes reference](/docs/api/routes)
- [Query parameters](/docs/api/query-parameters)
- [Custom endpoints](/docs/api/custom-endpoints)
- [OpenAPI / Swagger](/docs/api/openapi)
- [JavaScript client](/docs/client/introduction)

For product paths, see [Use cases — Headless API](/docs/use-cases#headless-api-only).
