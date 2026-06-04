---
id: query-parameters
sort_order: 3
status: published
title: Query Parameters
description: Filter, paginate, and constrain API responses with query parameters.
---

# Query Parameters

The Streams API supports powerful query parameters for filtering, pagination, and sorting.

## Filtering with `where`

Use the `where` parameter to filter results:

```http
GET /api/streams/posts/entries?where[status]=published
GET /api/streams/posts/entries?where[author]=john&where[status]=published
```

### Basic Equality

```http
?where[field]=value
```

Matches entries where `field` equals `value`.

### Using Operators

For advanced filtering, use the `constraint` parameter with operators:

```http
GET /api/streams/posts/entries?where[title]=Laravel&constraint[title]=like
```

**Available operators**:
- `like` - Partial match with wildcards
- `>`, `>=`, `<`, `<=` - Numeric comparisons
- `!=`, `<>` - Not equal
- `in` - Match any value in array
- `between` - Range between two values

### Examples

```http
# Posts with "Laravel" in the title
?where[title]=%Laravel%&constraint[title]=like

# Posts with more than 100 views
?where[views]=100&constraint[views]=>

# Posts from specific authors
?where[author]=john,jane,bob&constraint[author]=in
```

## Pagination

### Per Page

Control the number of results per page:

```http
GET /api/streams/posts/entries?per_page=20
```

Default: `15` (Laravel default)

### Page Number

Navigate through pages:

```http
GET /api/streams/posts/entries?page=2&per_page=20
```

### Limit

Set a maximum number of results (overrides pagination):

```http
GET /api/streams/posts/entries?limit=100
```

## Response Format

Paginated responses include metadata:

```json
{
    "data": [
        {
            "id": "post-1",
            "title": "Getting Started with Streams",
            "status": "published"
        }
    ],
    "links": {
        "first": "/api/streams/posts/entries?page=1",
        "last": "/api/streams/posts/entries?page=5",
        "prev": null,
        "next": "/api/streams/posts/entries?page=2"
    },
    "meta": {
        "current_page": 1,
        "from": 1,
        "last_page": 5,
        "per_page": 15,
        "to": 15,
        "total": 73
    }
}
```

## Advanced Queries

For complex queries, use the Query endpoint with JSON:

```http
POST /api/streams/posts/query
Content-Type: application/json

{
    "parameters": [
        {"where": ["status", "published"]},
        {"where": ["views", ">", 100]},
        {"orderBy": ["created_at", "desc"]},
        {"limit": [20]}
    ]
}
```

### Custom Query Methods

You can call any query builder method:

```json
{
    "parameters": [
        {"whereBetween": ["created_at", ["2024-01-01", "2024-12-31"]]},
        {"whereIn": ["category", ["tech", "tutorial"]]},
        {"whereNotNull": ["featured_image"]},
        {"latest": []},
        {"take": [10]}
    ]
}
```

## Combining Parameters

All parameters can be combined:

```http
GET /api/streams/posts/entries?where[status]=published&where[category]=tech&per_page=10&page=1
```

## Examples

### Get Published Posts

```http
GET /api/streams/posts/entries?where[status]=published
```

### Search by Title

```http
GET /api/streams/posts/entries?where[title]=%Laravel%&constraint[title]=like
```

### Recent Posts with Pagination

```http
GET /api/streams/posts/entries?per_page=20&page=1
```

### Complex Query

```http
POST /api/streams/posts/query
Content-Type: application/json

{
    "parameters": [
        {"where": ["status", "published"]},
        {"where": ["views", ">", 100]},
        {"whereIn": ["category", ["tech", "tutorial", "guide"]]},
        {"whereBetween": ["created_at", ["2024-01-01", "2024-12-31"]]},
        {"orderBy": ["views", "desc"]},
        {"limit": [50]}
    ]
}
```

## Next Steps

- [Custom Interfaces](custom-interfaces) - Create specialized API interfaces
- [Custom Endpoints](custom-endpoints) - Add custom query logic
- [Responses](responses) - Understand response formatting
