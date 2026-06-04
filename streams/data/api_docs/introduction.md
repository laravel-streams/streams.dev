---
title: Introduction
description: 'RESTful API automation for Laravel Streams.'
sort_order: 0
status: ready
---

# Streams API

The Streams API package (`streams/api`) generates REST endpoints for your streams without hand-written CRUD controllers for each model.

## What you get

- CRUD routes per exposed stream
- Query parameters for filter, sort, and pagination
- Request validation from stream field rules
- Custom interfaces, endpoints, and response formatting
- OpenAPI schema generation

Responses use the **Streams API JSON envelope** documented in [Responses](/docs/api/responses). The format is stable and intentionally preserved across versions.

## Default routes

Each exposed stream receives:

- `GET /api/{stream}` — list entries
- `GET /api/{stream}/{id}` — single entry
- `POST /api/{stream}` — create
- `PUT/PATCH /api/{stream}/{id}` — update
- `DELETE /api/{stream}/{id}` — delete

Exact prefixes depend on your API interface configuration.

## Installation

Install Streams API via Composer:

```bash
composer require streams/api
```

The package will automatically register and be available immediately.

## Configuration

### Publishing Configuration

```bash
php artisan vendor:publish --provider="Streams\Api\ApiServiceProvider" --tag=config
```

### Configuration File

```php
// config/streams/api.php
return [
    // Enable/disable the API
    'enabled' => env('STREAMS_API_ENABLED', true),
    
    // API route prefix
    'prefix' => env('STREAMS_API_PREFIX', 'api'),
    
    // Middleware group
    'middleware' => env('STREAMS_API_MIDDLEWARE', 'api'),
    
    // Default pagination limit
    'limit' => 15,
    
    // Maximum pagination limit
    'max_limit' => 100,
    
    // Include metadata in responses
    'metadata' => true,
];
```

## Quick Start

Once installed, your streams automatically have API endpoints available:

### Example Stream

```json
// streams/posts.json
{
    "name": "Posts",
    "fields": [
        {
            "handle": "title",
            "type": "string",
            "rules": ["required", "max:255"]
        },
        {
            "handle": "content",
            "type": "text"
        },
        {
            "handle": "author",
            "type": "relationship",
            "config": {
                "related": "users"
            }
        }
    ]
}
```

### Available Endpoints

```bash
# List posts
GET /api/posts

# Get specific post
GET /api/posts/123

# Create new post
POST /api/posts

# Update post
PUT /api/posts/123

# Delete post
DELETE /api/posts/123
```

## API Usage Examples

### List Entries

```bash
GET /api/posts
```

**Response:**
```json
{
    "data": [
        {
            "type": "posts",
            "id": "1",
            "attributes": {
                "title": "My First Post",
                "content": "This is the content...",
                "created_at": "2023-01-01T12:00:00Z"
            },
            "relationships": {
                "author": {
                    "data": {
                        "type": "users",
                        "id": "1"
                    }
                }
            }
        }
    ],
    "meta": {
        "pagination": {
            "count": 1,
            "current_page": 1,
            "per_page": 15,
            "total": 1,
            "total_pages": 1
        }
    }
}
```

### Get Single Entry

```bash
GET /api/posts/1
```

### Create Entry

```bash
POST /api/posts
Content-Type: application/json

{
    "data": {
        "type": "posts",
        "attributes": {
            "title": "New Post",
            "content": "Post content here..."
        },
        "relationships": {
            "author": {
                "data": {
                    "type": "users",
                    "id": "1"
                }
            }
        }
    }
}
```

### Update Entry

```bash
PUT /api/posts/1
Content-Type: application/json

{
    "data": {
        "type": "posts",
        "id": "1",
        "attributes": {
            "title": "Updated Title"
        }
    }
}
```

### Delete Entry

```bash
DELETE /api/posts/1
```

## Next Steps

- [API Endpoints](endpoints)
- [Querying and Filtering](querying)
- [Relationships](relationships)
- [Authentication](authentication)
- [Customization](customization)
