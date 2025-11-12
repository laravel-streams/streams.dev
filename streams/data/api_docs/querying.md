---
title: Querying and Filtering
description: 'Advanced querying capabilities for Streams API.'
sort_order: 2
category: core-concepts
status: live
---

# Querying and Filtering

Streams API provides powerful querying capabilities through URL parameters, allowing clients to filter, sort, paginate, and include related data efficiently.

## Basic Filtering

### Simple Filters

Filter entries using the `filter` parameter:

```http
GET /api/posts?filter[published]=true
GET /api/users?filter[role]=admin
GET /api/products?filter[category]=electronics
```

### Multiple Filters

Combine multiple filters (AND logic):

```http
GET /api/posts?filter[published]=true&filter[featured]=true
GET /api/users?filter[active]=true&filter[role]=admin
```

### Filter Operators

Use operators for more complex filtering:

```http
# Greater than
GET /api/posts?filter[views]=gt:1000

# Less than  
GET /api/products?filter[price]=lt:50.00

# Greater than or equal
GET /api/users?filter[age]=gte:18

# Less than or equal
GET /api/orders?filter[total]=lte:100

# Not equal
GET /api/posts?filter[status]=ne:draft

# Like (contains)
GET /api/users?filter[name]=like:john

# In array
GET /api/posts?filter[category]=in:tech,news,sports

# Not in array
GET /api/users?filter[role]=nin:banned,suspended
```

### Date Filtering

Filter by date ranges:

```http
# Specific date
GET /api/posts?filter[created_at]=2023-01-01

# Date range
GET /api/posts?filter[created_at]=gte:2023-01-01&filter[created_at]=lte:2023-12-31

# Relative dates
GET /api/posts?filter[created_at]=gt:now-30days
GET /api/users?filter[last_login]=gte:now-1week
```

### Null Filtering

Filter for null or not null values:

```http
# Is null
GET /api/users?filter[deleted_at]=null

# Is not null  
GET /api/users?filter[email_verified_at]=notnull
```

## Advanced Filtering

### Nested Field Filtering

Filter by relationship fields:

```http
# Filter posts by author name
GET /api/posts?filter[author.name]=john

# Filter users by profile city
GET /api/users?filter[profile.city]=newyork

# Multiple relationship filters
GET /api/posts?filter[author.active]=true&filter[category.featured]=true
```

### OR Conditions

Use OR logic in filters:

```http
# Posts that are published OR featured
GET /api/posts?filter[or][published]=true&filter[or][featured]=true

# Users with admin OR moderator role
GET /api/users?filter[or][role]=admin&filter[or][role]=moderator
```

### Complex Filter Groups

Group filters with complex logic:

```http
# (published=true AND featured=true) OR (priority=high)
GET /api/posts?filter[and][published]=true&filter[and][featured]=true&filter[or][priority]=high
```

### Full-Text Search

Search across multiple fields:

```http
# Search posts by title and content
GET /api/posts?search=laravel+streams

# Search with specific fields
GET /api/users?search[fields]=name,email,bio&search[query]=john
```

## Sorting

### Basic Sorting

Sort results using the `sort` parameter:

```http
# Sort by single field (ascending)
GET /api/posts?sort=title

# Sort descending (prefix with -)
GET /api/posts?sort=-created_at

# Sort by multiple fields
GET /api/posts?sort=-featured,created_at,-views
```

### Relationship Sorting

Sort by relationship fields:

```http
# Sort posts by author name
GET /api/posts?sort=author.name

# Sort users by profile created date
GET /api/users?sort=-profile.created_at
```

### Custom Sort Functions

Use predefined sort functions:

```http
# Random order
GET /api/posts?sort=random

# Distance from location
GET /api/stores?sort=distance&lat=40.7128&lng=-74.0060
```

## Pagination

### Basic Pagination

Control pagination with `page` and `limit`:

```http
# Page 2 with 10 items per page
GET /api/posts?page=2&limit=10

# Large page size (up to max_limit)
GET /api/users?limit=100
```

### Cursor Pagination

Use cursor-based pagination for better performance:

```http
# First page
GET /api/posts?limit=20

# Next page using cursor
GET /api/posts?after=eyJpZCI6MTIzLCJjcmVhdGVkX2F0IjoiMjAyMy0wMS0wMSJ9&limit=20

# Previous page
GET /api/posts?before=eyJpZCI6MTAwLCJjcmVhdGVkX2F0IjoiMjAyMy0wMS0wMSJ9&limit=20
```

### Pagination Metadata

Response includes pagination information:

```json
{
    "data": [...],
    "meta": {
        "pagination": {
            "count": 10,
            "current_page": 2,
            "per_page": 10,
            "total": 150,
            "total_pages": 15,
            "has_more": true
        }
    },
    "links": {
        "first": "/api/posts?page=1&limit=10",
        "prev": "/api/posts?page=1&limit=10",
        "next": "/api/posts?page=3&limit=10",
        "last": "/api/posts?page=15&limit=10"
    }
}
```

## Including Relationships

### Basic Inclusion

Include related data in the response:

```http
# Include single relationship
GET /api/posts?include=author

# Include multiple relationships
GET /api/posts?include=author,categories,comments

# Include nested relationships
GET /api/posts?include=author.profile,comments.author
```

### Conditional Inclusion

Include relationships based on conditions:

```http
# Include author only if post is published
GET /api/posts?include=author&include_if[author]=published:true

# Include comments only for recent posts
GET /api/posts?include=comments&include_if[comments]=created_at:gte:now-7days
```

### Sparse Fieldsets

Request only specific fields:

```http
# Only include specific post fields
GET /api/posts?fields[posts]=title,content,published_at

# Include specific fields from relationships
GET /api/posts?include=author&fields[author]=name,email&fields[posts]=title,author
```

## Aggregation

### Count Queries

Get counts without fetching data:

```http
# Total count
GET /api/posts/count

# Filtered count
GET /api/posts/count?filter[published]=true

# Grouped counts
GET /api/posts/count?group_by=category
```

### Statistical Aggregations

Perform calculations on numeric fields:

```http
# Sum of values
GET /api/orders/sum?field=total

# Average values
GET /api/products/avg?field=price&filter[category]=electronics

# Min/max values
GET /api/users/min?field=age
GET /api/products/max?field=price
```

## Field Selection

### Select Specific Fields

Reduce response size by selecting only needed fields:

```http
# Select specific fields
GET /api/users?fields=id,name,email

# Select fields with relationships
GET /api/posts?fields=title,content&include=author&fields[author]=name
```

### Exclude Fields

Exclude sensitive or unnecessary fields:

```http
# Exclude specific fields
GET /api/users?exclude=password,remember_token

# Exclude relationship fields
GET /api/posts?include=author&exclude[author]=password,api_token
```

## Caching

### Cache Control

Use cache parameters for better performance:

```http
# Enable caching for 1 hour
GET /api/posts?cache=3600

# Use specific cache tags
GET /api/posts?cache=3600&cache_tags=posts,featured

# Bypass cache
GET /api/posts?no_cache=true
```

### ETag Support

Use ETags for conditional requests:

```http
GET /api/posts/123
If-None-Match: "abc123"
```

Response:
```http
HTTP/1.1 304 Not Modified
ETag: "abc123"
```

## Advanced Query Features

### Scopes

Use predefined query scopes:

```http
# Published posts scope
GET /api/posts?scope=published

# Multiple scopes
GET /api/posts?scope=published,featured

# Scopes with parameters
GET /api/posts?scope=created_since:2023-01-01
```

### Custom Filters

Apply custom filter logic:

```http
# Distance-based filtering
GET /api/stores?near=40.7128,-74.0060&within=5km

# Text similarity
GET /api/products?similar_to=laptop&threshold=0.8

# Price range with discounts
GET /api/products?price_range=100-500&include_discounted=true
```

### Query Optimization

Optimize queries for performance:

```http
# Use indexes hint
GET /api/posts?hint=use_index:idx_published_created

# Disable expensive operations
GET /api/posts?no_count=true&no_relationships=true

# Stream results for large datasets
GET /api/posts?stream=true&limit=1000
```

## Query Examples

### Blog Posts Query

```http
GET /api/posts?filter[published]=true&filter[featured]=true&sort=-created_at&include=author,categories&fields[posts]=title,excerpt,featured_image&fields[author]=name,avatar&limit=10
```

### E-commerce Product Search

```http
GET /api/products?search=wireless+headphones&filter[category]=electronics&filter[price]=gte:50&filter[price]=lte:200&filter[in_stock]=true&sort=price&include=reviews&fields[products]=name,price,image,rating&limit=20
```

### User Administration

```http
GET /api/users?filter[role]=in:admin,moderator&filter[active]=true&filter[last_login]=gte:now-30days&sort=-last_login&include=profile&fields[users]=name,email,role,last_login&fields[profile]=avatar,department
```

### Analytics Query

```http
GET /api/orders/sum?field=total&filter[created_at]=gte:2023-01-01&filter[created_at]=lte:2023-12-31&group_by=month&include_count=true
```

## Best Practices

1. **Use specific filters**: Filter data at the API level rather than client-side
2. **Limit included data**: Only include relationships you need
3. **Use sparse fieldsets**: Request only necessary fields
4. **Paginate large datasets**: Always use pagination for lists
5. **Cache when possible**: Use caching for frequently accessed data
6. **Index filterable fields**: Ensure database indexes support your filters
7. **Monitor query performance**: Profile and optimize slow queries
8. **Use scopes for complex logic**: Define reusable query scopes
9. **Validate query parameters**: Ensure filters are safe and valid
10. **Document custom filters**: Provide clear documentation for custom query features

## Error Handling

Query errors return structured error responses:

```json
{
    "errors": [
        {
            "status": "400",
            "title": "Invalid Filter",
            "detail": "The filter 'invalid_field' is not allowed.",
            "source": {
                "parameter": "filter[invalid_field]"
            }
        }
    ]
}
```

Common query errors:
- Invalid filter fields
- Unsupported operators
- Invalid sort fields
- Exceeded maximum limit
- Invalid date formats
- Unauthorized field access
