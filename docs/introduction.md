---
__created_at: 1615267655
__updated_at: 1615267655
title: Introduction
intro: Welcome to Laravel Streams
sort: 0
stage: review
enabled: 1
---

## What is Laravel Streams?

Laravel Streams is a system of **low-code** utilities providing an optimized foundation and workflow for **Laravel development**.

Application fundamentals like **data modeling**, **API interaction**, **UI**, **control panels**, and more are defined with code-configured JSON files and establish best-practices design principles to support your work.


### Motivation

The ever-changing and widening landscape of web applications, websites, and the like, has stressed the traditions and ideology of popular CMS options. And, after digging into our own CMS engine, we discovered that the problem is in the CMS paradigm. This project results from the complete deconstruction of that paradigm and establishes a new one built upon new fundamental values and principles.


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

### Principle Concerns

Laravel Streams focuses on these fundamental areas of application development.

- [Data Modeling](streams)
- [Laravel Development](core)
- [Frontend Development](frontend)
- [User Interface](ui)
- [API Readiness](api)

### What's Next?

Time to get your feet wet!

@foreach (Streams::entries('docs')->where('category', 'getting_started')->orderBy('sort', 'asc')->get() as $doc)
- [{{$doc->link_title ?: $doc->title}}]({{$doc->id}})
@endforeach

## Core Packages

Know what you are looking for already? Dive right into our core packages.

- [Streams Core](core/introduction)
- [Streams UI](ui/introduction)
- [Streams API](api/introduction)
- [Streams CLI](cli/introduction)


## Community Resources

- <a href="https://discord.gg/vhz8NZC" rel="noreferrer noopener">Discord</a>
- <a href="https://stackoverflow.com/search?q=laravel+streams" rel="noreferrer noopener">Stack Exchange</a>
- <a href="https://github.com/laravel-streams/streams" rel="noreferrer noopener">GitHub</a>
- <a href="https://www.youtube.com/channel/UC4a-uVtWOHNCduY5T7_Q4wA" rel="noreferrer noopener">YouTube</a>
