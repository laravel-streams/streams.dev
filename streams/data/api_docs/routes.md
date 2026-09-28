---
title: 'API: Routes'
nav_title: Routes
description: Full route table for streams and entries endpoints.
section: packages
package: api
order: 40
tags: [api, routes]
status: ready
---

Register routes with `API::routeStreams()` and `API::routeEntries()`. Paths below assume default prefix `api`.

## Stream routes (`API::routeStreams()`)

| Method | Path | Route name |
|--------|------|------------|
| GET | `/api/streams` | `streams.api.streams.list` |
| POST | `/api/streams` | `streams.api.streams.create` |
| GET | `/api/streams/{stream}` | `streams.api.streams.show` |
| PUT | `/api/streams/{stream}` | `streams.api.streams.update` |
| PATCH | `/api/streams/{stream}` | `streams.api.streams.patch` |
| DELETE | `/api/streams/{stream}` | `streams.api.streams.delete` |

Stream controllers operate on the meta-stream from `config('streams.core.streams_id')`.

## Entry routes (`API::routeEntries()`)

| Method | Path | Route name |
|--------|------|------------|
| GET | `/api/streams/{stream}/entries` | `streams.api.entries.list` |
| POST | `/api/streams/{stream}/entries` | `streams.api.entries.create` |
| GET | `/api/streams/{stream}/entries/{entry}` | `streams.api.entries.show` |
| PUT | `/api/streams/{stream}/entries/{entry}` | `streams.api.entries.update` |
| PATCH | `/api/streams/{stream}/entries/{entry}` | `streams.api.entries.patch` |
| DELETE | `/api/streams/{stream}/entries/{entry}` | `streams.api.entries.delete` |
| POST | `/api/streams/{stream}/query` | `streams.api.entries.query` |

The `{entry}` parameter accepts composite IDs (`where => ['entry' => '(.*)']`).

## Not valid

There is no shorthand `/api/{stream}` path. Always use `/api/streams/{stream}/entries`.

## Related

- [Stream endpoints](/docs/api/streams-endpoints)
- [Entry endpoints](/docs/api/entry-endpoints)
- [Query endpoint](/docs/api/query-endpoint)
