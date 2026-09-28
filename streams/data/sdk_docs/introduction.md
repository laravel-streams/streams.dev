---
title: 'SDK: Introduction'
nav_title: Introduction
description: Dev-only Artisan generators and checks for streams, entries, addons, and Livewire components, plus a local MCP server for agents.
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

Commands are registered only when the app is running in the console. The ones `php artisan` can see today are `make:stream`, `make:entry`, `make:addon`, `streams:list`, `streams:validate`, `streams:schema`, and `streams:livewire`. Arguments, options, and the commands that exist in source but are not registered are in the [command reference](/docs/sdk/commands).

There is no `streams:component` or `streams:crud` command.

## What it is for

Use the SDK to write the first version of a stream file, an entry, an addon package, or a JSON schema export, to check definitions with `streams:validate`, and to generate Livewire index, form, and show components with `streams:livewire`. Configured control panels belong to [Streams UI](/docs/ui/introduction), not to these generators. `streams:admin` has been removed.

The SDK also ships a local-development [MCP server](/docs/mcp) for AI agents. Start it with `php artisan mcp:start streams`. It needs `laravel/mcp`, which needs Laravel 11.45+ or 12.41+. On Laravel 10, agents use `streams:list --json` and `streams:validate --json` instead.

The SDK is on the `1.0.x-dev` branch and has no tagged release yet. See [Versions and support](/docs/versions).

## What a stream file looks like

`make:stream` writes a minimal file. A stream you actually use names its fields. Field behavior is Core's, documented in [Fields](/docs/core/fields):

```json
{
    "$schema": "https://streams.dev/schema/streams.schema.json",
    "name": "Blog Posts",
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
- [Stream definition schema](/docs/sdk/stream-schema)
- [MCP server](/docs/mcp)
- [Agents](/docs/agents)
