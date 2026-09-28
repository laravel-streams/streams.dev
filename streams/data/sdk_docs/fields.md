---
sort_order: 3
title: Fields
description: 'Field types reference for SDK stream definitions.'
status: ready
---

# Field Types Reference

Fields define the properties of your stream entries. Each field type provides specific validation, input handling, and display formatting.

## String Fields

### Basic String
```json
{
    "handle": "title",
    "type": "string",
    "name": "Title",
    "required": true,
    "config": {
        "max": 255,
        "min": 3,
        "placeholder": "Enter a title..."
    }
}
```

### Slug Field
```json
{
    "handle": "slug",
    "type": "slug",
    "name": "URL Slug",
    "unique": true,
    "config": {
        "slugify": "title",
        "separator": "-"
    }
}
```

### Email Field
```json
{
    "handle": "email",
    "type": "email",
    "name": "Email Address",
    "required": true,
    "config": {
        "unique": true
    }
}
```

### URL Field
```json
{
    "handle": "website",
    "type": "url",
    "name": "Website URL",
    "config": {
        "placeholder": "https://example.com"
    }
}
```

## Text Fields

### Textarea
```json
{
    "handle": "description",
    "type": "textarea",
    "name": "Description",
    "config": {
        "rows": 5,
        "max": 1000
    }
}
```

### Markdown
```json
{
    "handle": "content",
    "type": "markdown",
    "name": "Content",
    "config": {
        "toolbar": ["bold", "italic", "link", "image"],
        "preview": true
    }
}
```

### HTML/Rich Text
```json
{
    "handle": "body",
    "type": "html",
    "name": "Article Body",
    "config": {
        "editor": "tinymce",
        "height": 400
    }
}
```

## Numeric Fields

### Integer
```json
{
    "handle": "quantity",
    "type": "integer",
    "name": "Quantity",
    "config": {
        "min": 0,
        "max": 999999,
        "step": 1
    }
}
```

### Decimal
```json
{
    "handle": "price",
    "type": "decimal",
    "name": "Price",
    "required": true,
    "config": {
        "decimals": 2,
        "min": 0,
        "currency": "USD"
    }
}
```

### Number (General)
```json
{
    "handle": "rating",
    "type": "number",
    "name": "Rating",
    "config": {
        "min": 1,
        "max": 5,
        "step": 0.1
    }
}
```

## Date and Time Fields

### Date
```json
{
    "handle": "published_date",
    "type": "date",
    "name": "Publication Date",
    "config": {
        "format": "Y-m-d",
        "min_date": "today"
    }
}
```

### DateTime
```json
{
    "handle": "created_at",
    "type": "datetime",
    "name": "Created At",
    "config": {
        "format": "Y-m-d H:i:s",
        "timezone": "UTC"
    }
}
```

### Time
```json
{
    "handle": "start_time",
    "type": "time",
    "name": "Start Time",
    "config": {
        "format": "H:i",
        "step": 15
    }
}
```

## Selection Fields

### Boolean
```json
{
    "handle": "is_featured",
    "type": "boolean",
    "name": "Featured",
    "config": {
        "default": false,
        "on_text": "Yes",
        "off_text": "No"
    }
}
```

### Select
```json
{
    "handle": "status",
    "type": "select",
    "name": "Status",
    "required": true,
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

### Enum
```json
{
    "handle": "priority",
    "type": "enum",
    "name": "Priority",
    "config": {
        "options": [
            {"value": "low", "label": "Low", "color": "green"},
            {"value": "medium", "label": "Medium", "color": "yellow"},
            {"value": "high", "label": "High", "color": "red"}
        ]
    }
}
```

### Multiple Select
```json
{
    "handle": "tags",
    "type": "multiple",
    "name": "Tags",
    "config": {
        "options": {
            "tech": "Technology",
            "business": "Business",
            "lifestyle": "Lifestyle"
        },
        "searchable": true
    }
}
```

## File and Media Fields

### File Upload
```json
{
    "handle": "attachment",
    "type": "file",
    "name": "Attachment",
    "config": {
        "disk": "public",
        "path": "attachments",
        "mimes": ["pdf", "doc", "docx"],
        "max_size": "10MB"
    }
}
```

### Image Upload
```json
{
    "handle": "featured_image",
    "type": "image",
    "name": "Featured Image",
    "config": {
        "disk": "public",
        "path": "images",
        "max_width": 1920,
        "max_height": 1080,
        "quality": 85,
        "thumbnails": {
            "thumb": {"width": 300, "height": 200},
            "medium": {"width": 800, "height": 600}
        }
    }
}
```

### Multiple Images
```json
{
    "handle": "gallery",
    "type": "multiple",
    "related": "image",
    "name": "Image Gallery",
    "config": {
        "max_files": 10,
        "sortable": true
    }
}
```

## Relationship Fields

### Single Relationship
```json
{
    "handle": "author",
    "type": "relationship",
    "name": "Author",
    "related": "users",
    "config": {
        "title_field": "name",
        "value_field": "id"
    }
}
```

### Multiple Relationships
```json
{
    "handle": "categories",
    "type": "multiple",
    "related": "categories",
    "name": "Categories",
    "config": {
        "max_items": 5,
        "searchable": true
    }
}
```

### Polymorphic Relationship
```json
{
    "handle": "commentable",
    "type": "polymorphic",
    "name": "Related Item",
    "config": {
        "types": ["posts", "pages", "products"]
    }
}
```

## Advanced Fields

### JSON/Object
```json
{
    "handle": "metadata",
    "type": "object",
    "name": "Metadata",
    "config": {
        "schema": {
            "seo_title": {"type": "string"},
            "seo_description": {"type": "string"},
            "keywords": {"type": "array"}
        }
    }
}
```

### Array
```json
{
    "handle": "features",
    "type": "array",
    "name": "Features",
    "config": {
        "item_type": "string",
        "min_items": 1,
        "max_items": 10
    }
}
```

### UUID
```json
{
    "handle": "id",
    "type": "uuid",
    "name": "ID",
    "config": {
        "default": true,
        "version": 4
    }
}
```

### Hash
```json
{
    "handle": "password",
    "type": "hash",
    "name": "Password",
    "required": true,
    "config": {
        "algorithm": "bcrypt"
    }
}
```

## Field Configuration Options

### Common Options
- **required**: Boolean - whether the field is required
- **unique**: Boolean - whether the field value must be unique
- **default**: Mixed - default value for new entries
- **placeholder**: String - placeholder text for inputs
- **help**: String - help text displayed with the field

### Validation
```json
{
    "config": {
        "rules": [
            "required",
            "min:3",
            "max:255",
            "regex:/^[a-zA-Z0-9]+$/"
        ],
        "messages": {
            "required": "This field is required.",
            "min": "Must be at least 3 characters."
        }
    }
}
```

### Display Options
```json
{
    "config": {
        "table": {
            "sortable": true,
            "searchable": true,
            "width": "200px"
        },
        "form": {
            "hidden": false,
            "readonly": false,
            "wrapper_class": "col-md-6"
        }
    }
}
```

## AI Assistant Field Selection Guide

When helping users choose field types:

### Content Fields
- **String**: Titles, names, short text
- **Slug**: URLs, identifiers
- **Textarea**: Descriptions, summaries
- **Markdown**: Article content, documentation
- **HTML**: Rich formatted content

### Data Fields
- **Integer**: Counts, IDs, quantities
- **Decimal**: Prices, measurements, ratings
- **Boolean**: Flags, toggles, yes/no values
- **Date/DateTime**: Timestamps, schedules, deadlines

### Media Fields
- **Image**: Photos, thumbnails, logos
- **File**: Documents, downloads, attachments
- **Multiple**: Galleries, file collections

### Relationship Fields
- **Relationship**: Authors, categories, references
- **Multiple**: Tags, collections, many-to-many

Choose field types based on:
1. **Data nature**: What kind of information is stored?
2. **Input method**: How will users enter this data?
3. **Display needs**: How will this data be shown?
4. **Validation requirements**: What rules apply?
5. **Search/filter needs**: Will users search by this field?
