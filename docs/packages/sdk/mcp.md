---
title: 'SDK: MCP server'
nav_title: MCP server
description: 'Give an AI agent typed tools instead of grep: the streams/sdk MCP server, 16 tools over stdio, read-only mode, and how to test it.'
section: packages
package: sdk
order: 25
tags: [sdk, mcp, agents, sallt]
editorial_status: ready
status: implemented
tracking_task: null
---

The SDK ships a local [Model Context Protocol](https://modelcontextprotocol.io) server so an AI agent can work on your streams through typed tools instead of grepping your `vendor/` directory. It is registered by `SdkServiceProvider`, runs over stdio, and is dev-only unless you opt in.

Start it:

```bash
php artisan mcp:start streams
```

That single command is the whole integration. Your agent talks JSON-RPC to the process; you keep your data in your app, with your validations, policies, and field rules.

`laravel/mcp` needs Laravel 11.45+ or 12.41+. On Laravel 10 the MCP server is unavailable; use the [TUI](/docs/sdk/commands#streamstui) and the CLI commands instead.

## Install

```bash
composer require streams/sdk --dev
composer require laravel/mcp --dev
```

Nothing else to publish. The server registers itself when the app boots in a non-production environment.

## Point an agent at it

The server is a stdio process, so a client launches it. For Claude Desktop, Cursor, or any MCP client that takes a command:

```json
{
  "mcpServers": {
    "streams": {
      "command": "php",
      "args": ["artisan", "mcp:start", "streams"],
      "cwd": "/absolute/path/to/your/app"
    }
  }
}
```

Then ask something ordinary: *"what streams does this app have, and what would I need to add a comment to a post?"* The agent calls `streams-status` and `describe-stream` rather than inventing a schema.

## Configuration

Keys live under `streams.sdk.mcp` (from `config/sdk.php` in this package). Copy the file to `config/streams/sdk.php` to override.

| Key | Env | Default | What it does |
|---|---|---|---|
| `enabled` | `STREAMS_MCP_ENABLED` | `true` | Register the server at all. |
| `allow_production` | `STREAMS_MCP_ALLOW_PRODUCTION` | `false` | Allow registration when `APP_ENV=production`. The server acts with your app's full permissions, so leave this off. |
| `handle` | `STREAMS_MCP_HANDLE` | `streams` | The handle in `mcp:start {handle}`. |
| `read_only` | `STREAMS_MCP_READ_ONLY` | `false` | Hide every write tool. Agents keep read, schema, and validate access. |
| `docs.paths` | — | `docs`, `vendor/streams/*/docs`, `streams/data/*docs` | Where `search-docs` and `read-doc` look, relative to the app root. Local files only, no network. |

## Tools

Handles are the kebab-case class names under `Streams\Sdk\Mcp\Tools`. Grouped by what you are doing.

**Orient — start here.**

| Tool | What you get |
|---|---|
| `streams-status` | Installed packages, registered streams, the definitions directory, project kind, and a ranked `next_steps` list. |
| `how-to` | A numbered playbook (`add-a-stream`, `add-a-field`, `add-an-entry`, `build-a-panel`, `serve-the-api`, `add-tenancy`, `onboard-an-app`) with the commands and tool calls for each step. |

**Read — safe anywhere.**

| Tool | What you get |
|---|---|
| `list-streams` | Every stream with namespace, description, and field count. |
| `describe-stream` | One stream in full: fields, types, required/unique/primary, config, and rules. |
| `definition-schema` | The JSON Schema a stream definition must satisfy. |
| `entry-schema` | A generated validation schema plus a fillable example entry for one stream. |
| `list-entries` | A page of entries with a projection (protected fields excluded, long values truncated). |
| `read-entry` | One entry by id or key field. Protected fields are never returned. |
| `search-docs` | Keyword search over the local docs index. |
| `read-doc` | One doc page by id. |

**Write — validated, and hidden when `read_only` is true.**

| Tool | What you get |
|---|---|
| `validate-stream-definition` | Errors and warnings for a definition, without touching disk. Call this before you write. |
| `create-entry` / `update-entry` / `delete-entry` | Entries through the repository and criteria layer, so field rules, defaults, and validation run. `delete-entry` is marked destructive. |
| `make-stream` | Writes `streams/{handle}.json` after the definition validates. |
| `make-addon` | Scaffolds an addon (`composer.json` + service provider) and reports what it wrote. |

Two things are also exposed: the `design-stream` prompt walks you through a stream from a plain-language description before you generate it, and two resources — `streams://schemas/streams.schema.json` and `streams://streams/{id}`.

## A first session

```text
you   > I want a blog with posts and tags.

agent > streams-status        → streams/core, streams/ui installed; no streams yet
        design-stream         → fields, relations, and the definition draft
        validate-stream-definition → 0 errors
        make-stream           → streams/posts.json written
        how-to(add-a-field)   → tag field next, then a relations field
```

Nothing in that transcript came from the agent guessing your field types. `validate-stream-definition` is the gate, and `make-stream` refuses to write a definition that fails it.

## Testing your own tools

Call a tool through the server class with the `laravel/mcp` test helpers — no transport, no process:

```php
use Streams\Sdk\Mcp\StreamsServer;
use Streams\Sdk\Mcp\Tools\DescribeStream;

StreamsServer::tool(DescribeStream::class, ['stream' => 'posts'])
    ->assertOk()
    ->assertStructuredContent(function ($content) {
        $content->where('id', 'posts')
            ->where('fields.title.required', true)
            ->etc();
    });

StreamsServer::tool(DescribeStream::class, ['stream' => 'nope'])
    ->assertHasErrors(['is not registered']);
```

`streams/sdk`'s own server is exercised end to end this way in `streams.dev/tests/Feature/StreamsSdkMcpTest.php`; copy that file into your app to test the tools you add.

## Related

- [MCP server reference](/docs/mcp) — the full hub page, including the streams.dev docs server.
- [Commands](/docs/sdk/commands) — the same surface as Artisan commands and the `streams:tui` menu.
- [AI prompts](/docs/sdk/ai-prompts) — prompts that map onto these tools.
