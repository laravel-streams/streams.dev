---
title: Custom interfaces
description: 'ApiInterface exists; automatic registration is not enabled by default.'
sort_order: 12
status: ready
---

The API package defines `Streams\Api\Contracts\ApiInterface` for custom endpoint classes. Automatic discovery and boot registration are **commented out** in the service provider — custom interfaces require manual wiring today.

## ApiInterface

Implement the contract on classes that should handle custom API behavior, then register routes pointing to your controllers explicitly.

## Current limits

- No auto-registered custom interface map from config
- No Builder class for fluent API route definition
- Custom behavior is added through standard Laravel routes and controllers that return `ApiResponse`

## Practical approach

Extend existing entry controllers or create app controllers that use Core criteria and wrap results:

```php
use Streams\Api\ApiResponse;

public function __invoke()
{
    return (new ApiResponse())
        ->setData($data)
        ->respond();
}
```

## Related

- [Custom endpoints](/docs/api/custom-endpoints)
- [Responses](/docs/api/responses)
