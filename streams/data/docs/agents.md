---
title: Agents
nav_title: Agents
description: 'How agents work with Streams: llms.txt and raw markdown, the MCP server, the stream definition schema, and OpenAPI.'
section: guides
category: development
package: sdk
order: 50
tags: [sdk, agents]
status: ready
---

Streams docs are meant to be read by coding agents as well as people. This site is the source of truth. Package repositories do not keep a second copy.

## Read the docs

| URL | What you get |
|-----|----------------|
| [/llms.txt](/llms.txt) | An index of every docs page, as markdown links, following [llmstxt.org](https://llmstxt.org). Built on each request from the same index as search. |
| [/llms-full.txt](/llms-full.txt) | The same pages, inlined. Use this when you need the text and cannot fetch each link. |
| `/docs/{id}.md` and `/docs/{package}/{id}.md` | The source of one page, with its title as a heading. Drop `.md` for the HTML page. |
| [/search/docs.json](/search/docs.json) | The Cmd+K index: title, description, URL, markdown URL, and an excerpt. |
| [/docs/api/openapi.yaml](/docs/api/openapi.yaml) | The generic OpenAPI 3 description of `streams/api`, copied from the package. It is not generated from your app's streams. |
| [/schema/streams.schema.json](/schema/streams.schema.json) | The JSON Schema for `streams/*.json` definition files. See [Stream definition schema](/docs/sdk/stream-schema). |

HTML pages also emit `<link rel="alternate" type="text/markdown">` pointing at the `.md` URL.

These routes are dynamic. They read the docs streams through `App\Support\DocsSearchIndex` and cache the text for 15 minutes (`docs.llms.index`, `docs.llms.full`, `docs.search.index`). A hub page is a markdown file under `streams/data/docs/`. A package page is a markdown file under `docs/packages/{name}/`, which the matching stream reads via `source.path`. Adding a file publishes it on the next cache miss. There is no generate step. `php artisan docs:sync` copies `docs/packages/{name}/` to and from that package's repository.

## What to trust

Documented behavior on a page marked `status: ready` was checked against package source. If a page and the code disagree, the code wins, and the page should be corrected here.

Two names stay distinct until a later rename lands:

- **SDK** (`streams/sdk`) is the PHP Artisan generators and the local [MCP server](/docs/mcp). See the [command reference](/docs/sdk/commands).
- **Client** (`@laravel-streams/api-client`) is the JavaScript client for the REST API.

## Work inside an app

Inside a Laravel app with `streams/sdk`, an agent doesn't have to read source files to learn the domain model:

- **MCP server.** `php artisan mcp:start streams` exposes 14 tools (list and describe streams, entry and definition schemas, validate definitions, entry CRUD, docs search, and the `make-stream` and `make-addon` generators), two resources, and the `design-stream` prompt. It needs `laravel/mcp`, which needs Laravel 11.45+ or 12.41+. See [MCP server](/docs/mcp).
- **CLI with JSON output.** On any supported Laravel version, `php artisan streams:list --json` lists the streams and `php artisan streams:validate --json` checks definition files. See the [command reference](/docs/sdk/commands).
- **Definition schema.** `make:stream` writes `"$schema": "https://streams.dev/schema/streams.schema.json"`, and this site serves that file at [/schema/streams.schema.json](/schema/streams.schema.json). `streams:schema` writes a schema for the *entries* of each of your streams; that is a different file.

## Related

- [MCP server](/docs/mcp)
- [Workflows](/docs/workflows)
- [Command reference](/docs/sdk/commands)
- [Contributing documentation](/docs/contributing-docs)
