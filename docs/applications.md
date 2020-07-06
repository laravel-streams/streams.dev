---
title: Applications
intro: Applicatons act as the main switchboard for incoming requests.
---
### Configuration

Your application will run as `default`.. by default.

Furthermore, there _must_ be a default application though it doesn't have to use the default `handle`.

```php
'applications' => [
    'default' => [],
],
```
