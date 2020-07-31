---
title: Installation
section: getting_started
intro: Installation is a strong word.
sort: 1
---
You won't need anything to kick the tires but it is highly recommend to become somewhat familiar with the [basics of Laravel](https://laravel.com/docs/routing).

### System Requirements

Please first review the [Laravel server requirements](https://laravel.com/docs/installation#server-requirements) before continueing.

Streams may require an additional requirement for [image manipulation](images):

- GD Library **OR** Imagick PHP extension

## Download

### Via Composer Create-Project

```bash
composer create-project anomaly/streams example.local --prefer-dist --stability=dev
```

### Via Composer Existing-Project

```bash
composer require anomaly/streams-platform --prefer-dist --stability=dev
```

Optionally you can also install the ` anomaly/streams-ui` and `anomaly/streams-api` packages that come standard in Streams.

## Updating
From within your project, use Composer to update the Streams core package:

```bash
composer update anomalylabs/streams-platform --with-dependencies
```

You can of course update your entire project, including Streams core using **composer update**.

## Getting Started

Now is a great time to take a quick look at [how it works](how-it-works) to getting started.
