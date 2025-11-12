---
title: Introduction
description: 'The core foundation package for Laravel Streams.'
sort_order: 0
category: core-concepts
status: live
---

# Streams Core

The Streams Core package (`streams/core`) is the foundational library that powers the Laravel Streams ecosystem. It provides the essential building blocks for domain-driven design (DDD) in Laravel applications.

## What is Streams Core?

Streams Core is a powerful data modeling and management system that allows you to:

- **Define Domain Models**: Create streams using JSON configuration files
- **Manage Data Sources**: Support for multiple data source types (files, databases, APIs)
- **Handle Field Types**: Rich field type system with validation and casting
- **Repository Pattern**: Clean data access layer with criteria-based querying
- **Asset Management**: Built-in asset and image processing capabilities
- **Extensible Architecture**: Plugin system for addons and custom functionality

## Key Features

### Stream-Based Architecture
Define your application's data structures using JSON configuration files called "streams". Each stream represents a domain entity with fields, validation rules, and behavior.

### Multiple Data Sources
Support for various data storage backends:
- **Filebase**: JSON and YAML file storage
- **Eloquent**: Traditional database models
- **Collection**: In-memory collections
- **Remote**: API-based data sources

### Rich Field Types
Comprehensive field type system including:
- Basic types (string, number, boolean)
- Complex types (relationships, arrays, objects)
- Media types (images, files)
- Specialized types (email, URL, UUID, hash)

### Repository Pattern
Clean separation between data access and business logic with:
- Criteria-based querying
- Caching support
- Event-driven architecture
- Custom repository implementations

## Installation

Install Streams Core via Composer:

```bash
composer require streams/core
```

The package will automatically register its service provider and publish necessary configuration files.

## Quick Start

1. **Define a Stream**: Create a JSON file in `streams/`
2. **Configure Fields**: Define the data structure
3. **Access Data**: Use the repository pattern to interact with entries

```json
// streams/posts.json
{
    "name": "Posts",
    "description": "Blog posts for the website",
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
            "handle": "published_at",
            "type": "datetime"
        }
    ]
}
```

```php
// Access the stream
$posts = Streams::entries('posts');

// Create an entry
$post = $posts->create([
    'title' => 'My First Post',
    'content' => 'This is the content of my first post.',
    'published_at' => now()
]);

// Query entries
$publishedPosts = $posts->where('published_at', '<=', now())->get();
```

## Architecture Overview

Streams Core follows these key architectural principles:

- **Domain-Driven Design**: Streams represent domain concepts
- **Configuration over Code**: JSON-based configuration
- **Repository Pattern**: Clean data access abstraction
- **Event-Driven**: Hooks and listeners throughout the lifecycle
- **Modular**: Extensible through addons and custom implementations

## Next Steps

- [Stream Configuration](streams)
- [Field Types](fields)
- [Repository Usage](repositories)
- [Data Sources](sources)
- [Asset Management](assets)
