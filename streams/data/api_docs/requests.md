---
title: Request format
nav_title: Request format
description: 'Flat JSON field maps on create and update — not JSON:API.'
section: packages
package: api
order: 50
tags: [api, requests]
status: ready
---

Create and update endpoints accept **flat JSON** (or form-encoded) field maps. The API does not use JSON:API resource objects or `data.attributes` envelopes for input.

## Create entry

`POST /api/streams/{stream}/entries`

```bash
curl -s -X POST "http://localhost/api/streams/films/entries" \
  -H "Content-Type: application/json" \
  -d '{"title":"Star Wars","director":"George Lucas"}'
```

The controller passes `$request->json()->all()` to `newInstance()` on the stream criteria.

## Update entry

**PUT** replaces fields from JSON body. **PATCH** merges partial updates.

```bash
curl -s -X PATCH "http://localhost/api/streams/films/entries/1" \
  -H "Content-Type: application/json" \
  -d '{"title":"Updated Title"}'
```

The route `{entry}` id is set on the payload from the URL segment.

## Create/update stream

Same flat JSON shape for stream definition entries on `/api/streams` endpoints.

## Query endpoint (different shape)

`POST /api/streams/{stream}/query` uses a `parameters` array — see [Query endpoint](/docs/api/query-endpoint).

## Related

- [Entry endpoints](/docs/api/entry-endpoints)
- [Responses](/docs/api/responses)
- [Errors](/docs/api/errors)
