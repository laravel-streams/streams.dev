---
title: Configuration
description: 'enabled, prefix, and middleware in config/streams/api.php.'
sort_order: 2
status: ready
---

API configuration is minimal today. All keys live in `config/streams/api.php`.

| Key | Env | Default | Purpose |
|-----|-----|---------|---------|
| `enabled` | `STREAMS_API_ENABLED` | `false` | Feature flag (routes still require manual registration) |
| `prefix` | `STREAMS_API_PREFIX` | `api` | URL prefix for route group |
| `middleware` | `STREAMS_API_MIDDLEWARE` | `api` | Middleware stack for route group |

## Example

```php
return [
    'enabled' => env('STREAMS_API_ENABLED', false),
    'prefix' => env('STREAMS_API_PREFIX', 'api'),
    'middleware' => env('STREAMS_API_MIDDLEWARE', 'api'),
];
```

Wrap Sanctum or custom auth middleware in your route group for protected deployments. See [Authentication](/docs/api/authentication).

## Related

- [Installation](/docs/api/installation)
- [Routes](/docs/api/routes)
