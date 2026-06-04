---
id: configuration
sort_order: 2
status: published
title: Configuration
description: Configure the Streams API to match your application's needs.
---

# Configuration

The Streams API can be configured through environment variables or by publishing the configuration file.

## Publishing Configuration

To publish the configuration file:

```bash
php artisan vendor:publish --provider=Streams\\Api\\ApiServiceProvider --tag=config
```

This creates `config/streams/api.php` in your application.

## Configuration Options

### Enable/Disable API

Control whether the API is enabled:

```php
// config/streams/api.php
'enabled' => env('STREAMS_API_ENABLED', false),
```

```env
# .env
STREAMS_API_ENABLED=true
```

**Important**: The API is disabled by default for security. All endpoints are public unless you add authentication middleware.

### API Prefix

Configure the base URL prefix for all API routes:

```php
'prefix' => env('STREAMS_API_PREFIX', 'api'),
```

```env
# .env
STREAMS_API_PREFIX=api/v1
```

With this setting, endpoints become `/api/v1/streams`, `/api/v1/streams/{stream}/entries`, etc.

### Middleware

Apply middleware to all API routes:

```php
'middleware' => env('STREAMS_API_MIDDLEWARE', 'api'),
```

```env
# .env
STREAMS_API_MIDDLEWARE=api,auth:sanctum
```

This applies Laravel's default `api` middleware group. You can add authentication, rate limiting, or custom middleware:

```php
'middleware' => ['api', 'auth:sanctum', 'throttle:60,1'],
```

## Service Provider Configuration

The API is registered via `ApiServiceProvider`. It automatically:

- Registers console commands (`api:schema`, `api:documentation`)
- Sets up the API manager and facade
- Configures route registration

## Enabling Default Routes

To use the default API endpoints, call the route registration methods in your `RouteServiceProvider` or `AppServiceProvider`:

```php
use Streams\Api\Support\Facades\API;

public function boot()
{
    API::routeEntries();  // Registers entry endpoints
    API::routeStreams();   // Registers stream management endpoints
}
```

## Environment Variables Reference

| Variable | Default | Description |
|----------|---------|-------------|
| `STREAMS_API_ENABLED` | `false` | Enable/disable the API |
| `STREAMS_API_PREFIX` | `api` | Base URL prefix |
| `STREAMS_API_MIDDLEWARE` | `api` | Middleware group(s) |

## Next Steps

- [Query Parameters](query-parameters) - Learn about filtering and pagination
- [Custom Interfaces](custom-interfaces) - Create multiple API configurations
- [Custom Endpoints](custom-endpoints) - Add custom routes to your API
