---
title: Package catalog
nav_title: Package catalog
description: How packages.json powers the add-ons page and homepage cards.
section: contributing
category: this-project
package: site
order: 40
tags: [site, package, catalog]
status: ready
---

## Overview

The add-on catalog on `/addons` comes from the **packages** stream. Entries describe Streams ecosystem packages (Core, UI, API, SDK, and community addons) with Composer metadata and marketing copy.

## Stream source

`streams/packages.json` uses a **self** source — entries are stored inline in the JSON file under `data`, not as separate files:

```json
{
    "config": {
        "source": {
            "type": "self"
        }
    }
}
```

Each entry includes fields such as `name`, `type`, `enabled`, and a `composer` object with package coordinates.

## Entry types

The `type` field groups catalog entries:

| Type | Examples |
|------|----------|
| `starter` | Official starter projects |
| `example` | Demo applications |
| `database` | Database adapters |
| `client` | API clients |
| `tools` | Dev tooling |

Only entries with `enabled: true` should appear in public listings (filter in your Blade/views as needed).

## Using entries in views

Pages can query the stream in Blade:

```blade
@foreach (Streams::entries('packages')->where('enabled', true)->get() as $package)
    <h3>{{ $package->name }}</h3>
@endforeach
```

The homepage and `/addons` page include package cards that link to documentation or external repositories.

## Adding a catalog entry

1. Edit `streams/packages.json` and add an object to the `data` array.
2. Set a unique `id`, `name`, `type`, and `enabled`.
3. Add `composer` JSON with `name`, `description`, and repository URL if applicable.
4. Link to `/docs/{package}/introduction` when package docs exist on this site.

## Related

- [Addons](/docs/addons) — Composer addon discovery in Core
- [Core addons reference](/docs/core/addons)
- [Architecture](/docs/architecture)
