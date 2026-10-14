---
title: Addons
nav_title: Addons
description: Composing packages and local development with Streams.
section: guides
category: development
package: core
order: 20
tags: [core, addons]
editorial_status: ready
status: implemented
tracking_task: 256
---

## Overview

Streams applications compose Composer packages. Your project may require Core only, or Core plus UI, API, SDK, and community addons.

Browse published packages on [Addons](/addons).

## Path repositories

For local development across packages, add path repositories in `composer.json`:

```json
{
    "repositories": [
        {
            "type": "path",
            "url": "../streams-core",
            "options": { "symlink": true }
        }
    ]
}
```

Prefer `"preferred-install": { "streams/*": "source" }` when contributing to Streams packages.

## Documentation

All package docs live on this site under `/docs/core`, `/docs/ui`, etc. Edit package reference markdown in `docs/packages/{name}/` in the streams.dev repository, for example `docs/packages/core/addons.md`. Hub guides live in `streams/data/docs/`. Package repositories keep their reference copies in `docs/`; `php artisan docs:sync` copies those pages into the site's package reference folders.

## Learn more

- [Architecture](/docs/architecture)
- [Installation](/docs/installation)
- [Packages catalog](/addons)
