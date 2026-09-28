---
title: Examples
description: 'curl recipes with correct paths and response shapes.'
sort_order: 19
status: ready
---

Complete curl examples assuming default prefix `api` and local server at `http://127.0.0.1:8000`.

## List entries

```bash
curl -s "http://127.0.0.1:8000/api/streams/films/entries"
```

## Filter and paginate

```bash
curl -s "http://127.0.0.1:8000/api/streams/films/entries\
?where[director]=Lucas&per_page=10&page=1"
```

## Create entry

```bash
curl -s -X POST "http://127.0.0.1:8000/api/streams/films/entries" \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{"title":"A New Hope","director":"George Lucas"}'
```

## Update entry

```bash
curl -s -X PATCH "http://127.0.0.1:8000/api/streams/films/entries/1" \
  -H "Content-Type: application/json" \
  -d '{"title":"Star Wars: A New Hope"}'
```

## Delete entry

```bash
curl -s -o /dev/null -w "%{http_code}" \
  -X DELETE "http://127.0.0.1:8000/api/streams/films/entries/1"
# 204
```

## Query endpoint

```bash
curl -s -X POST "http://127.0.0.1:8000/api/streams/films/query" \
  -H "Content-Type: application/json" \
  -d '{"parameters":[{"where":["director","George Lucas"]}]}'
```

## Related

- [Entry endpoints](/docs/api/entry-endpoints)
- [Request format](/docs/api/requests)
- [Responses](/docs/api/responses)
