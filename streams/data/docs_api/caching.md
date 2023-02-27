---
title: Caching
category: basics
intro:
sort: 31
stage: drafting
enabled: true
---

## API Cache

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

### Cache Control

You may control the API cache with the following headers:

#### Disable Cache

To force fresh results use the `no-cache` directive:

```bash
Cache-Control: no-cache
```

#### Allow Cache

Use the `max-age` directive to allow cached results for a certain amount of seconds:

```bash
Cache-Control: max-age=600
```

## Application Cache

Streams may be cached by default at the application level according to their [configuration](../core/caching).
### Configuration

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

### Cache Parameter

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
