---
title: Quickstart
section: getting_started
intro: 
sort: 3
---

## Install Streams
Just like Laravel, Streams utilizes Composer to manage its dependencies. So before you can begin using streams, [make sure you have Composer installed on your machine.](https://getcomposer.org/download/)

### Install using Composer Create-Project
`composer create-project anomaly/streams yourproject.test --prefer-dist --stability=dev`

### Configure Streams
Once you have you project setup, you can simply follow the instructions from Laravel to [configuring your project.](https://laravel.com/docs/7.x/installation#configuration)

## Hello world 
If you are interested in creating a website, you need pages. So let's start with an example where we show a navigation of our existing pages.

### Define pages data structure
```json
// streams/pages.json
{
    "name": "pages",
    "slug": "pages",
    "route": "/{id}",
    "template": "page",
    "fields": {
        "title": "text",
        "doc": {
            "type": "entry",
            "stream": "docs"
        },
        "docs": {
            "type": "entries",
            "stream": "docs"
        }
    }
}
```
- I dont directly understand why we define slug as "pages"?
- Route has an ID, where does that come from? Why don't this stucture has a field id?





## Route an entry
## Updating Streams
