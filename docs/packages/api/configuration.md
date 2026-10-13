---
title: 'API: Configuration'
nav_title: Configuration
description: 'Every key in config/streams/api.php: enabled, prefix, middleware, interface, and the gate.'
section: packages
package: api
order: 30
tags: [api, configuration]
status: ready
---

Configuration merges into `streams.api`. Publish `config/streams/api.php` to change it (see [Installation](/docs/api/installation)).

| Key | Env | Default | Purpose |
|-----|-----|---------|---------|
| `enabled` | `STREAMS_API_ENABLED` | `false` | Read by the default gate middleware. When `false`, API routes respond with `gate_status`. |
| `prefix` | `STREAMS_API_PREFIX` | `api` | Path used by `API::routeCrud()`, `routeEntries()`, and `routeStreams()`. |
| `default_interface` | `STREAMS_API_DEFAULT_INTERFACE` | `api` | ID of the default interface. Routes on other interfaces are named `streams.api.{id}.…`. |
| `middleware` | `STREAMS_API_MIDDLEWARE` | `api` | Middleware applied first to every interface's routes. Set an array in the config file for more than one. |
| `gate_middleware` | — | `EnsureApiIsEnabled::class` | Middleware applied second to every interface's routes. Point it at your own class to decide who gets in. |
| `gate_status` | `STREAMS_API_GATE_STATUS` | `404` | Status returned when the gate denies a request. |
| `gate_message` | `STREAMS_API_GATE_MESSAGE` | `Not Found` | Message returned when the gate denies a request. |
| `gate_except` | — | `[]` | Request paths (`$request->is()` patterns) that bypass the `enabled` check. |

## Middleware order

Every route on every interface gets this stack, in order:

1. `middleware` from config (Laravel's `api` group by default)
2. `gate_middleware` from config
3. `SetUpApiInterface`, which resolves the current interface from the route name and boots it
4. The interface's own `->middleware([...])`
5. Resource middleware (`ApiResource::$middleware`) and endpoint middleware

## Multiple middleware

The env var holds a single value. `STREAMS_API_MIDDLEWARE=api,auth:sanctum` does **not** work: Laravel reads it as one middleware named `api,auth` with a `sanctum` parameter. Use an array in the published config file instead:

```php
'middleware' => ['api', 'auth:sanctum', 'throttle:api'],
```

Or keep the config minimal and add middleware per interface (see [Authentication](/docs/api/authentication)).

## Related

- [Installation](/docs/api/installation)
- [Authentication](/docs/api/authentication)
- [Custom interfaces](/docs/api/custom-interfaces)
