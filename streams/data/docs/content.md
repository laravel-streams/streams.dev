---
title: Content
description: 'Pages and other content fragments.'
sort_order: 3
category: basics
status: ideation
---

## Introduction

This page examines common content models and how to use them in Streams.

## Pages

Let's start by looking at a basic pages stream:

```json
// streams/pages.json
{
    "source": {
        "type": "filebase",
        "format": "html"
    },
    "route": [
        {
            "handle": "view",
            "uri": "{path}"
            "defer": true,
            "parse": true,
        }
    ],
    "fields": {
        [
            "handle": "title",
            "required": true,
        ],
        [
            "handle": "path",
            "required": true,
            "unique": true,
        ],
        [
            "handle": "body"
        ]
    }
}
```

#### Example Page

The pages above will be stored similar to the below data:

```html
@verbatim<!-- streams/data/pages/welcome.html -->
---
title: Welcome to Streams
path: /
---

@include('partials.topbar')

<h1>{{ $entry->title }}</h1>@endverbatim
```



## Posts
## Blocks

Block content is represented as an array of structured data where each item is tied to a particular view fragment.

For example, you might have a block for a gallery of images, a group of links, or wysiwyg content. 

```json
// streams/pages.json
{
    "fields": {
        [
            "handle": "content",
            "type": "array",
            "config": {
                "allowed": [
                    {"stream": "gallery_blocks"},
                    {"structure": [
                        {"handle": "title", "type": "string"}
                        {"handle": "wysiwyg", "type": "wysiwyg"}
                    ]},
                ]
            }
        ]
    }
}
```

## Partials

Partials are much like view template partials in concept. They are a parts of a view template that can be included in other views. 

```json
// streams/partials.json
{
    "source": {
        "type": "filebase",
        "format": "html"
    },
    "fields": {
        [
            "handle": "id",
            "required": true,
            "unique": true,
            "config": {
                "default": true
            }
        ],
        [
            "handle": "name",
            "required": true,
        ],
        [
            "handle": "body"
        ]
    }
}
```

```json
// streams/pages.json
{
    "fields": {
        [
            "handle": "partial",
            "type": "relationship",
            "config": {
                "related": "partials"
            }
        ]
    }
}
```
