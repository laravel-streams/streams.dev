---
sort_order: 0
title: Introduction
description: 'Development workflow and scaffolding for Laravel Streams projects.'
status: ready
---

# Laravel Streams SDK


## What is Laravel Streams?

Laravel Streams is a powerful data modeling and management system that treats everything as abstract streams of data. Think of it as a meta-framework that sits on top of Laravel, providing:

- **Dynamic Schema Definition**: Define data structures using JSON configuration files providing Eloquent or unified modeling.
- **Flexible Data Sources**: Work with databases, files, APIs, or any custom data source as a unified interface.
- **Rich Field Types**: Built-in support for strings, numbers, images, relationships, and more.
- **Real-time UI Generation**: Automatically create forms, tables, admin interfaces, and more.

## AI Assistant Guide

This SDK is specifically designed to help AI assistants understand and implement Laravel Streams applications. Here's what AI assistants need to know:

### Core Concepts

1. **Streams**: Data structures defined in JSON files (stored in `streams/` directory)
2. **Entries**: Individual records within a stream. Can be an Eloquent model with the Entry interface / fluent trait from core. OR an Entry from core also sharing that interface. 
3. **Fields**: Properties of entries with specific types and validation rules.
4. **Repositories**: Handle data persistence and retrieval.
5. **Criteria**: Abstract the query patterns of the Eloquent ORM and uses a core adapter to extend for each data source.
6. **Sources**: Define data shape and location information (database, files, APIs)

### Available Commands

The SDK provides powerful artisan commands for scaffolding:

```bash
# Create a new stream definition
php artisan make:stream blog_posts

# Create entries interactively
php artisan make:entry blog_posts

# Generate admin panels
php artisan streams:admin blog_posts

# Create Livewire components
php artisan streams:component blog_posts

# Generate complete CRUD
php artisan streams:crud blog_posts

# Generate schema files
php artisan streams:schema
```

### TALL Stack Integration

The SDK automatically generates:

- **Tailwind CSS**: Pre-styled components with utility classes
- **Alpine.js**: Interactive behavior for forms and data manipulation
- **Laravel**: Controllers, routes, and middleware
- **Livewire**: Reactive components for real-time user interfaces

## Quick Start for AI Assistants

When helping users build with Streams:

1. **Start with Stream Definition**: Ask about the data structure they need
2. **Define Fields**: Help choose appropriate field types and validation
3. **Generate Components**: Use SDK commands to scaffold TALL stack components
4. **Customize UI**: Modify generated templates to match requirements
5. **Add Business Logic**: Implement custom methods and relationships

## Common Patterns

### Blog Example
```json
{
    "name": "Blog Posts",
    "handle": "blog_posts",
    "fields": [
        {"handle": "title", "type": "string", "required": true},
        {"handle": "slug", "type": "slug", "unique": true},
        {"handle": "content", "type": "markdown"},
        {"handle": "featured_image", "type": "image"},
        {"handle": "published_at", "type": "datetime"},
        {"handle": "author", "type": "relationship", "related": "users"}
    ]
}
```

### E-commerce Product
```json
{
    "name": "Products",
    "handle": "products",
    "fields": [
        {"handle": "name", "type": "string", "required": true},
        {"handle": "price", "type": "decimal", "required": true},
        {"handle": "images", "type": "multiple", "related": "image"},
        {"handle": "category", "type": "relationship", "related": "categories"},
        {"handle": "in_stock", "type": "boolean", "default": true}
    ]
}
```

## AI Prompting Best Practices

When working with this SDK, AI assistants should:

1. **Ask Clarifying Questions**: Understand the domain and requirements first
2. **Suggest Field Types**: Recommend appropriate field types based on use case
3. **Consider Relationships**: Identify connections between different data entities
4. **Think Mobile-First**: Generate responsive, accessible components
5. **Include Validation**: Add appropriate validation rules and error handling
6. **Suggest Admin Features**: Recommend admin panel features for content management

## Next Steps

Continue reading the documentation to learn about:
- [Stream Definition Guide](01-streams.md)
- [Field Types Reference](02-fields.md)
- [TALL Stack Components](03-tall-components.md)
- [Admin Panel Generation](04-admin-panels.md)
- [Advanced Patterns](05-advanced-patterns.md)
- [AI Assistant Prompts](06-ai-prompts.md)
