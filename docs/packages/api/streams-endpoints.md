---
title: Stream endpoints
nav_title: Stream endpoints
description: CRUD for stream definitions on /api/streams.
section: packages
package: api
order: 100
tags: [api, streams, endpoints]
status: ready
---

Stream endpoints manage entries in the meta-stream (`config('streams.core.streams_id')`) that store stream JSON definitions.

## List streams

```bash
curl -s "http://localhost/api/streams"
```

Returns collection in `data`.

## Show stream

```bash
curl -s "http://localhost/api/streams/posts"
```

## Create stream

```bash
curl -s -X POST "http://localhost/api/streams" \
  -H "Content-Type: application/json" \
  -d '{"id":"posts","name":"Posts","fields":[]}'
```

Returns **201** with `links.location`.

## Update stream

```bash
curl -s -X PUT "http://localhost/api/streams/posts" \
  -H "Content-Type: application/json" \
  -d '{"name":"Blog Posts"}'
```

PATCH accepts partial updates.

## Delete stream

```bash
curl -s -X DELETE "http://localhost/api/streams/posts"
```

Returns **204 No Content**.

## Related

- [Routes](/docs/api/routes)
- [Entry endpoints](/docs/api/entry-endpoints)
