---
title: Entries
category: reference
sort: 0
enabled: true
---

## Introduction

Entries represent the data within your configured [Streams](#streams). Use the Entries SDK to interact with streams data.

## List Entries

Display paginated entries from  a configured stream:

```bash
entries:list
    {stream : The stream to list entries from.}
    {--query= : Query constraints.}
    {--show= : Comma-seperated fields to display.}
    {--per-page=15 : Entries per page.}
    {--page= : Page to list.}
```

#### Response

```bash
php artisan entries:list docs --show=id,title --per-page=5

Total: 15
Per Page: 5
Last Page: 3
Current Page: 1
+---------------+---------------------+
| id            | title               |
+---------------+---------------------+
| api           | API Readiness       |
| configuration | Configuration       |
| contributing  | Contributing        |
| core          | Laravel Development |
| debugging     | Debugging           |
+---------------+---------------------+

 Next Page? (yes/no) [yes]:
 > 
```

### Filtering Results

You may filter entries by providing a query string of [criteria](../core/querying#filtering) parameters.

```bash
php artisan entries:list docs --show=id,title --query=where:category,core_concepts

Total: 5
Per Page: 15
Last Page: 1
Current Page: 1
+----------+----------------------+
| id       | title                |
+----------+----------------------+
| api      | API Readiness        |
| core     | Laravel Development  |
| frontend | Frontend Development |
| streams  | Data Modeling        |
| ui       | User Interface       |
+----------+----------------------+
```

<!-- ### Sorting Results

You may specify sorting and ordering within the JSON array of [criteria](../core/querying#sorting-ordering) parameters.

```bash
curl --location --request GET '/api/streams/{stream}/entries' \
    -H 'Content-Type: application/json' \
    -d '{"parameters": [{"order_by": ["name", "asc"]}]}'
``` -->


## Create Entry

Create a new entry for a configured stream:

```bash
entries:create
    {stream : The entry stream.} 
    {input? : Query string formatted attributes.}
    {--update : Update if exists.}
```

#### Response

```bash
JSON OUTPUT
```

## Show Entry

Display a single entry for a configured stream:

```bash
entries:show
    {stream : The entry stream.}
    {entry : The entry identifier.}
    {--show= : Comma-seperated fields to show.}c
```


#### Response

```bash
php artisan entries:show docs api

+------------+---------------+
| Field      | Value         |
+------------+---------------+
| id         | api           |
| sort       | 20            |
| stage      | review        |
| title      | API Readiness |
| category   | core_concepts |
| enabled    | 1             |
| publish_at |               |
| body       |               |
+------------+---------------+
```
