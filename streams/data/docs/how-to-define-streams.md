---
sort: 1
title: Defining Streams
category: basics
stage: drafting
enabled: false
---
<!-- @todo link up read more links to package documentation -->
<!-- @todo throughout; list other method examples (dev package, interactive) -->

## Introduction

A stream describes data you have or may need. Let's explore the basics of streams while making the default `welcome.blade.php` view more dynamic.

### Getting Started

To begin, create a new Laravel Streams project:

```bash
composer create laravel/laravel newproject

cd newproject

composer require streams/core:2.0.x-dev

php artisan serve
```

## Basic Usage

## Creating Streams

Let's start by creating a stream to describe the resources grid.

Create a new JSON file and save it in the `streams/` directory:

```json
// streams/resources.json
{
    "name": "Resources",
    "config": {
        "format": "json"
    },
    "fields": [
        {
            "handle": "title",
            "type": "string",
            "rules": ["required"]
        },
        {
            "handle": "description",
            "type": "string",
            "rules": ["required"]
        },
        {
            "handle": "url",
            "type": "url",
            "rules": ["required"]
        },
        {
            "handle": "icon",
            "type": "relationship",
            "rules": ["required"],
            "config": {
                "related": "icons"
            }
        },
        {
            "handle": "sort_order",
            "type": "integer",
            "rules": ["min:0"],
            "config": {
                "default": "increment"
            }
        }
    ]
}
```

Now, let's create that related **icons** stream to store our SVG data as a related object.

For now, let's just store these SVG icons within the stream definition itself using the `self` source adapter:

```json
// streams/icons.json
{
    "name": "Icons",
    "config": {
        "adapter": "self"
    },
    "fields": [
        {
            "handle": "id",
            "type": "string",
            "rules": ["required"]
        },
        {
            "handle": "svg",
            "type": "string",
            "rules": ["required"]
        }
    ],
    "data": {
        "book": {
            "svg": "<path d=\"M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253\"></path>"
        },
        "camera": {
            "svg": "<path d=\"M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z\"></path>"
        },
        "comment": {
            "svg": "<svg fill=\"none\" stroke=\"currentColor\" stroke-linecap=\"round\" stroke-linejoin=\"round\" stroke-width=\"2\" viewBox=\"0 0 24 24\" class=\"w-8 h-8 text-gray-500\"><path d=\"M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z\"></path></svg>"
        },
        "comment": {
            "svg": "<svg fill=\"none\" stroke=\"currentColor\" stroke-linecap=\"round\" stroke-linejoin=\"round\" stroke-width=\"2\" viewBox=\"0 0 24 24\" class=\"w-8 h-8 text-gray-500\"><path d=\"M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z\"></path></svg>"
        }
    }
}
```

### Flat-File Data

Let's use JSON files to describe the resources to display:

```json
// streams/data/resources/documentation.json
{
    "title": "Documentaton",
    "description": "Laravel Streams has growing documentation covering every aspect of Laravel Streams and using it. Whether you are new or have previous experience with Laravel, we recommend reading all of the documentation from beginning to end.",
    "icon": "book",
    "url": "https://streams.dev/docs"
}
```

```json
// streams/data/resources/laracasts.json
{
    "title": "Laracasts",
    "description": "Laracasts offers thousands of video tutorials on Laravel, PHP, and JavaScript development. Check them out, see for yourself, and massively level up your development skills in the process.",
    "icon": "book",
    "url": "https://laracasts.com"
}
```

### Listing Stream Data
