---
title: Addons
description: 'streams-addon Composer discovery and the Addons facade.'
sort_order: 15
status: ready
---

Addons are Composer packages tagged `streams-addon` that register streams, routes, and config through Core's Integrator at boot.

## Composer discovery

Add to your addon's `composer.json`:

```json
{
    "extra": {
        "laravel": {
            "providers": ["MyVendor\\MyAddon\\ServiceProvider"]
        },
        "streams": {
            "addon": true
        }
    }
}
```

The addon service provider typically calls `Integrator::integrate()` with streams and bindings.

## Addons facade

```php
use Streams\Core\Support\Facades\Addons;

Addons::register('my-addon', $paths);
```

Use the facade to inspect registered addon paths and assets.

## Related

- [Integrator](/docs/core/integrator)
- [Hub: Addons](/docs/addons)
