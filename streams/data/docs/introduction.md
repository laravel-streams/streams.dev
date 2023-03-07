---
__created_at: 1615267655
__updated_at: 1615267655
title: Introduction
sort: 0
stage: review
enabled: 1
---

## Introduction

Laravel Streams is a content management *framework* for **Laravel developers**. It provides the tools for you to build what you need in a way that maximixes your effectiveness and minimizes technical debt and resource requirements.

- [Streams Core](/packages/streams/core)
- [Streams UI](/packages/streams/ui)
- [Streams API](/packages/streams/api)
- [Streams API](/packages/streams/api)

More packages

### Use Cases

Laravel Streams and its components are well suited to build various applications:

- Websites
- Prototyping
- PWA Backbone
- Headless CMS
- Integrated CMS
- Code Generator
- Application Core
- Project Bootstraps
- Development Automation


## How does it work?

Laravel Streams focuses first on basic JSON descriptions of your data. We call them **streams**, hence our namesake.

```json
// streams/music.json
{
    "name": "Music",
    "fields": [
        {
            "handle": "id",
            "type": "slug"
        },
        {
            "handle": "title",
            "type": "string"
        },
        {
            "handle": "mp3",
            "type": "file"
        },
        {
            "handle": "artist",
            "type": "relationship",
            "config": {
                "related": "artists"
            }
        }
    ]
}
```

By default, Laravel Streams leverages a flat-file database engine. All databases supported by Laravel are inherently supported. An adapter interface allows you to tap into any data source you need.

### Building Upon Data

By building upon data structure, we can use stream configurations to help drastically reduce the time required to do things like:

- Validating the data.
- CRUD'ing the data via a fluent and extensive API.
- Generate schema from stream configurations.
- Generate code from stream configurations.
- Generate stream configurations from data.
- Serving and consuming the data via API.
- Building comprehensive control panels.
- Generating quality fake data.

### Development Abstraction

Our overarching focus is to **abstract, normalize, and optimize** development systems and the work required from many Laravel projects. We try to restrict this to our core focus of data abstraction and access.

## Core Packages

The features of this project are split into thier relevant packages:

- [Streams Core](/packages/streams/core)
- [Streams UI](/packages/streams/ui)
- [Streams API](/packages/streams/api)
- [Streams SDK](/packages/streams/sdk)
