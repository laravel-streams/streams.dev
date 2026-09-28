---
title: Databases
nav_title: Databases
description: Storage adapters and database-backed streams.
section: guides
category: advanced
package: core
order: 50
tags: [core, databases]
status: ready
---

## Overview

Streams is data-source agnostic. Configure each stream's `source` to use filebase files, Eloquent models, in-memory collections, or remote APIs.

Most production apps use Eloquent for transactional data and filebase for content or config-like entries.

## Eloquent source

Point a stream at an existing model:

```json
{
    "config": {
        "source": {
            "type": "eloquent",
            "model": "App\\Models\\Post"
        }
    }
}
```

Your team keeps migrations and models in Laravel; Streams adds repositories, criteria, and optional UI/API layers.

## Filebase source

Store entries as JSON, YAML, Markdown, or HTML under `streams/data/`. Ideal for content sites and git-reviewed data.

## Learn more

- [Streams Core — streams](/docs/core/streams)
- [Repositories](/docs/core/repositories)
- [Files](/docs/files)
