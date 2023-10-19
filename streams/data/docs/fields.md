---
sort_order: 5
title: Fields
description: 'Fields, types, and inputs are documented here.'
category: core-concepts
status: ideation
---

## Introduction

Fields represent the type and characteristics of your stream data. For example a "name" field would likely be a **string** field type.

Fields are strictly concerned with data. Please see the [UI package](../ui/introduction) for configuring field [inputs](../ui/inputs).

## Defining Fields

Fields can be defined within the JSON [configuration for your streams](streams#defining-streams). You can get started by simply defining fields by `handle` and their `type` respectively.

#### Basic Example

```json
// streams/contacts.json
{
    "fields": [
        {
            "handle": "title",
            "type": "string"
        }
    ]
}
```

#### Full Example

To define more information about the field use an array:

```json
// streams/contacts.json
{
    "fields": [
        {
            "handle": "title",
            "name": "Title",
            "description": "The title of the film.",
            "type": "string",
            "rules": ["min:4"],
            "config": {
                "default": "Untitled"
            },
            "example": "Star Wars: The Force Awakens",
            "protected": false
        }
    ]
}
```

### Field Validation

Define [Laravel validation rules](https://laravel.com/docs/validation#available-validation-rules) for fields and they will be merged the [stream validation rules](streams#stream-validation).

```json
// streams/contacts.json
{
    "fields": [
        {
            "handle": "name",
            "type": "string",
            "rules": ["required", "max:100"]
        },
        {
            "handle": "email",
            "type": "email",
            "rules": ["required", "email:rfc,dns"]
        },
        {
            "handle": "company",
            "type": "string",
            "rules": ["required", "unique"]
        }
    ]
}
```


## Basic Usage

Values are stored as an [image source](images#image-sources)

```php
Image::make($entry->profile_image)->url();
```

### Field Decorators

Field decorators provide expanded function to entry attributes like a universal presenter.

The below example demonstrates the `image` field decorator:

```php
$entry->decorate('profile_image')->url();
```

You may also use magic methods derived from "camel casing" the field's handle to invoke decoration.

```php
$entry->profileImage()->url();
```



## Field Types

The field type is responsible for validating, casting, and more for its specific data type.

### String

```json
{
    "type": "string"
}
```

### URL

```json
{
    "type": "url"
}
```

### UUID

```json
{
    "type": "uuid",
    "config": {
        "default": true
    }
}
```

### Hash

```json
{
    "type": "hash"
}
```

### Slug

```json
{
    "type": "slug",
    "config": {
        "seperator": "-"
    }
}
```

### Email

```json
{
    "type": "email"
}
```

### Encrypted

```json
{
    "type": "encrypted"
}
```

### Color

```json
{
    "type": "Color"
}
```

### Markdown

```json
{
    "type": "markdown"
}
```


### Number

```json
{
    "type": "number"
}
```

### Integer

```json
{
    "type": "integer"
}
```

### Decimal

```json
{
    "type": "decimal"
}
```

### Boolean

```json
{
    "type": "boolean"
}
```

### Date

```json
{
    "type": "date"
}
```

### Time

```json
{
    "type": "time"
}
```

### Datetime

```json
{
    "type": "datetime"
}
```

### Enum

```json
{
    "type": "enum",
    "config": {
        "options": {
            "foo": "Foo",
            "bar": "Bar",
        }
    }
}
```

### Select

```json
{
    "type": "select",
    "config": {
        "options": {
            "foo": "Foo",
            "bar": "Bar",
        }
    }
}
```

### Multiselect

```json
{
    "type": "multiselect",
    "config": {
        "options": {
            "foo": "Foo",
            "bar": "Bar",
        }
    }
}
```

### Array

```json
{
    "type": "array",
    "config": {
        "allowed": [
            {"type": "string"},
            {"type": "number"}
        ]
    }
}
```

### Object

```json
{
    "type": "object",
    "config": {
        "allowed": [
            {
                "stream": "example"
            },
            {"type": "number"}
        ]
    }
}
```

### File

```json
{
    "type": "file",
    "config": {
        "allowed": [
            "application/*",
            "text/*"
        ]
    }
}
```

### Image

```json
{
    "type": "image",
    "config": {
        "allowed": [
            "image/*"
        ]
    }
}
```

### Relationship

```json
{
    "type": "relationship",
    "config": {
        "related": "stream"
    }
}
```

### Polymorphic

```json
{
    "type": "polymorphic",
    "config": {
        "allowed": [
            "stream1",
            "stream2"
        ]
    }
}
```
