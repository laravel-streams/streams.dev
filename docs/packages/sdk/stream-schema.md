---
title: Stream definition schema
nav_title: Stream definition schema
description: 'The JSON Schema for streams/*.json, served at /schema/streams.schema.json: what it checks, editor setup, and validating with streams:validate.'
section: packages
package: sdk
order: 30
tags: [sdk, schema, validation, json]
status: ready
---

Stream definitions are the JSON files in `streams/` (for example `streams/posts.json`). `streams/sdk` ships a [JSON Schema](https://json-schema.org) (draft-07) for them in `vendor/streams/sdk/resources/schemas/streams.schema.json`, and this site serves the same file at:

```text
https://streams.dev/schema/streams.schema.json
```

That URL is the schema's `$id`. It is also the `$schema` that `make:stream` and the `make-stream` MCP tool write into new definitions, so editors that follow `$schema` validate and autocomplete stream files with no setup.

## What it checks

- Top-level keys: `id`, `name`, `description`, `extends`, `config`, `fields`, `rules`, `route`, `routes`, `data`, `ui`.
- `config.source`: known source types (`filebase`, `file`, `self`, `database`, `eloquent`, `filesystem`, `collection`, `elasticsearch`, `opensearch`, or an adapter class) and formats (`json`, `yaml`, `md`, `html`, `tpl`, `csv`).
- Both field forms: a list of field objects with `handle`, or an object keyed by handle whose values are a field object or a type string.
- Imports: the whole `fields` value, or any single field, can be `"@path/to/file.json"`. Core replaces it with the decoded file, relative to the app root, when it builds the stream.
- Field types: the core types are offered for completion. Other lowercase names are allowed because apps and addons can register their own types.
- Type-specific config: `relationship` fields need `config.related` (one stream ID, or a list of IDs for polymorphic and multi-target fields). `select`, `enum`, and `multiselect` fields need `config.options`. `object` fields list `config.allowed` as `{"stream": ...}`, `{"generic": ...}`, or `{"prototype": ...}` objects. `eloquent` sources need `config.source.model`.
- `data`: inline entries for a `self` source.
- `input` on a field: a form hint for Streams UI. Core does not read it.

Unknown top-level keys are allowed because addons extend definitions.

Two shapes show up in older examples and are ignored by Core. `streams:validate` reports both:

- `"related"` next to `"type"`. The value has to be `config.related`.
- `"default"` next to `"type"`. The value has to be `config.default` (for a `uuid` field, `"default": true` generates a UUID).

## Validate from the command line

```bash
php artisan streams:validate                       # every streams/*.json
php artisan streams:validate streams/posts.json    # specific files
php artisan streams:validate --json                # machine-readable output
```

The command exits non-zero if any file is invalid. Besides the schema, it checks the running app:

- field types are registered,
- `extends` streams exist,
- `config.related` streams exist (a warning, since you may be creating them together),
- `@` imports resolve (a warning),
- model and adapter classes exist, and
- Core can build the definition.

Agents connected to the [MCP server](/docs/mcp) get the same checks from `validate-stream-definition`, and the schema from `definition-schema` or the `streams://schemas/streams.schema.json` resource. See the [command reference](/docs/sdk/commands#streamsvalidate).

## Use it in your editor

Add `$schema` to a definition:

```json
{
    "$schema": "https://streams.dev/schema/streams.schema.json",
    "name": "Posts",
    "fields": []
}
```

To validate offline, or against the exact version you have installed, map stream files to the local copy. In VS Code, add this to `.vscode/settings.json`:

```json
{
    "json.schemas": [
        {
            "fileMatch": ["**/streams/*.json"],
            "url": "./vendor/streams/sdk/resources/schemas/streams.schema.json"
        }
    ]
}
```

## Related

- [Command reference](/docs/sdk/commands)
- [MCP server](/docs/mcp)
- [Core streams](/docs/core/streams)
- [Core fields](/docs/core/fields)
