---
title: Introduction
description: 'RESTful API automation for Laravel Streams.'
sort_order: 0
category: core-concepts
status: live
---

# Streams API

The Streams API package (`streams/api`) provides automatic RESTful API generation for your Laravel Streams. It creates standardized JSON APIs for your streams without requiring manual controller or route definition.

## What is Streams API?

Streams API automatically generates REST endpoints for your streams, providing:

- **Automatic REST Routes**: CRUD operations for all streams
- **JSON:API Compliance**: Follows JSON:API specification standards
- **Relationship Support**: Automatic relationship endpoints
- **Filtering & Sorting**: Query parameter support for data filtering
- **Pagination**: Built-in pagination with customizable limits
- **Validation**: Automatic request validation using stream rules
- **Customizable**: Override default behavior when needed

## Key Features

### Automatic Endpoint Generation
Every stream automatically gets a full set of REST endpoints:
- `GET /api/{stream}` - List entries
- `GET /api/{stream}/{id}` - Get single entry
- `POST /api/{stream}` - Create entry
- `PUT/PATCH /api/{stream}/{id}` - Update entry
- `DELETE /api/{stream}/{id}` - Delete entry

### JSON:API Compliance
Responses follow the JSON:API specification for consistent, predictable APIs.

### Advanced Querying
Support for filtering, sorting, pagination, and relationship inclusion via URL parameters.

### Security Integration
Integrates with Laravel's authentication and authorization systems.

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
