---
title: Introduction
description: 'REST for streams and entries — disabled by default, manual route registration.'
sort_order: 0
status: ready
---

Streams API (`streams/api`) exposes REST endpoints for stream definitions and entries. The package ships **disabled by default** — you register routes manually in your Laravel application.

## What the API provides

- CRUD for stream definitions (`/api/streams`)
- CRUD for entries (`/api/streams/{stream}/entries`)
- POST query endpoint for complex criteria
- Consistent JSON response envelope

## What it does not provide

- Automatic route registration from config alone
- JSON:API request/response format
- A `Streams\Api\Builder` class (does not exist)
- Default authentication (apply middleware on your route group)

## Enable the API

1. `composer require streams/api`
2. Set `STREAMS_API_ENABLED=true` if you gate features on config
3. Register routes in `routes/api.php`:

```php
use Streams\Api\Support\Facades\API;

Route::middleware(config('streams.api.middleware'))
    ->prefix(config('streams.api.prefix'))
    ->group(function () {
        API::routeStreams();
        API::routeEntries();
    });
```

## Related

- [Installation](/docs/api/installation)
- [Routes](/docs/api/routes)
- [Responses](/docs/api/responses)
