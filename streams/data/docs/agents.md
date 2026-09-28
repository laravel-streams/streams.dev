---
sort_order: 103
category: development
title: Agents
description: 'How agents should read Streams docs: llms.txt, raw markdown, OpenAPI, and what is not built yet.'
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
| [/docs/api/openapi.yaml](/docs/api/openapi.yaml) | The generic OpenAPI 3 description of `streams/api`. It is not generated from your app's streams. |

HTML pages also emit `<link rel="alternate" type="text/markdown">` pointing at the `.md` URL.

These routes are dynamic. They read the docs streams through `App\Support\DocsSearchIndex` and cache the text for 15 minutes (`docs.llms.index`, `docs.llms.full`, `docs.search.index`). Adding a markdown file under `streams/data/{docs,core_docs,ui_docs,api_docs,sdk_docs,testing_docs,client_docs}/` publishes it on the next cache miss. There is no generate step.

## What to trust

Documented behavior on a page marked `status: ready` was checked against package source. If a page and the code disagree, the code wins, and the page should be corrected here.

Two names stay distinct until a later rename lands:

- **SDK** (`streams/sdk`) is the PHP Artisan generators. See the [command reference](/docs/sdk/commands).
- **Client** (`@laravel-streams/api-client`) is the JavaScript client for the REST API.

## What is not available yet

An MCP server is not part of any published package. The planned local-development server, and the Artisan commands it would wrap, are described on [MCP](/docs/mcp). Do not call MCP tools that this site does not list as registered.

`make:stream` writes `$schema: https://streams.dev/schema/streams.schema.json`. That URL is not served yet. `streams:schema` can write a schema for each of *your* streams; that is a different file. See the [command reference](/docs/sdk/commands).

## Related

- [MCP](/docs/mcp)
- [Workflows](/docs/workflows)
- [Command reference](/docs/sdk/commands)
- [Contributing documentation](/docs/contributing-docs)
