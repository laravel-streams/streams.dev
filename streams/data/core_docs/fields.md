---
title: Field Types
description: 'Comprehensive guide to field types in Streams Core.'
sort_order: 2
category: core-concepts
status: live
---

# Field Types

Field types in Streams Core define how data is stored, validated, cast, and presented. Each field type provides specific functionality for handling different kinds of data.

## Basic Field Types

### String

Basic text field for short strings.

```json
{
    "handle": "title",
    "type": "string",
    "config": {
        "default": "",
        "max": 255
    }
}
```

**Configuration Options:**
- `default`: Default value
- `max`: Maximum length
- `min`: Minimum length

### Text

Multi-line text field for longer content.

```json
{
    "handle": "description",
    "type": "text",
    "config": {
        "default": "",
        "rows": 5
    }
}
```

### Number

Numeric field with integer or decimal support.

```json
{
    "handle": "quantity",
    "type": "number",
    "config": {
        "min": 0,
        "max": 999999,
        "step": 1
    }
}
```

### Decimal

Precise decimal numbers for financial calculations.

```json
{
    "handle": "price",
    "type": "decimal",
    "config": {
        "decimals": 2,
        "min": 0
    }
}
```

### Boolean

True/false values.

```json
{
    "handle": "featured",
    "type": "boolean",
    "config": {
        "default": false
    }
}
```

## Date and Time Fields

### Date

Date field without time information.

```json
{
    "handle": "birth_date",
    "type": "date",
    "config": {
        "format": "Y-m-d",
        "default": null
    }
}
```

### Time

Time field without date information.

```json
{
    "handle": "start_time",
    "type": "time",
    "config": {
        "format": "H:i:s"
    }
}
```

### Datetime

Combined date and time field.

```json
{
    "handle": "published_at",
    "type": "datetime",
    "config": {
        "format": "Y-m-d H:i:s",
        "default": "now"
    }
}
```

## Specialized Fields

### Email

Email address with validation.

```json
{
    "handle": "email",
    "type": "email",
    "rules": ["email:rfc,dns"]
}
```

### URL

Website URL with validation.

```json
{
    "handle": "website",
    "type": "url",
    "config": {
        "default_scheme": "https"
    }
}
```

### UUID

Universally unique identifier.

```json
{
    "handle": "id",
    "type": "uuid",
    "config": {
        "default": true
    }
}
```

### Hash

Hashed values (passwords, etc.).

```json
{
    "handle": "password",
    "type": "hash",
    "config": {
        "algorithm": "bcrypt"
    }
}
```

### Slug

URL-friendly strings.

```json
{
    "handle": "slug",
    "type": "slug",
    "config": {
        "source": "title",
        "separator": "-"
    }
}
```

## File and Media Fields

### File

File upload and management.

```json
{
    "handle": "document",
    "type": "file",
    "config": {
        "disk": "public",
        "path": "documents",
        "extensions": ["pdf", "doc", "docx"]
    }
}
```

### Image

Image upload with processing capabilities.

```json
{
    "handle": "featured_image",
    "type": "image",
    "config": {
        "disk": "public",
        "path": "images",
        "extensions": ["jpg", "jpeg", "png", "gif"],
        "max_size": 2048,
        "thumbnails": {
            "small": "150x150",
            "medium": "300x300",
            "large": "800x600"
        }
    }
}
```

**Image Processing:**
```php
// Access different sizes
$entry->featured_image->small()->url();
$entry->featured_image->medium()->url();

// Direct manipulation
$entry->featured_image->resize(400, 300)->url();
$entry->featured_image->crop(200, 200)->url();
```

## Relationship Fields

### Relationship

Link to entries in other streams.

```json
{
    "handle": "author",
    "type": "relationship",
    "config": {
        "related": "users",
        "key_name": "id",
        "value_name": "name"
    }
}
```

### Multiple Relationships

One-to-many or many-to-many relationships.

```json
{
    "handle": "categories",
    "type": "multiple",
    "config": {
        "related": "categories",
        "key_name": "id",
        "value_name": "name"
    }
}
```

**Usage:**
```php
// Single relationship
$post->author->name;

// Multiple relationships
foreach ($post->categories as $category) {
    echo $category->name;
}
```

## Array and Object Fields

### Array

Store arrays of simple values.

```json
{
    "handle": "tags",
    "type": "array",
    "config": {
        "separator": ",",
        "default": []
    }
}
```

### Object

Store complex object data.

```json
{
    "handle": "metadata",
    "type": "object",
    "config": {
        "default": {}
    }
}
```

### Collection

Store collections of related data.

```json
{
    "handle": "attributes",
    "type": "collection",
    "config": {
        "default": []
    }
}
```

## Selection Fields

### Select

Choose from predefined options.

```json
{
    "handle": "status",
    "type": "select",
    "config": {
        "options": {
            "draft": "Draft",
            "published": "Published",
            "archived": "Archived"
        },
        "default": "draft"
    }
}
```

### Multiselect

Choose multiple options.

```json
{
    "handle": "permissions",
    "type": "multiselect",
    "config": {
        "options": {
            "read": "Read",
            "write": "Write",
            "delete": "Delete"
        }
    }
}
```

## Advanced Field Types

### Encrypted

Encrypted field storage.

```json
{
    "handle": "secret",
    "type": "encrypted",
    "config": {
        "key": "app"
    }
}
```

### JSON

Store JSON data with validation.

```json
{
    "handle": "settings",
    "type": "json",
    "config": {
        "schema": {
            "type": "object",
            "properties": {
                "theme": {"type": "string"},
                "notifications": {"type": "boolean"}
            }
        }
    }
}
```

### Color

Color picker field.

```json
{
    "handle": "brand_color",
    "type": "color",
    "config": {
        "format": "hex",
        "default": "#ffffff"
    }
}
```

## Custom Field Types

Create custom field types for specialized needs:

```php
// app/Fields/CustomFieldType.php
namespace App\Fields;

use Streams\Core\Field\FieldType;

class CustomFieldType extends FieldType
{
    protected $inputView = 'fields.custom';
    
    public function cast($value)
    {
        // Custom casting logic
        return $value;
    }
    
    public function modify($value)
    {
        // Modify value before storage
        return $value;
    }
    
    public function restore($value)
    {
        // Restore value from storage
        return $value;
    }
}
```

Register the custom field type:

```php
// In a service provider
Streams::extend('custom', \App\Fields\CustomFieldType::class);
```

## Field Configuration Options

### Common Options

All field types support these common configuration options:

```json
{
    "handle": "field_name",
    "type": "string",
    "name": "Display Name",
    "description": "Field description",
    "placeholder": "Enter value...",
    "help": "Additional help text",
    "default": "default_value",
    "required": true,
    "readonly": false,
    "hidden": false,
    "sortable": true,
    "searchable": true,
    "rules": ["required", "max:255"],
    "example": "Example value"
}
```

### Field Validation

Define validation rules using Laravel's validation syntax:

```json
{
    "handle": "email",
    "type": "email",
    "rules": [
        "required",
        "email:rfc,dns",
        "unique:users,email",
        "max:255"
    ]
}
```

### Conditional Fields

Show/hide fields based on other field values:

```json
{
    "handle": "other_reason",
    "type": "text",
    "config": {
        "conditions": {
            "reason": "other"
        }
    }
}
```

## Field Decorators

Access field values with enhanced functionality:

```php
// Basic value access
$entry->title;

// Decorated value access
$entry->decorate('title')->upper();
$entry->decorate('content')->limit(100);
$entry->decorate('image')->thumbnail('small');

// Magic decorator methods
$entry->titleUpper();
$entry->contentLimit(100);
$entry->imageThumbnail('small');
```

## Field Events

Listen to field-specific events:

```php
// Field value changing
Event::listen('field.modifying:title', function ($value, $entry) {
    return ucwords($value);
});

// Field value casting
Event::listen('field.casting:price', function ($value, $entry) {
    return number_format($value, 2);
});
```

## Best Practices

1. **Choose appropriate types**: Use the most specific field type for your data
2. **Validate thoroughly**: Define comprehensive validation rules
3. **Use descriptive names**: Choose clear, meaningful field handles
4. **Set sensible defaults**: Provide default values where appropriate
5. **Document complex fields**: Use description and help text
6. **Consider performance**: Be mindful of relationship field queries
7. **Plan for localization**: Consider multi-language requirements

## Field Type Reference

| Type | Purpose | Common Use Cases |
|------|---------|------------------|
| `string` | Short text | Names, titles, labels |
| `text` | Long text | Descriptions, content |
| `number` | Integers | Quantities, counts |
| `decimal` | Precise decimals | Prices, measurements |
| `boolean` | True/false | Flags, toggles |
| `date` | Date only | Birth dates, deadlines |
| `datetime` | Date and time | Timestamps, schedules |
| `email` | Email addresses | Contact information |
| `url` | Web addresses | Links, references |
| `file` | File uploads | Documents, attachments |
| `image` | Image uploads | Photos, graphics |
| `relationship` | Links to other streams | Authors, categories |
| `select` | Single choice | Status, type |
| `array` | Multiple values | Tags, keywords |
| `object` | Complex data | Settings, metadata |
