---
title: API Endpoints
description: 'Understanding Streams API endpoint structure and usage.'
sort_order: 1
category: core-concepts
status: live
---

# API Endpoints

Streams API automatically generates RESTful endpoints for each stream in your application. This page covers the standard endpoint patterns and how to use them effectively.

## Endpoint Structure

### Base URL Pattern

```
{domain}/{prefix}/{stream}
```

**Examples:**
- `https://api.example.com/api/posts`
- `https://example.com/api/v1/users`
- `http://localhost:8000/api/products`

### Standard REST Endpoints

Every stream gets these standard endpoints:

| Method | Endpoint | Description |
|--------|----------|-------------|
| `GET` | `/api/{stream}` | List all entries |
| `GET` | `/api/{stream}/{id}` | Get single entry |
| `POST` | `/api/{stream}` | Create new entry |
| `PUT` | `/api/{stream}/{id}` | Update entire entry |
| `PATCH` | `/api/{stream}/{id}` | Partial update entry |
| `DELETE` | `/api/{stream}/{id}` | Delete entry |

## List Entries (Index)

Get a paginated list of entries from a stream.

### Request

```http
GET /api/posts HTTP/1.1
Host: example.com
Accept: application/json
```

### Response

```json
{
    "data": [
        {
            "type": "posts",
            "id": "1",
            "attributes": {
                "title": "First Post",
                "content": "This is my first post...",
                "published": true,
                "created_at": "2023-01-01T12:00:00Z",
                "updated_at": "2023-01-01T12:00:00Z"
            },
            "relationships": {
                "author": {
                    "data": {
                        "type": "users",
                        "id": "1"
                    },
                    "links": {
                        "related": "/api/posts/1/author"
                    }
                }
            },
            "links": {
                "self": "/api/posts/1"
            }
        }
    ],
    "included": [],
    "meta": {
        "pagination": {
            "count": 1,
            "current_page": 1,
            "per_page": 15,
            "total": 25,
            "total_pages": 2
        }
    },
    "links": {
        "first": "/api/posts?page=1",
        "last": "/api/posts?page=2",
        "next": "/api/posts?page=2",
        "prev": null
    }
}
```

### Query Parameters

#### Pagination

```http
GET /api/posts?page=2&limit=10
```

#### Filtering

```http
GET /api/posts?filter[published]=true&filter[author]=john
```

#### Sorting

```http
GET /api/posts?sort=-created_at,title
```

#### Including Relationships

```http
GET /api/posts?include=author,categories
```

## Get Single Entry (Show)

Retrieve a specific entry by its ID.

### Request

```http
GET /api/posts/123 HTTP/1.1
Host: example.com
Accept: application/json
```

### Response

```json
{
    "data": {
        "type": "posts",
        "id": "123",
        "attributes": {
            "title": "Specific Post",
            "content": "This is a specific post...",
            "published": true,
            "created_at": "2023-01-01T12:00:00Z",
            "updated_at": "2023-01-02T10:30:00Z"
        },
        "relationships": {
            "author": {
                "data": {
                    "type": "users",
                    "id": "5"
                },
                "links": {
                    "related": "/api/posts/123/author"
                }
            },
            "categories": {
                "data": [
                    {
                        "type": "categories",
                        "id": "1"
                    },
                    {
                        "type": "categories", 
                        "id": "3"
                    }
                ],
                "links": {
                    "related": "/api/posts/123/categories"
                }
            }
        },
        "links": {
            "self": "/api/posts/123"
        }
    }
}
```

### Error Response (Not Found)

```json
{
    "errors": [
        {
            "status": "404",
            "title": "Not Found",
            "detail": "The requested post could not be found.",
            "source": {
                "parameter": "id"
            }
        }
    ]
}
```

## Create Entry (Store)

Create a new entry in the stream.

### Request

```http
POST /api/posts HTTP/1.1
Host: example.com
Content-Type: application/json
Accept: application/json

{
    "data": {
        "type": "posts",
        "attributes": {
            "title": "New Post Title",
            "content": "This is the content of the new post...",
            "published": false
        },
        "relationships": {
            "author": {
                "data": {
                    "type": "users",
                    "id": "5"
                }
            },
            "categories": {
                "data": [
                    {
                        "type": "categories",
                        "id": "1"
                    }
                ]
            }
        }
    }
}
```

### Success Response (201 Created)

```json
{
    "data": {
        "type": "posts",
        "id": "124",
        "attributes": {
            "title": "New Post Title",
            "content": "This is the content of the new post...",
            "published": false,
            "created_at": "2023-01-15T14:30:00Z",
            "updated_at": "2023-01-15T14:30:00Z"
        },
        "relationships": {
            "author": {
                "data": {
                    "type": "users",
                    "id": "5"
                }
            }
        },
        "links": {
            "self": "/api/posts/124"
        }
    }
}
```

### Validation Error Response (422)

```json
{
    "errors": [
        {
            "status": "422",
            "title": "Validation Error",
            "detail": "The title field is required.",
            "source": {
                "pointer": "/data/attributes/title"
            }
        },
        {
            "status": "422",
            "title": "Validation Error", 
            "detail": "The content must be at least 10 characters.",
            "source": {
                "pointer": "/data/attributes/content"
            }
        }
    ]
}
```

## Update Entry (Update)

Update an existing entry. Supports both full replacement (PUT) and partial updates (PATCH).

### Full Update (PUT)

```http
PUT /api/posts/124 HTTP/1.1
Host: example.com
Content-Type: application/json
Accept: application/json

{
    "data": {
        "type": "posts",
        "id": "124",
        "attributes": {
            "title": "Updated Post Title",
            "content": "This is the updated content...",
            "published": true
        }
    }
}
```

### Partial Update (PATCH)

```http
PATCH /api/posts/124 HTTP/1.1
Host: example.com
Content-Type: application/json
Accept: application/json

{
    "data": {
        "type": "posts",
        "id": "124",
        "attributes": {
            "published": true
        }
    }
}
```

### Success Response (200 OK)

```json
{
    "data": {
        "type": "posts",
        "id": "124",
        "attributes": {
            "title": "Updated Post Title",
            "content": "This is the updated content...",
            "published": true,
            "created_at": "2023-01-15T14:30:00Z",
            "updated_at": "2023-01-15T16:45:00Z"
        },
        "links": {
            "self": "/api/posts/124"
        }
    }
}
```

## Delete Entry (Destroy)

Delete an entry from the stream.

### Request

```http
DELETE /api/posts/124 HTTP/1.1
Host: example.com
Accept: application/json
```

### Success Response (204 No Content)

```http
HTTP/1.1 204 No Content
```

### Error Response (Not Found)

```json
{
    "errors": [
        {
            "status": "404",
            "title": "Not Found",
            "detail": "The requested post could not be found."
        }
    ]
}
```

## Relationship Endpoints

Access related data through dedicated relationship endpoints.

### Get Related Resources

```http
GET /api/posts/123/author
GET /api/posts/123/categories
```

### Get Relationship Identifiers

```http
GET /api/posts/123/relationships/author
GET /api/posts/123/relationships/categories
```

**Response:**
```json
{
    "data": {
        "type": "users",
        "id": "5"
    },
    "links": {
        "self": "/api/posts/123/relationships/author",
        "related": "/api/posts/123/author"
    }
}
```

### Update Relationships

```http
PATCH /api/posts/123/relationships/categories
Content-Type: application/json

{
    "data": [
        {
            "type": "categories",
            "id": "1"
        },
        {
            "type": "categories", 
            "id": "2"
        }
    ]
}
```

## Bulk Operations

### Bulk Create

```http
POST /api/posts/bulk
Content-Type: application/json

{
    "data": [
        {
            "type": "posts",
            "attributes": {
                "title": "First Post",
                "content": "Content 1..."
            }
        },
        {
            "type": "posts",
            "attributes": {
                "title": "Second Post",
                "content": "Content 2..."
            }
        }
    ]
}
```

### Bulk Update

```http
PATCH /api/posts/bulk
Content-Type: application/json

{
    "data": [
        {
            "type": "posts",
            "id": "1",
            "attributes": {
                "published": true
            }
        },
        {
            "type": "posts",
            "id": "2", 
            "attributes": {
                "published": true
            }
        }
    ]
}
```

### Bulk Delete

```http
DELETE /api/posts/bulk
Content-Type: application/json

{
    "data": [
        {
            "type": "posts",
            "id": "1"
        },
        {
            "type": "posts",
            "id": "2"
        }
    ]
}
```

## Status Codes

| Code | Meaning | Usage |
|------|---------|-------|
| 200 | OK | Successful GET, PUT, PATCH |
| 201 | Created | Successful POST |
| 204 | No Content | Successful DELETE |
| 400 | Bad Request | Malformed request |
| 401 | Unauthorized | Authentication required |
| 403 | Forbidden | Access denied |
| 404 | Not Found | Resource not found |
| 422 | Unprocessable Entity | Validation errors |
| 500 | Internal Server Error | Server error |

## Content Types

### Request Headers

```http
Content-Type: application/json
Accept: application/json
```

### Response Headers

```http
Content-Type: application/json
Cache-Control: no-cache, private
```

## Rate Limiting

API endpoints may be rate-limited. Check response headers:

```http
X-RateLimit-Limit: 60
X-RateLimit-Remaining: 59
X-RateLimit-Reset: 1609459200
```

## Best Practices

1. **Use proper HTTP methods**: GET for reading, POST for creating, PUT/PATCH for updating, DELETE for removing
2. **Include Accept header**: Always specify `Accept: application/json`
3. **Handle errors gracefully**: Check status codes and parse error responses
4. **Use pagination**: Don't request all data at once
5. **Cache responses**: Use appropriate caching headers
6. **Include relationships wisely**: Only include needed relationships to avoid over-fetching
7. **Validate input**: Ensure data conforms to stream field requirements

## Error Handling

All API errors follow the JSON:API error specification:

```json
{
    "errors": [
        {
            "id": "unique-error-id",
            "status": "422",
            "code": "validation_failed",
            "title": "Validation Error",
            "detail": "The given data was invalid.",
            "source": {
                "pointer": "/data/attributes/email"
            },
            "meta": {
                "field": "email",
                "rule": "unique"
            }
        }
    ]
}
```
