---
title: 'SDK: Introduction'
nav_title: Introduction
description: Dev-only Artisan generators for streams, entries, addons, schemas, and Livewire admins.
section: packages
package: sdk
order: 10
tags: [sdk, introduction]
status: ready
---

`streams/sdk` is a dev dependency. It registers Artisan generators and publishes example stream JSON. It is not part of the runtime your application serves.

Install it with Core already required:

```bash
composer require --dev streams/sdk:1.0.x-dev
```

Commands are registered only when the app is running in the console. The ones `php artisan` can see today are `make:stream`, `make:entry`, `make:addon`, `streams:schema`, `streams:livewire`, and `streams:admin`. Arguments, options, and the commands that exist in source but are not registered are in the [command reference](/docs/sdk/commands).

There is no `streams:component` or `streams:crud` command.

## What it is for

Use the SDK to write the first version of a stream file, an entry, an addon package, a JSON schema export, or a plain Livewire admin. Configured control panels belong to [Streams UI](/docs/ui/introduction), not to these generators.

`streams:livewire` and `streams:admin` are in the `1.0` branch of the package and are not on a tagged release. See [Versions and support](/docs/versions).

## What a stream file looks like

`make:stream` writes a minimal file. A stream you actually use names its fields. Field behavior is Core's, documented in [Fields](/docs/core/fields):

```json
{
    "name": "Blog Posts",
    "handle": "blog_posts",
    "fields": [
        {"handle": "id", "type": "uuid"},
        {"handle": "title", "type": "string", "required": true},
        {"handle": "slug", "type": "slug", "unique": true},
        {"handle": "author", "type": "relationship", "config": {"related": "users"}}
    ]
}
```

The SDK also publishes the example streams in its `streams/` directory (`blog_posts`, `products`, `contacts`, `files`, and `docs`) with `php artisan vendor:publish --tag=examples`.

## How to extend it

`make:entry` asks for missing fields through console inputs bound as `streams.console.inputs.{type}`. Replace that map in `config/streams/console.php` when you add a field type. Copy the defaults from `SdkServiceProvider::registerInputs()` first; a config value replaces the map rather than merging into it. Details are in the [command reference](/docs/sdk/commands).

## Related

- [Command reference](/docs/sdk/commands)
- [Core streams](/docs/core/streams)
- [Core fields](/docs/core/fields)
- [Admin panels](/docs/sdk/admin-panels)
- [Agents](/docs/agents)
