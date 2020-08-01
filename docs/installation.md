---
title: Installation
section: getting_started
intro: Installation is a strong word.
sort: 2
---

## Start a new project


You will be using composer to install streams. Unless you already have it installed on 
your machine, follow this link and do that first: [https://getcomposer.org/](https://getcomposer.org/)


### Create

```bash
composer create-project anomaly/streams example.local --prefer-dist --stability=dev
```

### Create within an existing project


```bash
composer require anomaly/streams-platform --prefer-dist --stability=dev
```

Optionally you can also install the **anomaly/streams-ui** and **anomaly/streams-api** packages that come standard in Streams.

## Updating
From within your project, use Composer to update the Streams core package:

### Composer update

```bash
composer update anomalylabs/streams-platform --with-dependencies
```

You can of course update your entire project, including Streams core using **composer update**.

## Getting Started

Now is a great time to take a quick look at [how it works](how-it-works) to getting started.
