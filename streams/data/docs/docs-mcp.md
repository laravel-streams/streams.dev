---
title: Docs MCP server
nav_title: Docs MCP server
description: 'Connect Cursor, Claude Code, or any MCP client to the hosted streams.dev docs over MCP: search, read pages, browse the navigation, and fetch the stream JSON Schema.'
section: guides
category: development
package: site
order: 65
tags: [mcp, agents, docs]
status: ready
---

streams.dev runs a public, read-only [Model Context Protocol](https://modelcontextprotocol.io) server over this documentation. Point your agent at it and it can search the docs, read whole pages as markdown, and fetch the stream JSON Schema without scraping HTML.

This server is hosted by streams.dev and only reads these docs. It is separate from the SDK's local [MCP server](/docs/mcp) (`php artisan mcp:start streams`), which runs inside your own app and works with its streams and entries. Use both: the SDK server for your app, this one for the docs.

## Endpoint

```text
https://streams.dev/mcp
```

Streamable HTTP transport (JSON-RPC over `POST`), no authentication. Requests are rate limited to 60 per minute per IP; over the limit the server answers `429` with a `Retry-After` header.

## Connect

### Cursor

Add the server to `.cursor/mcp.json` in your project (or `~/.cursor/mcp.json` for every project):

```json
{
    "mcpServers": {
        "streams-docs": {
            "url": "https://streams.dev/mcp"
        }
    }
}
```

### Claude Code

```bash
claude mcp add --transport http streams https://streams.dev/mcp
```

### Other clients

Any client that speaks MCP over Streamable HTTP can use the URL above. To try it by hand, run the MCP Inspector:

```bash
npx @modelcontextprotocol/inspector --cli https://streams.dev/mcp --transport http --method tools/list
```

### Local, over stdio

In a clone of the streams.dev repository the same server runs as an Artisan command, reading the docs from your checkout:

```bash
php artisan mcp:start streams-docs
```

```json
{
    "mcpServers": {
        "streams-docs-local": {
            "command": "php",
            "args": ["artisan", "mcp:start", "streams-docs"],
            "cwd": "/path/to/streams.dev"
        }
    }
}
```

## Tools

All tools are read-only.

| Tool | Arguments | Returns |
|------|-----------|---------|
| `search_docs` | `query` (required), `package`, `section`, `limit` (default 10, max 50) | Ranked pages with title, slug, URL, markdown URL, package, and a snippet |
| `get_page` | `page`: a slug (`core/introduction`), docs path, or URL | The page as markdown with a frontmatter block (title, description, slug, url) |
| `list_pages` | `package`, `section` | The navigation: guides by category, then each package, with title, slug, and URL |
| `get_schema` | `name` (default `streams`) | The JSON Schema served at [/schema/streams.schema.json](/schema/streams.schema.json) |

`package` is one of `guides`, `core`, `ui`, `api`, `sdk`, `testing`, `client`. `section` is `guide` or `reference`. A page's slug is its path under `/docs/`: `installation` for a hub guide, `core/introduction` for a package page.

Pages are also exposed as resources: `streams-docs://pages/{package}/{page}` (for example `streams-docs://pages/core/introduction`), plus `streams-docs://llms.txt` for the index.

## Other ways to read the docs

- [/llms.txt](/llms.txt) and [/llms-full.txt](/llms-full.txt)
- Any docs page with `.md` appended, such as [/docs/core/streams.md](/docs/core/streams.md)
- [/search/docs.json](/search/docs.json)

## Related

- [Agents](/docs/agents)
- [MCP server](/docs/mcp)
