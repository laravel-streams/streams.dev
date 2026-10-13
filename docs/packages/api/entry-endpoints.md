---
title: Entry endpoints
nav_title: Entry endpoints
description: 'CRUD and upsert behavior for /api/streams/{stream}/entries.'
section: packages
package: api
order: 110
tags: [api, entry, endpoints]
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

PUT sets the payload on the entry with `setAttributes()`; PATCH assigns each field in the payload one at a time. Both validate and save, and return **200** with the entry, or **409** when validation fails. If the entry doesn't exist yet, both create it (the `{entry}` key from the URL becomes its key) and return **201**, the same as a create:

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
