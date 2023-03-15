---
sort: 1
title: Installation
category: getting_started
stage: review
enabled: true
---

## Introduction

Laravel Streams is a modular content management *framework* for **Laravel developers**. It allows developers to easily create and manage custom database-agnostic content/data types, fields, and relationships and maximizes your applications integration and use of them.

### Why Streams?

Streams comes with only what you want and makes it incredibly fast and easy to build up custom data structures for streamlined integration into your website or application.

The CMF is designed to be flexible, modular, performant, and extensible, allowing developers to create powerful and customized websites and applications that meet their specific needs while quickly prototyping ideas with built-in design patterns and flat-file modeling.

### Before You Get Started

Streams is built first and intended for Laravel developers. This means that building something with it requires that you have somewhat familiarity with the Laravel framework.

The optional UI is consistent, user friendly, and accessible, but again, requires some coding knowledge to initially configure.

## Server Requirements
    
To run Laravel Streams you will need to meet [Laravel server requirements](https://laravel.com/docs/deployment#server-requirements).

### Supported Image Libraries

Please ensure one of the following libraries is installed in order to support [image manipulation](/docs/images).

- GD Library
- Imagick PHP extension


## New Projects

For new projects, the fastest way to get up and running is the [Streams starter project](/packages/streams/streams):

```bash
composer create-project streams/streams:1.0.x-dev

cd streams

php artisan serve
```

### Included Packages

The following packages are installed with the starter project:

- [streams/core](/packages/streams/core)
- [streams/api](/packages/streams/api)
- [streams/ui](/packages/streams/ui)
- [streams/sdk](/packages/streams/sdk)

<!-- ### Dev Packages

The following development tools are also included:

- [Streams Testing](/docs/testing) -->

### Getting Started

Your streams project is now ready for you to start coding.

- [Configuration](configuration)
- [Contributing](contributing)


### The Basics

Explore the basics of Streams by example.

- [Streams](streams)
- [Fields](fields)

#### Fundamental Concepts

Dig deeper into the fundamental concepts of Laravel Streams. 

- [Data Modeling](streams)
- [Laravel Development](core)
- [Frontend Development](frontend)
- [User Interface](ui)
- [API Readiness](api)

## Existing Laravel Projects

You can add the Streams features you need to existing Laravel projects by requiring the packages you need. The **core** package is responsible for the fundamental features; it is the only **required** package.

```bash
composer require streams/core
composer require streams/api
composer require streams/ui
composer require streams/sdk
```
