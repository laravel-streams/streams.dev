---
title: Installation
section: getting_started
intro: Installation is a strong word.
sort: 2
---

## Download

Start using streams by first downloading it using Composer:

### Via Composer Create-Project

```bash
composer create-project anomaly/streams example.local --prefer-dist --stability=dev
```

### Via Composer Existing-Project

```bash
composer require anomaly/streams-platform --prefer-dist --stability=dev
```

#### Or you can do 
Optionally you can also install the **anomaly/streams-ui** and **anomaly/streams-api** packages that come standard in Streams.

## Updating
From within your project, use Composer to update the Streams core package:

```bash
composer update anomalylabs/streams-platform --with-dependencies
```

You can of course update your entire project, including Streams core using **composer update**.

## Getting Started

Now is a great time to take a quick look at [how it works](how-it-works) to getting started.
