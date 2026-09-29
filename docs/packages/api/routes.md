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

Register routes with `API::routeCrud()` in a service provider's `boot()` method. It registers the default interface with both resources below. Paths below assume the default prefix `api`.

`API::routeStreams()` and `API::routeEntries()` register the same default interface with only one resource each. Use one helper, not several: each helper does nothing if the default interface is already registered, so calling `routeStreams()` and then `routeEntries()` leaves you with the stream routes only. To get both, call `routeCrud()`, or register your own [interface](/docs/api/custom-interfaces) with both resources.

## Stream routes (`StreamsResource`)

| Method | Path | Route name |
|--------|------|------------|
| GET | `/api/streams` | `streams.api.streams.list` |
| POST | `/api/streams` | `streams.api.streams.create` |
| GET | `/api/streams/{stream}` | `streams.api.streams.show` |
| PUT | `/api/streams/{stream}` | `streams.api.streams.update` |
| PATCH | `/api/streams/{stream}` | `streams.api.streams.patch` |
| DELETE | `/api/streams/{stream}` | `streams.api.streams.delete` |

Stream controllers operate on the meta-stream from `config('streams.core.streams_id')`.

## Entry routes (`EntriesResource`)

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
