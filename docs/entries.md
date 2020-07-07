---
title: Entries
intro: An entry represents a Streamed data-model object.
---

## Fetching Entries
### Criteria and Repositories
- Explain the difference (who/what/why would use these)
- When you would need one over the other


### Criteria

Entry [criteria](criteria) is a basic query builder adapter interface.

This is the lowest level of access we abstract for fetching data.

```php
$entry = Streams::entries('docs')->find('introduction');
$entry = Streams::stream('docs')->entries()->find('introduction');
$entry = Streams::stream('docs')->repository()->newCriteria()->find('introduction');
```
^ we need a bit more clarity on where the criteria is in each of these examples (either here for further down)


### Repositories

Entry [repositories](repositories) are abstracted interfaces for accessing data. They leverage the criteria underneath.

```php
$entry = Streams::repository('docs')->find('introduction');
$entry = Streams::stream('docs')->repository()->find('introduction');
```

```php
$entries = Streams::repository('docs')->findAllBy('status', true);
$entries = Streams::stream('docs')->repository()->find('introduction');
```

```php
Streams::repository('docs')->save($entry);
Streams::stream('docs')->repository()->save($entry);
```
