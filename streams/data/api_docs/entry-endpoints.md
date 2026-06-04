---
title: Entry endpoints
description: 'CRUD and upsert behavior for /api/streams/{stream}/entries.'
sort_order: 10
status: ready
---

Entry endpoints operate on any stream by handle.

## List entries

```bash
curl -s "http://localhost/api/streams/films/entries"
```

Supports [query parameters](/docs/api/query-parameters) and [pagination](/docs/api/pagination).

## Show entry

```bash
curl -s "http://localhost/api/streams/films/entries/1"
```

## Create entry

```bash
curl -s -X POST "http://localhost/api/streams/films/entries" \
  -H "Content-Type: application/json" \
  -d '{"title":"The Last Jedi","director":"Rian Johnson"}'
```

Returns **201** on success, **409** when validation fails.

## Update entry

PUT replaces fields; PATCH merges partial data:

```bash
curl -s -X PATCH "http://localhost/api/streams/films/entries/4" \
  -H "Content-Type: application/json" \
  -d '{"title":"Patched Title"}'
```

## Delete entry

```bash
curl -s -X DELETE "http://localhost/api/streams/films/entries/4"
```

Returns **204** with empty body.

## Related

- [Request format](/docs/api/requests)
- [Query endpoint](/docs/api/query-endpoint)
