---
title: Entries
category: reference
sort: 0
enabled: true
---

## Introduction

Entries represent the data within your configured [Streams](#streams). Use the Entries API to interact with streams data.

## Get Entries

Return paginated entries from a configured stream:

`GET` `/api/streams/{stream}/entries`

#### Request

```bash
curl --location --request GET 'http://127.0.0.1:8000/api/streams/{stream}/entries'
```

#### Response

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

### Pagination Options

Use the `per_page` and `page` options to control the amount of entries per page and specific page to return.

```bash
curl --location --request GET 'http://127.0.0.1:8000/api/streams/{stream}/entries?per_page=15&page=5'
```

### Filtering Results

You may filter entries by providing field constraint options.

```bash
curl --location --request GET '/api/streams/{stream}/entries?where[field]=value&constraint[fiel]=operator'
```

### Sorting Results

You may specify sorting and ordering within the JSON array of [criteria](../core/querying#sorting-ordering) parameters.

```bash
curl --location --request GET '/api/streams/{stream}/entries?orderBy[field]=asc|desc'
```


## Create Entry

Create a new entry for a configured stream:

`POST` `/api/streams/{stream}/entries`

#### Request

```bash
curl --location --request POST '/api/streams/{stream}/entries' \
    -H 'Content-Type: application/json' \
    -d '{"user": "7e947900-2be1-315b-89b8-c28ae715969f", "name": "Example Entry"}'
```

#### Response

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

## Get Entry

Return a single entry for a configured stream:

`GET` `/api/streams/{stream}/entries/{entry}`

#### Request

```bash
curl --location --request GET 'http://127.0.0.1:8000/api/streams/{stream}/entries/{entry}'
```

#### Response

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

## Patch Entry

Update specific values of an entry in a configured stream:

`PATCH` `/api/streams/{stream}/entries/{entry}`

If the entry does not exist the API will attempt to create one with the given values.

#### Request

```bash
curl --location --request PATCH '/api/streams/{stream}/entries/{entry}' \
    -H 'Content-Type: application/json' \
    -d '{"name": "Updated Entry"}'
```

#### Response

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

## Update Entry

Replace values of an entry in a configured stream:

`PUT` `/api/streams/{stream}/entries/{entry}`

If the entry does not exist the API will attempt to create one with the given values.

#### Request

```bash
curl --location --request PUT '/api/streams/{stream}/entries/{entry}' \
    -H 'Content-Type: application/json' \
    -d '{"name": "Updated Entry"}'
```

#### Response

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

## Delete Entry

Deletes an entry from a configured stream. Successful delete requests return an empty 204 response.

`DELETE` `/api/streams/{stream}/entries/{entry}`

#### Request

```bash
curl --location --request DELETE '/api/streams/{stream}/entries' \
    -H 'Content-Type: application/json' \
    -d '{"name": "Updated Entry"}'
```
