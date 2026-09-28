---
sort_order: 11
title: API
description: 'When and how to add a REST API to your Streams application.'
category: advanced
status: ready
---

## When to add Streams API

Add `streams/api` when your team needs to expose stream data to SPAs, mobile apps, partners, or webhooks—without writing CRUD controllers by hand.

Keep business logic in Laravel as usual. The API reads your stream definitions at request time, so new fields show up without new controllers.

## Installation

```bash
composer require streams/api:1.0.x-dev
```

Enable the gate and register routes from a service provider (not from `routes/api.php`):

```env
STREAMS_API_ENABLED=true
```

```php
// app/Providers/AppServiceProvider.php
use Streams\Api\Support\Facades\API;

public function boot(): void
{
    API::routeCrud();
}
```

See [API installation](/docs/api/installation).

## What you get

Standard REST endpoints under `/api/streams/{stream}/entries`. Response shape follows the Streams envelope in [Responses](/docs/api/responses), not JSON:API.

## What you own

- **Authentication.** The API ships none. Add middleware to the interface or extend the gate. See [API authentication](/docs/api/authentication).
- **Tenancy.** Resolve a tenant with `API::tenant()` and scope criteria with endpoint callbacks. See [API tenancy](/docs/api/tenancy).
- **Caching.** Opt in to ETags with the `ApiCache` middleware. See [API caching](/docs/api/caching).

## Learn more

- [API introduction](/docs/api/introduction)
- [Routes reference](/docs/api/routes)
- [Query parameters](/docs/api/query-parameters)
- [Custom interfaces](/docs/api/custom-interfaces)
- [Custom endpoints](/docs/api/custom-endpoints)
- [OpenAPI / Swagger](/docs/api/openapi)
- [JavaScript client](/docs/client/introduction)

For product paths, see [Use cases — Headless API](/docs/use-cases#headless-api-only).
