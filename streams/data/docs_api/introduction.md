---
title: Streams API
link_title: Introduction
intro: A robust API for interacting with streams and entry data.
enabled: true
---

## Introduction

The Streams API provides a bolt-on, fully configurable, highly performant, and extensible RESTful API. The API provides access and management control to all configured stream information and a streamlined interface to define custom endpoints of your own.

### [Installation](installation)

```bash
composer require streams/api:1.0.x-dev
```

### [Configuration](configuration)

```bash
php artisan vendor:publish --provider=Streams\\Api\\ApiServiceProvider --tag=config
```
