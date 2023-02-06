---
sort_order: 11
title: API
description: 'RESTful API automation'
category: advanced
status: ideation
---

## Installation

### Downloading

This package is added to existing Laravel projects as a dependency by requiring it with Composer.

```bash
composer require streams/api:1.0.x-dev
```

The Streams API comes pre-configured with the [Streams starter application](/docs/installation) and some of the [examples](/docs/examples).

#### Cloning with Git

```bash
git clone git@github.com:laravel-streams/streams-api.git
```

### Updating

From within your project, use Composer to update this individual package:

```bash
composer update streams/api --with-dependencies
```

You can also update your entire project using `composer update`.


## Configuration

### Configuration Files

Published configuration files reside in `config/streams/`.

``` files
├── config/streams/
│   └── api.php
```

#### Publishing Configuration

Use the following command to publish configuration files.

```bash
php artisan vendor:publish --provider=Streams\\Api\\ApiServiceProvider --tag=config
```

The above command will copy configuration files from their package location to the directory mentioned above so that you can modify them directly and commit them to your version control system.

### Configuring the API

Below are the contents of the published configuration file:

```php
// config/streams/api.php

return [

    /*
     * Determine if the API should be enabled.
     *
     * This is disabled by default because
     * The API is public by default.
     */
    'enabled' => env('STREAMS_API_ENABLED', true),

    /*
     * Specify the API prefix.
     */
    'prefix' => env('STREAMS_API_PREFIX', 'api'),

    /*
     * Specify the API group middleware.
     *
     * This is designed to match out of the box
     * "app/Providers/RouteServiceProvider.php"
     * and "app/Http/Kernel.php" Laravel files.
     *
     * Changing this value will require
     * adjusting the above files.
     */
    'middleware' => env('STREAMS_API_MIDDLEWARE', 'api'),

];
```

#### API Middleware

API middleware an be configured in your application's HTTP kernel.

```php
// app/Http/Kernel.php

protected $middlewareGroups = [
    'api' => [
        'throttle:60,1',
        'bindings',
        \Streams\Api\Http\Middleware\ApiCache::class,
    ],
];
```

#### API Routes File

The `app/Providers/RouteServiceProvider.php` file typically uses the `api` middleware group when loading the `routes/api.php` file. By default this is compatible and routes defined there will be properly prefixed and grouped.



### API Cache

You may enable API level caching with the `ApiCache` middleware:

```php
// app/Http/Kernel.php

protected $middlewareGroups = [
    'api' => [
        ...
        \Streams\Api\Http\Middleware\ApiCache::class,
    ],
];
```

#### Cache Control

You may control the API cache with the following headers:

##### Disable Cache

To force fresh results use the `no-cache` directive:

```bash
Cache-Control: no-cache
```

##### Allow Cache

Use the `max-age` directive to allow cached results for a certain amount of seconds:

```bash
Cache-Control: max-age=600
```

### Application Cache

Streams may be cached by default at the application level according to their [configuration](../core/caching).
#### Configuration

Application-level streams cache can be specified in the stream configuration:

```json
//streams/examples.json
{
    "config": {
        "cache": {
            "enabled": true,
            "store": "default",
            "ttl": 3600
        }
    }
}
```

#### Cache Parameter

You may enable application-level cache using the `cache` parameter:

```json
// GET /api/streams/examples/entries
{
    "parameters": [
        {"cache": [300]}
        {"where": ["status", "active"]}
    ]
}
```


## Entries

Entries represent the data within your configured [Streams](#streams). Use the Entries API to interact with streams data.

### Get Entries

Return paginated entries from a configured stream:

`GET` `/api/streams/{stream}/entries`

##### Request

```bash
curl --location --request GET 'http://127.0.0.1:8000/api/streams/{stream}/entries'
```

##### Response

```json
{
    "errors": [],
    "links": {
        "self": "http://127.0.0.1:8000/api/streams/examples/entries",
        "streams": "http://127.0.0.1:8000/api/streams",
        "stream": "http://127.0.0.1:8000/api/streams/examples",
        "entries": "http://127.0.0.1:8000/api/streams/examples/entries",
        "first_page": "http://127.0.0.1:8000/api/streams/examples/entries?page=1",
        "next_page": null,
        "previous_page": null
    },
    "meta": {
        "parameters": {
            "stream": "examples"
        },
        "stream": "examples",
        "total": 2,
        "per_page": 100,
        "last_page": 1,
        "current_page": 1
    },
    "data": [
        {
            "id": "454eddee-5e72-3f40-ae98-99d4bc88410a",
            "user": "6bab6d9d-44c8-379f-842b-16b0ebb6fd71",
            "name": "Test Example"
        },
        {
            "id": "a2f2647d-8232-403b-b266-f333c8c3f6cb",
            "user": "7e947900-2be1-315b-89b8-c28ae715969f",
            "name": "Another Test"
        }
    ]
}
```

#### Pagination Options

Use the `per_page` and `page` options to control the amount of entries per page and specific page to return.

```bash
curl --location --request GET 'http://127.0.0.1:8000/api/streams/{stream}/entries?per_page=15&page=5'
```

#### Filtering Results

You may filter entries by providing a JSON array of [criteria](../core/querying#filtering) parameters.

```bash
curl --location --request GET '/api/streams/{stream}/entries' \
    -H 'Content-Type: application/json' \
    -d '{"parameters": [{"where": ["name", "LIKE", "%Example%"]}]}'
```

#### Sorting Results

You may specify sorting and ordering within the JSON array of [criteria](../core/querying#sorting-ordering) parameters.

```bash
curl --location --request GET '/api/streams/{stream}/entries' \
    -H 'Content-Type: application/json' \
    -d '{"parameters": [{"order_by": ["name", "asc"]}]}'
```


### Create Entry

Create a new entry for a configured stream:

`POST` `/api/streams/{stream}/entries`

##### Request

```bash
curl --location --request POST '/api/streams/{stream}/entries' \
    -H 'Content-Type: application/json' \
    -d '{"user": "7e947900-2be1-315b-89b8-c28ae715969f", "name": "Example Entry"}'
```

##### Response

```json
{
    "errors": [],
    "links": {
        "self": "http://127.0.0.1:8000/api/streams/examples/entries",
        "streams": "http://127.0.0.1:8000/api/streams",
        "stream": "http://127.0.0.1:8000/api/streams/examples",
        "entries": "http://127.0.0.1:8000/api/streams/examples/entries",
        "location": "http://127.0.0.1:8000/api/streams/examples/entries/2b91e2fd-fc16-4f5a-becb-5b74b07ac48d"
    },
    "meta": {
        "payload": {
            "user": "7e947900-2be1-315b-89b8-c28ae715969f",
            "name": "Example Entry"
        },
        "parameters": {
            "stream": "examples"
        },
        "stream": "examples"
    },
    "data": {
        "id": "2b91e2fd-fc16-4f5a-becb-5b74b07ac48d",
        "user": "7e947900-2be1-315b-89b8-c28ae715969f",
        "name": "Example Entry"
    }
}
```

### Get Entry

Return a single entry for a configured stream:

`GET` `/api/streams/{stream}/entries/{entry}`

##### Request

```bash
curl --location --request GET 'http://127.0.0.1:8000/api/streams/{stream}/entries/{entry}'
```

##### Response

```json
{
    "errors": [],
    "links": {
        "self": "http://127.0.0.1:8000/api/streams/examples/entries/2b91e2fd-fc16-4f5a-becb-5b74b07ac48d",
        "streams": "http://127.0.0.1:8000/api/streams",
        "stream": "http://127.0.0.1:8000/api/streams/examples",
        "entries": "http://127.0.0.1:8000/api/streams/examples/entries",
        "user": "http://127.0.0.1:8000/api/streams/users/entries/7e947900-2be1-315b-89b8-c28ae715969f"
    },
    "meta": {
        "parameters": {
            "stream": "examples",
            "entry": "2b91e2fd-fc16-4f5a-becb-5b74b07ac48d"
        },
        "stream": "examples"
    },
    "data": {
        "id": "2b91e2fd-fc16-4f5a-becb-5b74b07ac48d",
        "user": "7e947900-2be1-315b-89b8-c28ae715969f",
        "name": "Example Entry"
    }
}
```

### Patch Entry

Update specific values of an entry in a configured stream:

`PATCH` `/api/streams/{stream}/entries/{entry}`

If the entry does not exist the API will attempt to create one with the given values.

##### Request

```bash
curl --location --request PATCH '/api/streams/{stream}/entries/{entry}' \
    -H 'Content-Type: application/json' \
    -d '{"name": "Updated Entry"}'
```

##### Response

```json
{
    "errors": [],
    "links": {
        "self": "http://127.0.0.1:8000/api/streams/examples/entries/2b91e2fd-fc16-4f5a-becb-5b74b07ac48d",
        "streams": "http://127.0.0.1:8000/api/streams",
        "stream": "http://127.0.0.1:8000/api/streams/examples",
        "entries": "http://127.0.0.1:8000/api/streams/examples/entries"
    },
    "meta": {
        "payload": {
            "name": "Updated Entry"
        },
        "parameters": {
            "stream": "examples",
            "entry": "2b91e2fd-fc16-4f5a-becb-5b74b07ac48d"
        },
        "stream": "examples"
    },
    "data": {
        "id": "2b91e2fd-fc16-4f5a-becb-5b74b07ac48d",
        "user": "7e947900-2be1-315b-89b8-c28ae715969f",
        "name": "Updated Entry"
    }
}
```

### Update Entry

Replace values of an entry in a configured stream:

`PUT` `/api/streams/{stream}/entries/{entry}`

If the entry does not exist the API will attempt to create one with the given values.

##### Request

```bash
curl --location --request PUT '/api/streams/{stream}/entries/{entry}' \
    -H 'Content-Type: application/json' \
    -d '{"name": "Updated Entry"}'
```

##### Response

```json
{
    "errors": [],
    "links": {
        "self": "http://127.0.0.1:8000/api/streams/examples/entries/2b91e2fd-fc16-4f5a-becb-5b74b07ac48d",
        "streams": "http://127.0.0.1:8000/api/streams",
        "stream": "http://127.0.0.1:8000/api/streams/examples",
        "entries": "http://127.0.0.1:8000/api/streams/examples/entries"
    },
    "meta": {
        "payload": {
            "name": "Updated Entry"
        },
        "parameters": {
            "stream": "examples",
            "entry": "2b91e2fd-fc16-4f5a-becb-5b74b07ac48d"
        },
        "stream": "examples"
    },
    "data": {
        "id": "2b91e2fd-fc16-4f5a-becb-5b74b07ac48d",
        "user": null,
        "name": "Updated Entry"
    }
}
```

### Delete Entry

Deletes an entry from a configured stream. Successful delete requests return an empty 204 response.

`DELETE` `/api/streams/{stream}/entries/{entry}`

##### Request

```bash
curl --location --request DELETE '/api/streams/{stream}/entries' \
    -H 'Content-Type: application/json' \
    -d '{"name": "Updated Entry"}'
```

## Streams

Streams represent the data structures within your application. Use the Streams API to manage data structures.

If you want to access the data within a configured stream, use the [Entries API](entries).

### Get Stream

Return a paginated list of configured streams:

`GET` `/api/streams`

##### Request

```bash
curl --location --request GET 'http://127.0.0.1:8000/api/streams'
```

##### Response

```json
{
    "errors": [],
    "links": {
        ...
    },
    "meta": {
        "total": 2,
        "per_page": 100,
        "last_page": 1,
        "current_page": 1
    },
    "data": [
        ...
    ]
}
```

#### Pagination Options

Use the `per_page` and `page` options to control the amount of streams per page and specific page to return.

```bash
curl --location --request GET 'http://127.0.0.1:8000/api/streams?per_page=15&page=5'
```

#### Filtering Results

You may filter streams by providing a JSON array of [criteria](../core/querying#filtering) parameters.

```bash
curl --location --request GET '/api/streams' \
    -H 'Content-Type: application/json' \
    -d '{"parameters": [{"where": ["name", "LIKE", "%Docs%"]}]}'
```

#### Sorting Results

You may specify sorting and ordering within the JSON array of [criteria](../core/querying#sorting-ordering) parameters.

```bash
curl --location --request GET '/api/streams' \
    -H 'Content-Type: application/json' \
    -d '{"parameters": [{"order_by": ["name", "asc"]}]}'
```


### Create Stream

Create a new stream:

`POST` `/api/streams`

##### Request

```bash
curl --location --request POST '/api/streams/{stream}' \
    -H 'Content-Type: application/json' \
    -d '{"id": "examples", "name": "Examples"}'
```

##### Response

```json
{
    "errors": [],
    "links": {
        "self": "http://127.0.0.1:8000/api/streams",
        "streams": "http://127.0.0.1:8000/api/streams",
        "entries": "http://127.0.0.1:8000/api/streams/examples/entries",
        "location": "http://127.0.0.1:8000/api/streams/examples"
    },
    "meta": {
        "payload": {
            "id": "examples",
            "name": "Examples"
        },
        "stream": "core.streams"
    },
    "data": {
        "id": "examples",
        "name": "Examples"
    }
}
```

### Get Stream

Return a single stream:

`GET` `/api/streams/{stream}`

##### Request

```bash
curl --location --request GET 'http://127.0.0.1:8000/api/streams/{stream}'
```

##### Response

```json
{
    "errors": [],
    "links": {
        "self": "http://127.0.0.1:8000/api/streams/examples",
        "streams": "http://127.0.0.1:8000/api/streams",
        "stream": "http://127.0.0.1:8000/api/streams/examples",
        "entries": "http://127.0.0.1:8000/api/streams/examples/entries"
    },
    "meta": {
        "parameters": {
            "stream": "examples"
        },
        "stream": "core.streams"
    },
    "data": {
        "id": "examples",
        "name": "Examples"
    }
}
```

### Patch Stream

Update specific values of a stream:

`PATCH` `/api/streams/{stream}`

If the stream does not exist the API will attempt to create one with the given values.

##### Request

```bash
curl --location --request PATCH '/api/streams/{stream}' \
    -H 'Content-Type: application/json' \
    -d '{"description": "Example stream data."}'
```

##### Response

```json
{
    "errors": [],
    "links": {
        "self": "http://127.0.0.1:8000/api/streams/examples",
        "streams": "http://127.0.0.1:8000/api/streams",
        "stream": "http://127.0.0.1:8000/api/streams/examples",
        "entries": "http://127.0.0.1:8000/api/streams/examples/entries"
    },
    "meta": {
        "payload": {
            "description": "Example stream data."
        },
        "parameters": {
            "stream": "examples"
        },
        "stream": "core.streams"
    },
    "data": {
        "id": "examples",
        "name": "Updated Name",
        "description": "Example stream data."
    }
}
```

### Update Stream

Replace values of a streams:

`PUT` `/api/streams/{stream}`

If the stream does not exist the API will attempt to create one with the given values.

##### Request

```bash
curl --location --request PUT '/api/streams/{stream}' \
    -H 'Content-Type: application/json' \
    -d '{"description": "Example stream data."}'
```

##### Response

```json
{
    "errors": [],
    "links": {
        "self": "http://127.0.0.1:8000/api/streams/examples",
        "streams": "http://127.0.0.1:8000/api/streams",
        "stream": "http://127.0.0.1:8000/api/streams/examples",
        "entries": "http://127.0.0.1:8000/api/streams/examples/entries"
    },
    "meta": {
        "payload": {
            "description": "Example stream data."
        },
        "parameters": {
            "stream": "examples"
        },
        "stream": "core.streams"
    },
    "data": {
        "description": "Example stream data.",
        "id": "examples"
    }
}
```

### Delete Stream

Deletes a stream. Successful delete requests return an empty 204 response.

`DELETE` `/api/streams/{stream}`

##### Request

```bash
curl --location --request DELETE '/api/streams/{stream}' \
    -H 'Content-Type: application/json' \
    -d '{"name": "Updated Entry"}'
```
