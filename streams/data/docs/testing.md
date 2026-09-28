---
title: Testing
nav_title: Testing
description: Testing Streams applications and packages.
section: guides
category: development
package: testing
order: 40
tags: [testing]
status: ready
---

## Overview

`streams/testing` provides a pre-configured Orchestra Testbench environment, sample streams, and helpers for testing Streams behavior in your app or package.

## Installation

```bash
composer require --dev streams/testing:1.0.x-dev
```

Extend the package TestCase in your PHPUnit tests and use sample stream data for realistic scenarios.

## Learn more

- [Testing introduction](/docs/testing/introduction)
- [Writing tests](/docs/testing/writing-tests)
- [Test data](/docs/testing/test-data)
- [Troubleshooting](/docs/testing/troubleshooting)

Laravel's own [testing documentation](https://laravel.com/docs/testing) applies for HTTP, database, and feature tests outside Streams specifics.
