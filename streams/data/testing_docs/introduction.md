---
title: 'Testing: Introduction'
nav_title: Introduction
description: Testing package for Streams with pre-configured environments and sample data.
section: packages
package: testing
order: 10
tags: [testing, introduction]
status: ready
---

The Streams Testing package provides everything you need to test your Streams applications with confidence. Built on top of Orchestra Testbench, it offers a fully configured Laravel testing environment with sample data, automatic cleanup, and testing utilities specifically designed for Streams development.

## What's Included

- **Pre-configured Laravel Environment**: A complete Laravel application setup ready for testing
- **Sample Test Data**: Star Wars-themed sample streams for learning and testing
- **Automatic Data Management**: Automatic backup and restoration of test data between tests
- **Base TestCase**: A custom TestCase class with Streams-specific utilities
- **PHPUnit 10 Support**: Modern testing framework integration

## Quick Example

```php
<?php

namespace YourApp\Tests;

use Streams\Core\Stream\Stream;
use Streams\Core\Support\Facades\Streams;

class FilmTest extends \Streams\Testing\TestCase
{
    public function test_can_query_films()
    {
        $films = Streams::entries('films')->get();
        
        $this->assertCount(7, $films);
    }
}
```

## Who Should Use This Package?

- **Streams Developers**: Anyone building applications with the Streams platform
- **Package Authors**: Developers creating Streams addons and extensions
- **Teams**: Organizations needing reliable testing infrastructure for Streams projects
- **Learners**: Those exploring Streams capabilities in a safe testing environment

## Key Benefits

### 1. Zero Configuration
The package comes pre-configured with a Laravel application, streams definitions, and sample data. You can start writing tests immediately without setup hassle.

### 2. Clean State Testing
Every test runs with a fresh copy of the sample data. The TestCase automatically restores data after each test, ensuring no test pollution.

### 3. Real-World Examples
Sample streams include films, people, planets, and more—providing realistic data structures to learn from and test against.

### 4. Laravel Integration
Full access to Laravel's testing features, including database assertions, HTTP testing, and more.

## Getting Started

Ready to start testing? Continue to the [Installation Guide](/docs/testing/installation) to set up the Streams Testing package in your project.
