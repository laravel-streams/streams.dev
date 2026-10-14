---
title: 'SDK: Streams'
nav_title: Streams
description: Define streams with the SDK scaffolding workflow.
section: packages
package: sdk
order: 40
tags: [sdk, streams]
editorial_status: ready
status: implemented
tracking_task: 262
---

Streams are the foundation of Laravel Streams - they define the structure and behavior of your data entities. Think of them as dynamic Eloquent models that can be configured through JSON.

## Basic Stream Structure

```json
{
    "$schema": "https://streams.dev/schema/streams.schema.json",
    "name": "Blog Posts",
    "id": "blog_posts",
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

### Identity and fields

No top-level properties are required by the definition schema. Use **id** for the stream identifier (snake_case recommended), or let the JSON filename supply it. The legacy top-level **handle** is deprecated. **fields** defines attributes as a list of field objects or an object keyed by field handle; a field in list form needs its own `handle`.

### Optional Properties

- **description**: Brief description of the stream's purpose
- **name**: Human-readable name; defaults to the title-cased ID
- **config**: Configuration options for data source and behavior
- **routes**: URL routing definitions
- **rules**: Stream-level validation rules keyed by field handle. The legacy top-level `validation` property is not supported by this workflow.

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
            "type": "filebase",
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

Validate source configuration with `php artisan streams:validate`. Filebase sources use `format` and `path`; database sources use `table`, and Eloquent sources use `model`. The validator checks source configuration against the runtime adapter requirements.

## Stream Configuration Options

### Pagination

Choose pagination when making the query. A `config.pagination` object does not automatically paginate entries:

```php
Streams::entries('products')->paginate(20);
```

### Sorting

Apply sorting to the query. A `config.sort` object does not automatically order entries:

```php
Streams::entries('products')->orderBy('created_at', 'desc')->get();
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
