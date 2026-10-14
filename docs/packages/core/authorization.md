---
title: 'Core: Authorization gates'
nav_title: Authorization gates
description: Register Laravel gates and a policy class from a stream definition.
section: packages
package: core
order: 90
tags: [core, authorization]
status: implemented
tracking_task: 255
---

Register Laravel authorization from a stream when that stream is registered. Core reads `config.gates`, a top-level `gates` map, and a top-level `policy` class.

## Stream policy

Set `policy` to a policy class. Core registers `Streams\Core\Stream\StreamPolicy` with `Gate::policy()` for the stream entry class in `config.abstract`. When `config.abstract` is omitted, the entry class is `Streams\Core\Entry\Entry`.

```json
{
    "policy": "App\\Contacts\\ContactPolicy",
    "config": {
        "abstract": "App\\Contacts\\Contact"
    }
}
```

The dispatcher reads the policy from the entry’s own stream when checking an ability. Streams can share an entry class and still use different policy classes. Checks require an entry instance; class-only checks are denied. Laravel’s policy `before` and guest rules apply.

## Stream config gates

`config.gates` uses the same map as `Integrator::policies()`. A key that names a class registers a model policy. Any other key registers a gate ability. A top-level `gates` map is used only when `config.gates` is absent. `Integrator::gates()` registers the same map from an integration payload.

```json
{
    "config": {
        "gates": {
            "contacts.review": "App\\Contacts\\ContactPolicy@review"
        }
    }
}
```

## Related

- [Streams](/docs/streams)
- [Integrator](/docs/core/integrator)
