---
title: Errors
nav_title: Errors
description: 409 validation, 404 JSON, 204 delete, and error envelope.
section: packages
package: api
order: 90
tags: [api, errors]
status: ready
---

Error responses populate the `errors` array in the JSON envelope (except 204 delete).

## Status codes

| Code | When | Body |
|------|------|------|
| **409** | Validation failure on create/update/patch | Envelope with `errors` strings |
| **404** | Stream or entry not found | Envelope with `errors` (e.g. `"Entry not found."`) |
| **204** | Successful delete | Empty body, no envelope |
| **400** | Bad request | Envelope with `errors` |

Validation uses **409 Conflict**, not HTTP 422.

## Example validation error

```json
{
    "data": null,
    "errors": ["The title field is required."],
    "links": { "self": "..." },
    "meta": { "stream": "films" }
}
```

## Example not found

`GET /api/streams/films/entries/missing` returns 404 with errors populated.

## Related

- [Responses](/docs/api/responses)
- [Request format](/docs/api/requests)
