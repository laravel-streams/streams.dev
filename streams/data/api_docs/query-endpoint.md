---
title: Query endpoint
description: 'POST /api/streams/{stream}/query with parameters array.'
sort_order: 11
status: ready
---

The query endpoint runs criteria operations from a JSON body instead of query string parameters.

## Request

`POST /api/streams/{stream}/query`

```bash
curl -s -X POST "http://localhost/api/streams/films/query" \
  -H "Content-Type: application/json" \
  -d '{
    "parameters": [
      {"where": ["director", "George Lucas"]},
      {"orderBy": ["title", "asc"]},
      {"limit": [20]}
    ]
  }'
```

Each item in `parameters` maps to a criteria method call.

## Response

Same envelope as list endpoints, with results in `data` and applied query reflected in `meta.query`.

## When to use

Prefer GET with query parameters for simple filters. Use POST query when filter payloads are too large or complex for query strings.

## Related

- [Query parameters](/docs/api/query-parameters)
- [Core criteria](/docs/core/criteria)
