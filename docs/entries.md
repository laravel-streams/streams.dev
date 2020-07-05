---
title: Entries
intro: An entry represents a Streamed data-model object.
---

## Fetching Entries

### Criteria

Entry [criteria](criteria) is a basic query builder adapter interface.

This is the lowest level of access we abstract for fetching data.

```php
$entry = Streams::entries('docs')->find('introduction');
$entry = Streams::stream('docs')->entries()->find('introduction');
$entry = Streams::stream('docs')->repository()->newCriteria()->find('introduction');
```


### Repositories

Entry [repositories](repositories) are abstracted interfaces for accessing data. They leverage the criteria underneath.

```php
$entry = Streams::repository('docs')->find('introduction');
$entry = Streams::stream('docs')->repository()->find('introduction');
```

```php
$entry = Streams::repository('docs')->save($entry);
$entry = Streams::stream('docs')->repository()->save($entry);
```
