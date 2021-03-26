---
__created_at: 1615267655
__updated_at: 1615267655
title: Introduction
sort: 0
stage: review
enabled: 1
---

## What is Laravel Streams?

Laravel Streams is an open-source **system of interconnected packages** which provides fundamental application standards like data modeling, an API, user interface, and more. 

Our primary mission is to **abstract and standardize data** in a way that maximizes openness and accessibility and radically minimizes overall technical debt in using it.

### Motivation

The ever-changing and widening landscape of web applications, websites, and the like, have stressed the traditions and ideology of popular CMS options. After digging into our own CMS engine we discovered that the problem is in the CMS paradigm. This project results from the complete deconstruction of that paradigm and establishes a new one built upon new fundamental values and principles.

### Use Cases

Laravel Streams is well suited to build a wide variety of applications:

- Websites
- Prototyping
- PWA Backbone
- Headless CMS
- Integrated CMS
- Application Core
- Development Automation

## How does it work?

Laravel Streams focuses primarily on basic JSON descriptions of your data. We call them **streams**.

```json
// streams/music.json
{
    "name": "Music",
    "fields": {
	    "id": "slug",
        "title": "string",
        "mp3": "file",
        "artist": {
            "type": "relationship",
            "related": "artist"
        }
    }
}
```

By default, Laravel Streams leverages a flat-file database engine. All databases supported by Laravel are inherently supported. An adapter interface allows you to tap into any data source you need.

### Building Upon Data

By building upon data structure, we can use stream configurations to help drastically reduce the time required to do things like:

- Validating the data.
- CRUD'ing the data via a fluent and extensive API.
- Generate code from stream configurations.
- Generate stream configurations from data.
- Serving and consuming the data via API.
- Building comprehensive control panels.
- Generating quality, fake data.

### Principle Concerns

Laravel Streams focuses on abstracting these fundamental areas of application development.

- [Data Modeling](streams)
- [Laravel Enhancement](core)
- [Frontend Development](frontend)
- [User Interface](ui)
- [API Readiness](api)
- [CLI Interface](cli)

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
