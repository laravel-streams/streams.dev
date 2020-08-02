---
title: Introduction
intro: Streams is your data-first multitool for building Laravel applications.
sort: 0
---

## What is streams?

Streams is a way of building higher quality Laravel applications faster by focusing on the characteristics and structure of it's related data first and foremost.

## Why use it?

Streams allows you to access and use all your data universally the same and with the same code.

You can query and manage all your data with the same robust and powerful API:

```php
$entry = Streams::entries('contacts')->orderBy('name', 'ASC')->first();

echo $entry->expand('email')->mailto($entry->name);

$entry->email = 'admin@pyrocms.com';

$entry->save();
```

> Streams is strictly auxillary. All Laravel code and design systems work as intended.

### Streams UI
### Streams API
### Service Based/Extensible/Addons/Etc

## Who is it for?

- Development Teams
- Production Teams
- Individuals/Freelancers

## How does it work?

### Data-first

Everything starts by describing a new or existing data structure we call, a `stream`.

```json
// streams/contacts.json
{
    "name": "Contacts",
    "fields": {
        "name": "text",
        "email": "email",
        "website": "url",
    }
}
```

Streams assumes built-in flat-file data storage by default but supports Eloquent models and Laravel databases as well as other sources via [adapters](adapters).

Below is a simple example of a flat-file data entry using the default JSON format.

```json
// streams/data/contacts/ryan_thompson.json
{
    "name": "Ryan Thompson",
    "email": "ryan@pyrocms.com",
    "website": "https://ryanthepyro.com/",
}
```

> Markdown and text files are also supported with front-matter data.

### Bottom-up

Stream-configured data-structures automate application functionality and code. 
