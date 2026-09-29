---
title: Pagination
nav_title: Pagination
description: first_page, next_page, and meta keys from addPaginationMeta().
section: packages
package: api
order: 80
tags: [api, pagination]
status: ready
---

Paginated list responses add keys to `links` and `meta` via `ApiResponse::addPaginationMeta()`.

## Meta keys

```json
{
    "meta": {
        "total": 150,
        "per_page": 100,
        "last_page": 2,
        "current_page": 1
    }
}
```

## Link keys

```json
{
    "links": {
        "self": "...",
        "first_page": "...",
        "next_page": "...",
        "previous_page": "..."
    }
}
```

When on the first page, `previous_page` may be null. When on the last page, `next_page` may be null.

## Request parameters

Use `per_page` and `page` query parameters on `GET /api/streams/{stream}/entries`. Default `per_page` is **100** in source code.

## Related

- [Query parameters](/docs/api/query-parameters)
- [Responses](/docs/api/responses)
