---
title: MCP server
nav_title: MCP server
description: 'The local-development MCP server in streams/sdk: requirements, starting it with mcp:start streams, client setup, tools, resources, the design-stream prompt, and configuration.'
section: guides
category: development
package: sdk
order: 60
tags: [sdk, mcp, agents, tools]
status: ready
---

The SDK includes a local-development [Model Context Protocol](https://modelcontextprotocol.io) server. MCP is the standard way AI agents call tools. With it, an agent working in your app can see the domain model (streams), read and write entries, check the stream definitions it writes, search the Streams docs, and run the SDK generators, all without guessing from source files.

The server runs over stdio on your machine through Artisan. It is not an HTTP endpoint.

## Requirements

- `streams/sdk` installed as a dev dependency (`composer require --dev streams/sdk:1.0.x-dev`).
- [`laravel/mcp`](https://github.com/laravel/mcp) `^1.0`, which needs Laravel 11.45+ or 12.41+.

```bash
composer require --dev laravel/mcp
```

On Laravel 10 the MCP server is not available and is skipped automatically. Agents can still use the CLI equivalents: `php artisan streams:list --json` and `php artisan streams:validate --json`. See the [version matrix](/docs/versions#version-matrix).

## Start the server

```bash
php artisan mcp:start streams
```

You won't normally run this yourself. Your agent client starts it. To try the tools interactively, use the MCP Inspector that ships with `laravel/mcp`:

```bash
php artisan mcp:inspector streams
```

## Connect an agent

Register the server with each client from the app root. Use an absolute path to `artisan` if the client does not start servers in the project directory.

**Claude Code** (writes to `.mcp.json` with `--scope project`, so the team shares it):

```bash
claude mcp add --scope project streams -- php artisan mcp:start streams
```

Or add it to `.mcp.json` yourself:

```json
{
    "mcpServers": {
        "streams": {
            "command": "php",
            "args": ["artisan", "mcp:start", "streams"]
        }
    }
}
```

**Cursor** (`.cursor/mcp.json`) uses the same `mcpServers` shape as `.mcp.json`.

**VS Code** (`.vscode/mcp.json`):

```json
{
    "servers": {
        "streams": {
            "type": "stdio",
            "command": "php",
            "args": ["artisan", "mcp:start", "streams"]
        }
    }
}
```

**Codex** (`~/.codex/config.toml`):

```toml
[mcp_servers.streams]
command = "php"
args = ["/absolute/path/to/app/artisan", "mcp:start", "streams"]
```

## Tools

| Tool | What it does | CLI equivalent |
|------|--------------|----------------|
| `list-streams` | Registered streams with ID, name, description, source, and field handles. | `streams:list --json` |
| `describe-stream` | One stream's source, key, routes, definition file, original definition, and fields with resolved rules and config. | |
| `entry-schema` | JSON Schema for a stream's entries. | `streams:schema` |
| `definition-schema` | JSON Schema for `streams/*.json` definition files. | |
| `validate-stream-definition` | Validate a draft definition, `streams/{id}.json`, or every definition. | `streams:validate --json` |
| `list-entries` | Query entries with `where` constraints, ordering, and pagination. | |
| `read-entry` | Read an entry by key. | |
| `create-entry` | Create a validated entry. Fails if the key exists. | `make:entry` |
| `update-entry` | Update some attributes; the merged entry is validated. | |
| `delete-entry` | Delete an entry by key. Marked destructive. | |
| `search-docs` | Keyword search over local Streams docs. | |
| `read-doc` | Read a doc returned by `search-docs`. | |
| `make-stream` | Write a validated definition to `streams/{id}.json` and register it. | `make:stream {id} [--force]` |
| `make-addon` | Scaffold an addon package in `addons/{vendor}/{name}`. | `make:addon` |

Tools annotate themselves as read-only, idempotent, or destructive, so clients can auto-approve safe calls and ask before writes.

Protected fields (`"protected": true`) are left out of entry output.

### Resources and prompts

- `streams://schemas/streams.schema.json`: the [stream definition JSON Schema](/docs/sdk/stream-schema) (also served at [/schema/streams.schema.json](/schema/streams.schema.json)).
- `streams://streams/{id}`: a stream's description (same as `describe-stream`).
- `design-stream` prompt: walks an agent through modeling a new stream (inspect, draft, validate, create, seed).

## Docs search

`search-docs` indexes markdown under these paths, relative to the app root:

- `docs`
- `vendor/streams/*/docs` (docs shipped with installed Streams packages)
- `streams/data/*docs` (docs streams, as on streams.dev)

Change the list with `streams.sdk.mcp.docs.paths`. The index is built from local files only; the server makes no network requests. To search these docs from outside an app, use [/llms.txt](/llms.txt) and the raw `.md` pages described in [Agents](/docs/agents).

## Configuration

Publish the config to change defaults:

```bash
php artisan vendor:publish --tag=streams-sdk-config
```

| Key | Env | Default | Description |
|-----|-----|---------|-------------|
| `streams.sdk.mcp.enabled` | `STREAMS_MCP_ENABLED` | `true` | Register the server. |
| `streams.sdk.mcp.allow_production` | `STREAMS_MCP_ALLOW_PRODUCTION` | `false` | Also register when `APP_ENV=production`. |
| `streams.sdk.mcp.handle` | `STREAMS_MCP_HANDLE` | `streams` | Handle passed to `mcp:start`. |
| `streams.sdk.mcp.read_only` | `STREAMS_MCP_READ_ONLY` | `false` | Hide `create-entry`, `update-entry`, `delete-entry`, `make-stream`, and `make-addon`. |
| `streams.sdk.mcp.docs.paths` | | see above | Glob patterns for docs search. |

## Safety

- The server is for local development. It is not registered in production unless you opt in, and the SDK should be a dev dependency anyway.
- It acts with the full permissions of your app. Entry tools write to whatever source a stream uses (flat files in `streams/data` by default, or your database).
- Flat-file writes land in your working tree, so review them with `git diff` like any other change.
- Use `STREAMS_MCP_READ_ONLY=true` when you only want an agent to look.

## Troubleshooting

- **"MCP Server with name [streams] not found"**: `laravel/mcp` is missing, the app is in production, or `STREAMS_MCP_ENABLED=false`.
- **The client reports invalid JSON**: something in the app wrote to stdout during boot (for example `echo`, `dump`, or `dd` in a service provider). stdout carries the protocol, so send debugging output to the log.
- **A new stream is missing**: streams are registered at boot. `make-stream` registers what it creates, but if you add a definition by hand, restart the server (most clients have a reconnect command).

## Related

- [Agents](/docs/agents)
- [Command reference](/docs/sdk/commands)
- [Stream definition schema](/docs/sdk/stream-schema)
- [Workflows](/docs/workflows)
