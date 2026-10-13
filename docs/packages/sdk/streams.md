---
title: 'SDK: Streams'
nav_title: Streams
description: Define streams with the SDK scaffolding workflow.
section: packages
package: sdk
order: 40
tags: [sdk, streams]
status: ready
---

Streams are the foundation of Laravel Streams - they define the structure and behavior of your data entities. Think of them as dynamic Eloquent models that can be configured through JSON.

## Basic Stream Structure

```json
{
    "$schema": "https://streams.dev/schema/streams.schema.json",
    "name": "Blog Posts",
    "handle": "blog_posts",
    "description": "Content management for blog articles",
    "config": {
        "source": {
            "type": "eloquent",
            "model": "App\\Models\\BlogPost"
        }
    },
    "fields": [
        {
            "handle": "id",
            "type": "integer",
            "config": {
                "auto_increment": true
            }
        },
        {
            "handle": "title",
            "type": "string",
            "name": "Title",
            "required": true,
            "config": {
                "max": 255
            }
        }
    ],
    "routes": [
        {
            "handle": "index",
            "uri": "blog",
            "view": "blog.index"
        }
    ]
}
```

## Stream Properties

### Required Properties

- **name**: Human-readable name for the stream
- **handle**: Unique identifier (snake_case recommended)
- **fields**: Array of field definitions

### Optional Properties

- **description**: Brief description of the stream's purpose
- **config**: Configuration options for data source and behavior
- **routes**: URL routing definitions
- **validation**: Stream-level validation rules

## Data Sources

### Eloquent Models
```json
{
    "config": {
        "source": {
            "type": "eloquent",
            "model": "App\\Models\\Product"
        }
    }
}
```

### File Storage (JSON/YAML)
```json
{
    "config": {
        "source": {
            "format": "json",
            "path": "content/products"
        }
    }
}
```

### Database Tables
```json
{
    "config": {
        "source": {
            "type": "database",
            "table": "products"
        }
    }
}
```

## Stream Configuration Options

### Pagination
```json
{
    "config": {
        "pagination": {
            "per_page": 20,
            "page_name": "page"
        }
    }
}
```

### Sorting
```json
{
    "config": {
        "sort": {
            "field": "created_at",
            "direction": "desc"
        }
    }
}
```

### Caching
```json
{
    "config": {
        "cache": {
            "ttl": 3600,
            "tags": ["products", "catalog"]
        }
    }
}
```

## Routing Integration

Streams can automatically generate routes for common operations:

```json
{
    "routes": [
        {
            "handle": "index",
            "uri": "products",
            "view": "products.index"
        },
        {
            "handle": "show",
            "uri": "products/{id}",
            "view": "products.show"
        },
        {
            "handle": "api",
            "uri": "api/products",
            "uses": "App\\Http\\Controllers\\Api\\ProductController"
        }
    ]
}
```

## AI Assistant Examples

When creating streams, consider these common patterns:

### Content Management
- Blog posts, pages, articles
- Categories, tags, taxonomies
- Media galleries, file management

### E-commerce
- Products, variants, inventories
- Orders, customers, payments
- Reviews, ratings, wishlists

### User Management
- Users, roles, permissions
- Profiles, preferences, settings
- Activity logs, notifications

### Business Applications
- Contacts, companies, leads
- Projects, tasks, timelines
- Invoices, payments, reports

## Best Practices

1. **Use Descriptive Handles**: Choose clear, descriptive identifiers
2. **Plan Relationships**: Consider how streams connect to each other
3. **Think Mobile-First**: Design for responsive interfaces
4. **Consider Performance**: Add appropriate indexes and caching
5. **Validate Early**: Define validation rules in the stream definition
6. **Document Purpose**: Add clear descriptions for future developers
