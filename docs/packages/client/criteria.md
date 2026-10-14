---
title: Criteria Query Builder
nav_title: Criteria Query Builder
description: Fluent filters, sorting and pagination for API entry lists.
section: packages
package: client
order: 70
tags: [client, criteria]
editorial_status: ready
status: implemented
tracking_task: 265
---

`Criteria` builds the [API query parameters](/docs/api/query-parameters) used by entry lists. Each method returns the builder so calls can be chained.

## Filtering and ordering

```javascript
import { Client, Criteria } from '@laravel-streams/api-client';

const client = new Client({ baseURL: 'https://example.com/api' });
const criteria = Criteria.make()
    .where('status', 'published')
    .where('price', '>=', 10)
    .where('price', '<=', 100)
    .orderBy('price', 'asc')
    .orderBy('title', 'desc')
    .limit(10);

const response = await client.entries.get('products', { criteria });
console.log(response.data); // Entries array
```

Equality uses `where(field, value)`, or `where(field, '==', value)`. GET filters support `>`, `>=`, `<`, `<=`, `!=`, `<>`, `LIKE`, `IN`, and `BETWEEN`. Use array operands with `IN` and `BETWEEN`.

Distinct fields and distinct operators on the same field are retained. Repeating the same field/operator throws instead of silently replacing a condition. GET filters are AND conditions: `orWhere()` and nested filters cannot be standardized for this endpoint. Use the [query endpoint](/docs/api/query-endpoint) for those operations:

```javascript
const response = await client.post('/streams/posts/query', {
    data: {
        parameters: [
            { where: ['title', 'like', '%streams%'] },
            { orWhere: ['content', 'like', '%streams%'] },
            { orderBy: ['created_at', 'desc'] },
        ],
    },
});
```

## Limits and pagination

`limit(10)` caps results; `first()` sets `limit(1)`; `find(id)` adds an ID equality filter and `limit(1)`. These build criteria; the request still returns an entries array. `orderBy(field)` defaults to descending order.

```javascript
const criteria = new Criteria().where('status', 'published').paginate(25, 2);
const response = await client.entries.get('posts', { criteria });
console.log(response.meta);
console.log(response.links);
```

`paginate()` defaults to 100 entries per page and page 1.

## Inspecting parameters

`getParameters()` returns ordered `{ name, value }` records. `setParameters(records)` replaces them and `addParameter(name, value)` appends one. `standardizeParameters()` converts supported filters into the API shape:

```javascript
const params = new Criteria()
    .where('status', 'published')
    .where('price', '>=', 10)
    .where('price', '<=', 100)
    .orderBy('title', 'asc')
    .limit(10)
    .standardizeParameters();
// {
//   where: { status: 'published', price: { '>=': 10, '<=': 100 } },
//   order_by: { title: 'asc' },
//   limit: 10
// }
```

Default middleware sends `where[status]=published`, `where[price][>=]=10`, `where[price][<=]=100`, and `order_by[title]=asc`. Values are URL encoded. `compile()` retains the legacy base64 JSON representation; HTTP requests use standardized bracket parameters instead.

## Related

- [Entries](/docs/client/entries)
- [Examples](/docs/client/examples)
