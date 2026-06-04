---
sort_order: 11
title: API
description: 'When and how to add a REST API to your Streams application.'
category: advanced
status: ready
---

## When to add Streams API

Add `streams/api` when your team needs to expose stream data to SPAs, mobile apps, partners, or webhooks—without writing CRUD controllers by hand.

Keep business logic in Laravel as usual. API registers routes from stream configuration.

## Installation

```bash
composer require streams/api
```

Publish config if you need to customize behavior:

```bash
php artisan vendor:publish --provider="Streams\\Api\\ApiServiceProvider" --tag=config
```

## What you get

Each exposed stream receives standard REST endpoints. Response shape follows the Streams API envelope documented in [Responses](/docs/api/responses)—not JSON:API.

## Learn more

- [API introduction](/docs/api/introduction)
- [Query parameters](/docs/api/query-parameters)
- [Custom endpoints](/docs/api/custom-endpoints)
- [OpenAPI / Swagger](/docs/api/openapi)
- [JavaScript client](/docs/client/introduction)

For product paths, see [Use cases — Headless API](/docs/use-cases#headless-api-only).
