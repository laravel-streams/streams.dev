---
title: Fields
nav_title: Fields
description: Fields, types, and inputs are documented here.
section: concepts
category: core-concepts
package: core
order: 30
tags: [core, fields]
status: ready
---

## Introduction

Fields represent the type and characteristics of your stream data. For example a "name" field would likely be a **string** field type.

Fields are strictly concerned with data. Please see the [UI package](/docs/ui/introduction) for configuring field [inputs](/docs/ui/inputs).

## Defining Fields

Fields can be defined within the JSON [configuration for your streams](/docs/streams#defining-streams). You can get started by simply defining fields by `handle` and their `type` respectively.

#### Basic Example

In `streams/contacts.json`:

```json
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

In `streams/contacts.json`:

```json
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

Define [Laravel validation rules](https://laravel.com/docs/validation#available-validation-rules) for fields and they will be merged the [stream validation rules](/docs/streams#stream-validation).

In `streams/contacts.json`:

```json
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

Values are stored as an [image source](/docs/images#image-sources)

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

The field type is responsible for validating, casting, and more for its specific data type. These are the 24 types registered by Core (`streams.core.field_types`); apps and addons can register more. Each example is a complete field in list form, so it validates against the [stream definition schema](/docs/sdk/stream-schema).

### String

```json
{
    "handle": "title",
    "type": "string"
}
```

### URL

```json
{
    "handle": "website",
    "type": "url"
}
```

### UUID

`"default": true` in `config` generates a UUID when the attribute is missing.

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

```json
{
    "handle": "password",
    "type": "hash"
}
```

### Slug

`config.separator` sets the word separator (default `-`).

```json
{
    "handle": "slug",
    "type": "slug",
    "config": {
        "separator": "-"
    }
}
```

### Email

```json
{
    "handle": "email",
    "type": "email"
}
```

### Encrypted

```json
{
    "handle": "api_token",
    "type": "encrypted"
}
```

### Color

Values are stored lowercase and must parse as a color. The decorator adds `hex()`, `rgb()`, `rgba()`, and the individual channels; `config.format` picks the default output (`hex`).

```json
{
    "handle": "brand_color",
    "type": "color"
}
```

### Number

```json
{
    "handle": "rating",
    "type": "number"
}
```

### Integer

```json
{
    "handle": "age",
    "type": "integer"
}
```

### Decimal

`config.precision` rounds to that many decimal places.

```json
{
    "handle": "price",
    "type": "decimal",
    "config": {
        "precision": 2
    }
}
```

### Boolean

```json
{
    "handle": "published",
    "type": "boolean"
}
```

### Date

`config.format` is the storage format (default `Y-m-d`).

```json
{
    "handle": "birthday",
    "type": "date"
}
```

### Time

`config.format` defaults to `H:i:s`; `config.timezone` defaults to `app.timezone`.

```json
{
    "handle": "opens_at",
    "type": "time"
}
```

### Datetime

`config.format` defaults to `Y-m-d H:i:s`; `config.timezone` defaults to `app.timezone`.

```json
{
    "handle": "published_at",
    "type": "datetime",
    "config": {
        "timezone": "UTC"
    }
}
```

### Enum

`enum` is an alias of `select`. Both need `config.options`.

```json
{
    "handle": "size",
    "type": "enum",
    "config": {
        "options": {
            "sm": "Small",
            "lg": "Large"
        }
    }
}
```

### Select

```json
{
    "handle": "status",
    "type": "select",
    "config": {
        "options": {
            "draft": "Draft",
            "live": "Live"
        }
    }
}
```

### Multiselect

```json
{
    "handle": "tags",
    "type": "multiselect",
    "config": {
        "options": {
            "news": "News",
            "guides": "Guides"
        }
    }
}
```

### Array

`config.items` lists the allowed item types. Each item must pass at least one of them. Set `config.enforce_items` to `false` to skip the check, or use `config.related` (or `config.stream`) to cast items to entries of a stream.

```json
{
    "handle": "scores",
    "type": "array",
    "config": {
        "items": [
            {"type": "string"},
            {"type": "number"}
        ]
    }
}
```

### Object

`config.allowed` lists the value types an object may be. Each item names a `stream` (an entry of that stream), a `generic` class, or a `prototype` class. Without `allowed`, any object is accepted.

```json
{
    "handle": "address",
    "type": "object",
    "config": {
        "allowed": [
            {"stream": "addresses"},
            {"generic": "Illuminate\\Support\\Collection"}
        ]
    }
}
```

### File

The value is a path string. The decorator adds file helpers. Core does not restrict file types; add Laravel `rules` for that.

```json
{
    "handle": "attachment",
    "type": "file"
}
```

### Image

A `file` whose decorator works with the [image manager](/docs/images).

```json
{
    "handle": "profile_image",
    "type": "image"
}
```

### Relationship

`config.related` names the related stream. `related` next to `type` is ignored. Add `"multiple": true` to store a list of keys, and `key_name` if the related stream is not keyed by `id`.

```json
{
    "handle": "author_id",
    "type": "relationship",
    "config": {
        "related": "authors"
    }
}
```

### Polymorphic

Stores a reference to an entry of any stream as `{"@stream": "posts", "id": "..."}` and restores the entry when read. `config.related` optionally documents the streams you expect; Core does not enforce it.

```json
{
    "handle": "commentable",
    "type": "polymorphic",
    "config": {
        "related": ["posts", "pages"]
    }
}
```
