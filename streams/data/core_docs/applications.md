---
title: Applications
description: 'Multi-app URL matching, active application, and Integrator merge keys.'
sort_order: 13
status: ready
---

Applications are entries in the stream identified by `config('streams.core.applications_id')` (default `applications`). They enable multi-tenant or multi-site setups where each application merges its own config, streams, and routes at boot.

## Application entries

Each application entry defines:

- URL **match** patterns for request detection
- Optional locale, config overrides, stream definitions, bindings, routes

Core activates the matching application via `Streams\Core\Support\Facades\Applications`.

## Activation flow

1. `StreamsServiceProvider` loads the applications stream
2. `ApplicationManager` matches the incoming request URL
3. `Applications::activate()` sets the active application
4. `Integrator::integrate()` merges the application's configuration

## Accessing the active application

```php
Applications::active();
Applications::activate($applicationEntry);
```

Application entries extend `Streams\Core\Application\Application` (subclass of `Entry`).

## Related

- [Integrator](/docs/core/integrator)
- [Routes](/docs/core/routes)
