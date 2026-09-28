# Documentation Analysis

This document summarizes the documentation architecture for streams.dev. For contribution rules, see [STYLE.md](STYLE.md).

## Architecture

All documentation is maintained in this repository under `streams/data/`. Stream JSON configs in `streams/` register routes and point at the default filebase path (`streams/data/{handle}/`).

Package repositories link to streams.dev URLs. They do not maintain parallel `docs/` trees.

## Directory map

| Handle | Directory | Route |
|--------|-----------|-------|
| `docs` | `streams/data/docs/` | `/docs/{id}` |
| `core_docs` | `streams/data/core_docs/` | `/docs/core/{id}` |
| `ui_docs` | `streams/data/ui_docs/` | `/docs/ui/{id}` |
| `api_docs` | `streams/data/api_docs/` | `/docs/api/{id}` |
| `sdk_docs` | `streams/data/sdk_docs/` | `/docs/sdk/{id}` |
| `testing_docs` | `streams/data/testing_docs/` | `/docs/testing/{id}` |
| `client_docs` | `streams/data/client_docs/` | `/docs/client/{id}` |

## Content layers

**Hub docs** (`streams/data/docs/`) — cross-cutting guides for Laravel developers and teams: installation, architecture, use cases, routing, caching, and workflow pages that link into package sections.

**Package sections** — reference depth per package. Hub pages should not duplicate long API or configuration reference.

## Status workflow

Frontmatter `status` values:

- `drafting` — outline or work in progress
- `editing` — substantive content, needs review
- `ready` — publication-ready

## Legacy content

`docs.bak/` contains archived pages (contributing, debugging, examples) that may be mined for hub content. It is not wired to the site.
