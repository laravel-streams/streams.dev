---
title: Decimal Type
link_title: Decimal
intro: Store fixed-precision decimal values.
category: field_types
stage: drafting
enabled: true
---

## Overview

The `decimal` field type stores fixed-precision decimal values. If you need to support floating point numbers, check out the [number](number) field type.

```json
// streams/example.json
"fields": {
    "price": {
        "type": "decimal",
        "config": {
            "decimals": 2
        }
    }
}
```

### Configuration

@todo Generate config options from class::configuration


## Data Structure

```json
{
    "price": 89.95
}
```

Basic value access displays the stored value:

```blade
@verbatim// Basic access
{{ $entry->price }}@endverbatim
```

### Decorator Usage

Boolean types also provide decorated values.

```blade
@verbatim// Decorated value
@if ($entry->price()->isOdd())
    // ...
@endif@endverbatim
```

#### Methods

@todo Generate methods from @docs
