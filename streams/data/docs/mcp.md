---
title: MCP
nav_title: MCP
description: The Streams MCP server is not shipped. These are the tools it is planned to expose, and the Artisan commands that exist today.
section: guides
category: development
package: sdk
order: 60
tags: [sdk, mcp]
status: ready
---

There is no MCP server in `streams/core`, `streams/sdk`, or this site. A local-development server is planned as a `require-dev` addition to `streams/sdk` (separate work, not in the packages this site documents). Until that lands, agents should use [/llms.txt](/llms.txt), the raw `.md` pages, and [/docs/api/openapi.yaml](/docs/api/openapi.yaml). See [Agents](/docs/agents).

## Planned tools

The server is expected to stay on the machine that runs the Laravel app and to expose tools that map onto SDK commands:

| Planned tool | Command class in `streams/sdk` today | Registered with Artisan? |
|--------------|--------------------------------------|--------------------------|
| List streams | `streams:list` (`ListStreams`) | No. The registration line is commented out. |
| Describe a stream | `streams:describe` (`DescribeStream`) | No. Commented out. |
| Stream JSON Schema | `streams:schema` (`StreamsSchema`) | Yes. |
| Entry create, read, update, delete | `make:entry`, `entries:list`, `entries:show` | `make:entry` is registered. The list and show commands are not. There is no `entries:update` or `entries:delete` command. |
| Docs search | This site's `/search/docs.json` | Not an Artisan command. |
| Generators | `make:stream`, `make:entry`, `make:addon` | Yes. |

`streams:tap` has a command class and an empty handler, and it is not registered. Do not treat it as a working tool.

Entry reads and writes that the MCP server would add on top of Artisan are not implemented. The HTTP API already does entry CRUD when you install `streams/api` and register an interface. See [Entry endpoints](/docs/api/entry-endpoints) and [Authentication](/docs/api/authentication).

## Why it is separate from the SDK commands

The generators are for a developer at a terminal. An MCP server would call the same operations from an agent without scraping HTML. Shipping it inside `streams/sdk` as `require-dev` keeps it off production installs.

## What you can do now

```text
GET /llms.txt
GET /llms-full.txt
GET /docs/core/streams.md
GET /search/docs.json
GET /docs/api/openapi.yaml
```

Inside an app that has the SDK installed:

```bash
php artisan make:stream posts
php artisan make:entry posts "title=Hello"
php artisan streams:schema --path=storage/schemas
```

Create `storage/schemas` first. `streams:schema` does not create the directory.

## Related

- [Agents](/docs/agents)
- [Command reference](/docs/sdk/commands)
- [OpenAPI](/docs/api/openapi)
