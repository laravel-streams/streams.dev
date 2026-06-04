---
title: Responses
description: 'Response envelope — data, errors, links, meta — and ApiResponse API.'
sort_order: 5
status: ready
---

Successful API responses use a consistent JSON envelope from `Streams\Api\ApiResponse`.

## Envelope shape

```json
{
    "data": { "id": "1", "title": "Star Wars" },
    "errors": [],
    "links": {
        "self": "http://localhost/api/streams/films/entries/1"
    },
    "meta": {
        "stream": "films",
        "query": {},
        "parameters": { "stream": "films", "entry": "1" }
    }
}
```

| Key | Purpose |
|-----|---------|
| `data` | Resource or collection (null when empty) |
| `errors` | Array of error strings |
| `links` | `self`, pagination links, create `location` |
| `meta` | `stream`, `query`, `payload`, `parameters`, pagination keys |

HTTP status is carried in the response header, not inside the JSON body.

## Status codes used

`200`, `201`, `204`, `400`, `404`, `409`

Validation failures return **409** with errors populated — not 422.

## Delete response

Successful delete returns **204 No Content** with an empty body (no envelope).

## Related

- [Errors](/docs/api/errors)
- [Pagination](/docs/api/pagination)
