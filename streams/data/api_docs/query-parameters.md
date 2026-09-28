---
title: Query parameters
nav_title: Query parameters
description: where, constraint, order_by, limit, skip, and per_page on GET entries.
section: packages
package: api
order: 70
tags: [api, query, parameters]
status: ready
---

`GET /api/streams/{stream}/entries` accepts query parameters implemented in `GetEntries::applyFilters()`.

## Parameters

| Parameter | Example | Purpose |
|-----------|---------|---------|
| `where[field]` | `where[director]=Lucas` | Equality filter |
| `constraint[field]` | `constraint[director]=like` | Operator: `like`, `>`, `>=`, `<`, `<=`, `!=`, `<>`, `in`, `between` |
| `order_by[field]` | `order_by[title]=ASC` | Sort direction |
| `limit` | `limit=10` | Max results (with `skip`) |
| `skip` | `skip=20` | Offset (default `0`) |
| `per_page` | `per_page=20` | Page size (default **100** in code) |
| `page` | `page=2` | Page number (default `1`) |
| `with` | `with=author,tags` or `with[]=author` | Eager-load relationship fields (a trailing `_id` is stripped from the handle) |

## Example

```bash
curl -s "http://localhost/api/streams/films/entries\
?where[director]=George%20Lucas\
&constraint[director]=like\
&order_by[title]=ASC\
&per_page=20&page=1"
```

Array values on `where` fields support operator/operand pairs for advanced filters.

## Related

- [Query endpoint](/docs/api/query-endpoint)
- [Pagination](/docs/api/pagination)
- [Core criteria](/docs/core/criteria)
