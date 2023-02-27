---
title: Integer Type
link_title: Integer
intro: Store integer values.
category: field_types
stage: drafting
enabled: true
---

## Overview

The `integer` field type stores whole number values.

```json
// streams/example.json
"fields": {
    "count": {
        "type": "integer"
    }
}
```


## Data Structure

```json
{
    "count": 100
}
```

Basic value access displays the stored value:

```blade
@verbatim// Basic access
{{ $entry->count }}@endverbatim
```

### Decorator Usage

Boolean types also provide decorated values.

```blade
@verbatim// Decorated value
@if ($entry->count()->isEven())
    // ...
@endif@endverbatim
```

#### Methods

@todo Generate methods from @docs

## Configuration

@todo Generate config options from class::configuration

```json
// streams/example.json
"fields": {
    "count": {
        "type": "integer",
        "config": {
            "default": "increment"
        }
    }
}
```
