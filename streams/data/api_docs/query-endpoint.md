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

Each item in `parameters` maps to a criteria method call: the key is the method, the value is its argument list. Results are paginated like the list endpoint; pass `per_page` and `page` in the query string or body.

## Response

Same envelope as list endpoints: results in `data`, pagination in `meta` and `links`, and the request body echoed in `meta.payload`.

## Security

The endpoint calls **any** criteria method named in the body except `delete` and `truncate`. That includes write methods such as `create`, `save`, `firstOrCreate`, and `updateOrCreate`. Treat access to this endpoint as write access to the stream: put it behind the same middleware as your create and update endpoints, or leave `EntriesResource` off public interfaces and register only the endpoints you want (see [Custom endpoints](/docs/api/custom-endpoints)).

## When to use

Prefer GET with query parameters for simple filters. Use POST query when filter payloads are too large or complex for query strings.

## Related

- [Query parameters](/docs/api/query-parameters)
- [Core criteria](/docs/core/criteria)
