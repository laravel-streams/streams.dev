---
title: Streams
description: 'Configure and manage streams in Streams Core.'
sort_order: 1
category: core-concepts
status: live
---

# Streams

Streams are the core abstraction in Streams Core. They define domain entities with their fields, validation rules, data sources, and behavior. Think of streams as enhanced models that are configured rather than coded.

## Defining Streams

Streams are defined using JSON configuration files in the `streams/` directory. The filename serves as the stream's unique identifier.

### Basic Stream Structure

```json
// streams/users.json
{
    "name": "Users",
    "description": "Application users",
    "fields": [
        {
            "handle": "name",
            "type": "string",
            "rules": ["required", "max:255"]
        },
        {
            "handle": "email",
            "type": "email",
            "rules": ["required", "email", "unique"]
        }
    ]
}
```

### Stream Configuration Options

| Option | Description | Required |
|--------|-------------|----------|
| `name` | Human-readable name for the stream | No |
| `description` | Description of the stream's purpose | No |
| `fields` | Array of field definitions | Yes |
| `config` | Stream-specific configuration | No |
| `routes` | Associated route definitions | No |
| `rules` | Stream-level validation rules | No |

## Data Sources

Configure how and where your stream data is stored using the `config.source` option.

### Filebase Source (Default)

Store data in JSON or YAML files:

```json
{
    "config": {
        "source": {
            "type": "filebase",
            "path": "streams/data/users",
            "format": "json"
        }
    }
}
```

### Eloquent Source

Use traditional Laravel Eloquent models:

```json
{
    "config": {
        "source": {
            "type": "eloquent",
            "model": "App\\Models\\User"
        }
    }
}
```

### Collection Source

Use in-memory collections for static data:

```json
{
    "config": {
        "source": {
            "type": "collection",
            "data": [
                {"id": 1, "name": "John Doe"},
                {"id": 2, "name": "Jane Smith"}
            ]
        }
    }
}
```

### Self Source

Reference data within the stream configuration:

```json
{
    "config": {
        "source": {
            "type": "self"
        }
    },
    "data": {
        "item1": {"name": "First Item"},
        "item2": {"name": "Second Item"}
    }
}
```

## Stream Configuration

### Abstract Classes

Customize the underlying classes used by your stream:

```json
{
    "config": {
        "abstract": "App\\Streams\\CustomEntry",
        "repository": "App\\Repositories\\CustomRepository",
        "criteria": "App\\Criteria\\CustomCriteria",
        "collection": "App\\Collections\\CustomCollection"
    }
}
```

### Caching

Enable caching for improved performance:

```json
{
    "config": {
        "cache": {
            "enabled": true,
            "ttl": 3600,
            "tags": ["users", "cache"]
        }
    }
}
```

### Validation

Define stream-level validation rules:

```json
{
    "rules": {
        "email": ["unique:users,email"],
        "name": ["required_with:email"]
    }
}
```

## Working with Streams

### Access Stream Repositories

```php
// Get the stream repository
$users = Streams::repository('users');

// Alternative syntax
$users = Streams::entries('users');
```

### Create Entries

```php
// Create a single entry
$user = $users->create([
    'name' => 'John Doe',
    'email' => 'john@example.com'
]);

// Create multiple entries
$users->createMany([
    ['name' => 'Jane Smith', 'email' => 'jane@example.com'],
    ['name' => 'Bob Wilson', 'email' => 'bob@example.com']
]);
```

### Query Entries

```php
// Get all entries
$allUsers = $users->all();

// Find by ID
$user = $users->find('user-id-123');

// Query with criteria
$activeUsers = $users->where('active', true)->get();

// Complex queries
$results = $users
    ->where('created_at', '>', now()->subDays(30))
    ->orderBy('name')
    ->limit(10)
    ->get();
```

### Update Entries

```php
// Update single entry
$user = $users->find('user-id-123');
$user->update(['name' => 'Updated Name']);

// Update multiple entries
$users->where('status', 'pending')
      ->update(['status' => 'active']);
```

### Delete Entries

```php
// Delete single entry
$users->delete('user-id-123');

// Delete multiple entries
$users->where('inactive', true)->delete();
```

## Stream Routes

Define routes associated with your stream:

```json
{
    "routes": [
        {
            "handle": "index",
            "uri": "/users",
            "method": "GET",
            "uses": "UserController@index"
        },
        {
            "handle": "show",
            "uri": "/users/{id}",
            "method": "GET",
            "uses": "UserController@show"
        }
    ]
}
```

### Route Shortcuts

Use shorthand notation for common patterns:

```json
{
    "routes": {
        "index": "/users",
        "show": "/users/{id}",
        "create": "/users/create",
        "edit": "/users/{id}/edit"
    }
}
```

## Stream Events

Streams Core fires events throughout the entry lifecycle:

### Available Events

- `entry.creating` - Before creating an entry
- `entry.created` - After creating an entry
- `entry.updating` - Before updating an entry
- `entry.updated` - After updating an entry
- `entry.deleting` - Before deleting an entry
- `entry.deleted` - After deleting an entry

### Listening to Events

```php
// In a service provider
Event::listen('streams.entry.creating:users', function ($entry) {
    // Modify entry before creation
    $entry->slug = Str::slug($entry->name);
});

// Using stream-specific listeners
Streams::listen('users', 'creating', function ($entry) {
    // Auto-generate username
    $entry->username = Str::slug($entry->name);
});
```

## Advanced Configuration

### Custom Field Types

Register custom field types for your streams:

```php
// In a service provider
Streams::extend('custom_field', CustomFieldType::class);
```

### Stream Macros

Add custom methods to stream repositories:

```php
// In a service provider
Streams::macro('published', function () {
    return $this->where('published', true);
});

// Usage
$publishedPosts = Streams::entries('posts')->published()->get();
```

### Stream Templates

Use templates for consistent stream configurations:

```json
{
    "extends": "templates/content",
    "fields": [
        {
            "handle": "custom_field",
            "type": "string"
        }
    ]
}
```

## Best Practices

1. **Use descriptive names**: Choose clear, meaningful names for your streams
2. **Organize fields logically**: Group related fields together
3. **Validate early**: Define validation rules at the field and stream level
4. **Cache appropriately**: Enable caching for frequently accessed streams
5. **Version your schemas**: Consider migration strategies for schema changes
6. **Document your streams**: Use the description field to explain the stream's purpose

## Examples

### Blog Post Stream

```json
// streams/posts.json
{
    "name": "Blog Posts",
    "description": "Articles and blog posts for the website",
    "config": {
        "source": {
            "type": "filebase",
            "path": "content/posts",
            "format": "md"
        }
    },
    "fields": [
        {
            "handle": "title",
            "type": "string",
            "rules": ["required", "max:255"]
        },
        {
            "handle": "slug",
            "type": "slug",
            "config": {
                "source": "title"
            }
        },
        {
            "handle": "content",
            "type": "text",
            "rules": ["required"]
        },
        {
            "handle": "author",
            "type": "relationship",
            "config": {
                "related": "users"
            }
        },
        {
            "handle": "published_at",
            "type": "datetime"
        },
        {
            "handle": "featured_image",
            "type": "image"
        }
    ],
    "routes": {
        "index": "/blog",
        "show": "/blog/{slug}"
    }
}
```

### Product Catalog Stream

```json
// streams/products.json
{
    "name": "Products",
    "description": "E-commerce product catalog",
    "config": {
        "source": {
            "type": "eloquent",
            "model": "App\\Models\\Product"
        },
        "cache": {
            "enabled": true,
            "ttl": 1800
        }
    },
    "fields": [
        {
            "handle": "name",
            "type": "string",
            "rules": ["required", "max:255"]
        },
        {
            "handle": "sku",
            "type": "string",
            "rules": ["required", "unique"]
        },
        {
            "handle": "price",
            "type": "decimal",
            "config": {
                "decimals": 2
            }
        },
        {
            "handle": "category",
            "type": "relationship",
            "config": {
                "related": "categories"
            }
        },
        {
            "handle": "images",
            "type": "multiple",
            "config": {
                "related": "file"
            }
        },
        {
            "handle": "in_stock",
            "type": "boolean",
            "config": {
                "default": true
            }
        }
    ]
}
```
