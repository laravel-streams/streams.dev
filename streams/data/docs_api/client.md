---
title: Client
category: basics
stage: outlining
enabled: true
sort: 99
---

## Introduction

In addition to the API server, we also provide a Javascript [API Client](https://github.com/laravel-streams/api-client) to interact with the server. The API Client is consistent in design to its PHP counterpart.

## Installation

You may use any NPM package manager to install the API Client:

```bash
yarn add @laravel-streams/api-client
```

```bash
npm install @laravel-streams/api-client
```


## Getting Started

To get started, import `Streams` and instantiate it with the `baseURL` of your Streams API server. The resulting streams object is equivelant to the PHP Streams facade.

```js
import { Streams } from "@laravel-streams/api-client";

export const streams = new Streams({
    baseURL: "http://127.0.0.1:8000/api",
});
```

### Stream Instances

You can return stream instances for later use using the `make` method.

```js
const stream: Stream = await streams.make("contacts");
```

Streams can return their respective repositories and entry criteria using corresponding methods.

```js
const repository: Repository = stream.getRepository();
const query: Criteria = stream.getEntries();
```


### Repositories

You can return a streams entry repository using the `repository` method.

```js
const repository: Repository = await streams.respository("contacts");
```

The repository has the same methods available as it's [PHP counterpart](/docs/core/repositories).

```js
let entries: Collection = await repository.all();

let entries: Collection = await repository.findAll([1, 2, 3, 64]);

const first: Entry = entries.first();

const entry: Entry = await repository.create({
    name: "John Doe",
    age: 30,
});

entry.name = 'John Smith';

await repository.save(entry);

await repository.delete(entry);
```


### Queries

You can return an [entry criteria](/docs/core/querying) using the `entries` method.

```js
const query: Criteria = await streams.entries("contacts");
```

The entry criteria also has the same methods available as it's [PHP counterpart](/docs/core/querying).

```js
let entries: Collection = await query
    .where('age', '<=', 30)
    .orderBy('age', 'desc')
    .get();

let paginator: Paginator = await query
    .orderBy('age', 'desc')
    .paginate(10);
```


## Entries

Working with entry instances is consistent to their PHP counterparts.

```js
const entry: Entry = await repository.first();

entry.name = 'John Smith';

await entry.save();

await entry.delete();

const obj = entry.serialize();
```


## Caching

The API Client comes with built-in ETag support. To get started, enable the feature on the client.

```js
import { Streams } from "@laravel-streams/api-client";

export const streams = new Streams({
    baseURL: "http://127.0.0.1:8000/api",
    etag: {
        enabled:true
    }
});
```

You must also [enable cache](caching#configuration) on the desired stream:

```json
// streams/examples.json
{
    "config": {
        "cache": {
            "enabled": true,
            "ttl": 300
        }
    }
}
```
