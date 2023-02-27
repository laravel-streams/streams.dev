---
title: Streams
category: reference
sort: 0
enabled: true
---

## Introduction

Streams represent the data structures within your application. Use the Streams API to manage data structures.

If you want to access the data within a configured stream, use the [Entries API](entries).

## Get Stream

Return a paginated list of configured streams:

`GET` `/api/streams`

#### Request

```bash
curl --location --request GET 'http://127.0.0.1:8000/api/streams'
```

#### Response

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

### Pagination Options

Use the `per_page` and `page` options to control the amount of streams per page and specific page to return.

```bash
curl --location --request GET 'http://127.0.0.1:8000/api/streams?per_page=15&page=5'
```

### Filtering Results

You may filter streams by providing a JSON array of [criteria](../core/querying#filtering) parameters.

```bash
curl --location --request GET '/api/streams' \
    -H 'Content-Type: application/json' \
    -d '{"parameters": [{"where": ["name", "LIKE", "%Docs%"]}]}'
```

### Sorting Results

You may specify sorting and ordering within the JSON array of [criteria](../core/querying#sorting-ordering) parameters.

```bash
curl --location --request GET '/api/streams' \
    -H 'Content-Type: application/json' \
    -d '{"parameters": [{"order_by": ["name", "asc"]}]}'
```


## Create Stream

Create a new stream:

`POST` `/api/streams`

#### Request

```bash
curl --location --request POST '/api/streams/{stream}' \
    -H 'Content-Type: application/json' \
    -d '{"id": "examples", "name": "Examples"}'
```

#### Response

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
        "stream": "streams"
    },
    "data": {
        "id": "examples",
        "name": "Examples"
    }
}
```

## Get Stream

Return a single stream:

`GET` `/api/streams/{stream}`

#### Request

```bash
curl --location --request GET 'http://127.0.0.1:8000/api/streams/{stream}'
```

#### Response

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
        "stream": "streams"
    },
    "data": {
        "id": "examples",
        "name": "Examples"
    }
}
```

## Patch Stream

Update specific values of a stream:

`PATCH` `/api/streams/{stream}`

If the stream does not exist the API will attempt to create one with the given values.

#### Request

```bash
curl --location --request PATCH '/api/streams/{stream}' \
    -H 'Content-Type: application/json' \
    -d '{"description": "Example stream data."}'
```

#### Response

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
        "stream": "streams"
    },
    "data": {
        "id": "examples",
        "name": "Updated Name",
        "description": "Example stream data."
    }
}
```

## Update Stream

Replace values of a streams:

`PUT` `/api/streams/{stream}`

If the stream does not exist the API will attempt to create one with the given values.

#### Request

```bash
curl --location --request PUT '/api/streams/{stream}' \
    -H 'Content-Type: application/json' \
    -d '{"description": "Example stream data."}'
```

#### Response

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
        "stream": "streams"
    },
    "data": {
        "description": "Example stream data.",
        "id": "examples"
    }
}
```

## Delete Stream

Deletes a stream. Successful delete requests return an empty 204 response.

`DELETE` `/api/streams/{stream}`

#### Request

```bash
curl --location --request DELETE '/api/streams/{stream}' \
    -H 'Content-Type: application/json' \
    -d '{"name": "Updated Entry"}'
```
