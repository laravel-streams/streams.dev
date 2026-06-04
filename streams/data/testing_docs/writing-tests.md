---
sort_order: 3
title: Writing Tests
description: 'Write effective tests for Streams applications.'
status: ready
---

# Writing Tests

This guide covers everything you need to know about writing tests for Streams applications, from basic examples to advanced testing patterns.

## Basic Test Structure

### Your First Test

Every test extends the `Streams\Testing\TestCase` class:

```php
<?php

namespace Tests;

use Streams\Core\Support\Facades\Streams;

class MyFirstTest extends \Streams\Testing\TestCase
{
    public function test_example()
    {
        $this->assertTrue(true);
    }
}
```

### Anatomy of a Test

```php
public function test_descriptive_name()  // 1. Test method
{
    // 2. Arrange - Set up test data
    $stream = Streams::make('films');
    
    // 3. Act - Perform the action
    $result = $stream->entries()->count();
    
    // 4. Assert - Verify the result
    $this->assertEquals(7, $result);
}
```

## Testing Streams

### Creating Entries

```php
public function test_creates_new_film()
{
    $film = Streams::make('films')->create([
        'title' => 'New Film',
        'director' => 'New Director',
        'release_date' => '2025-01-01',
    ]);
    
    $this->assertNotNull($film->id);
    $this->assertEquals('New Film', $film->title);
}
```

### Reading Entries

```php
public function test_finds_film_by_title()
{
    $film = Streams::entries('films')
        ->where('title', 'A New Hope')
        ->first();
    
    $this->assertNotNull($film);
    $this->assertEquals('George Lucas', $film->director);
}
```

### Updating Entries

```php
public function test_updates_film_director()
{
    $film = Streams::entries('films')
        ->where('title', 'A New Hope')
        ->first();
    
    $film->update(['director' => 'Updated Director']);
    
    $updated = Streams::entries('films')
        ->where('title', 'A New Hope')
        ->first();
    
    $this->assertEquals('Updated Director', $updated->director);
}
```

### Deleting Entries

```php
public function test_deletes_film()
{
    $initialCount = Streams::entries('films')->count();
    
    $film = Streams::entries('films')->first();
    $film->delete();
    
    $finalCount = Streams::entries('films')->count();
    
    $this->assertEquals($initialCount - 1, $finalCount);
}
```

## Testing Queries

### Simple Where Clauses

```php
public function test_filters_by_director()
{
    $films = Streams::entries('films')
        ->where('director', 'George Lucas')
        ->get();
    
    $this->assertGreaterThan(0, $films->count());
    
    foreach ($films as $film) {
        $this->assertEquals('George Lucas', $film->director);
    }
}
```

### Complex Queries

```php
public function test_complex_film_query()
{
    $films = Streams::entries('films')
        ->where('director', 'George Lucas')
        ->where('release_date', '>=', '1977-01-01')
        ->orderBy('release_date')
        ->limit(3)
        ->get();
    
    $this->assertLessThanOrEqual(3, $films->count());
}
```

### Using Like Operator

```php
public function test_searches_film_titles()
{
    $films = Streams::entries('films')
        ->where('title', 'like', '%Empire%')
        ->get();
    
    $this->assertGreaterThan(0, $films->count());
}
```

### Counting Results

```php
public function test_counts_films_by_director()
{
    $count = Streams::entries('films')
        ->where('director', 'George Lucas')
        ->count();
    
    $this->assertGreaterThan(0, $count);
}
```

## Testing Stream Definitions

### Validating Stream Configuration

```php
public function test_films_stream_configuration()
{
    $stream = Streams::make('films');
    
    $this->assertEquals('Films', $stream->name);
    $this->assertEquals('episode_id', $stream->config['key_name']);
}
```

### Testing Field Definitions

```php
public function test_films_has_required_fields()
{
    $stream = Streams::make('films');
    $fields = $stream->fields;
    
    $this->assertArrayHasKey('title', $fields);
    $this->assertArrayHasKey('director', $fields);
    $this->assertArrayHasKey('release_date', $fields);
}
```

### Validating Field Types

```php
public function test_field_types()
{
    $stream = Streams::make('films');
    
    $this->assertEquals('integer', $stream->fields['episode_id']->type);
    $this->assertEquals('string', $stream->fields['title']->type);
    $this->assertEquals('datetime', $stream->fields['release_date']->type);
}
```

## Testing Relationships

### One-to-Many Relationships

```php
public function test_planet_has_residents()
{
    $tatooine = Streams::entries('planets')
        ->where('name', 'Tatooine')
        ->first();
    
    $residents = Streams::entries('people')
        ->where('homeworld', $tatooine->name)
        ->get();
    
    $this->assertGreaterThan(0, $residents->count());
}
```

### Many-to-Many Relationships

```php
public function test_character_appears_in_films()
{
    $luke = Streams::entries('people')
        ->where('name', 'Luke Skywalker')
        ->first();
    
    // Assuming a films relationship
    if (isset($luke->films)) {
        $this->assertGreaterThan(0, count($luke->films));
    }
}
```

## Using Setup and Teardown

### Setup Method

Run code before each test:

```php
protected function setUp(): void
{
    parent::setUp();
    
    // Create test data that all tests need
    $this->testFilm = Streams::make('films')->create([
        'title' => 'Test Film',
        'director' => 'Test Director',
    ]);
}
```

### Teardown Method

The parent TestCase handles data restoration automatically. Add custom cleanup if needed:

```php
protected function tearDown(): void
{
    // Custom cleanup here
    
    parent::tearDown(); // This restores test data
}
```

## Data Providers

Test the same logic with different data:

```php
/**
 * @dataProvider filmDirectorProvider
 */
public function test_finds_films_by_director($director, $expectedMinimum)
{
    $films = Streams::entries('films')
        ->where('director', $director)
        ->get();
    
    $this->assertGreaterThanOrEqual($expectedMinimum, $films->count());
}

public static function filmDirectorProvider()
{
    return [
        'George Lucas' => ['George Lucas', 1],
        'Irvin Kershner' => ['Irvin Kershner', 1],
        'Richard Marquand' => ['Richard Marquand', 1],
    ];
}
```

## Testing Exceptions

### Expecting Exceptions

```php
public function test_throws_exception_for_invalid_stream()
{
    $this->expectException(\Exception::class);
    
    Streams::make('nonexistent_stream');
}
```

### Testing Validation Errors

```php
public function test_validates_required_fields()
{
    $this->expectException(\Illuminate\Validation\ValidationException::class);
    
    Streams::make('films')->create([
        // Missing required 'title' field
        'director' => 'Test Director',
    ]);
}
```

## Common Assertions

### Equality Assertions

```php
$this->assertEquals($expected, $actual);
$this->assertNotEquals($unexpected, $actual);
$this->assertSame($expected, $actual); // Strict comparison
```

### Boolean Assertions

```php
$this->assertTrue($condition);
$this->assertFalse($condition);
$this->assertNull($value);
$this->assertNotNull($value);
```

### Collection Assertions

```php
$this->assertCount($expectedCount, $collection);
$this->assertEmpty($collection);
$this->assertNotEmpty($collection);
$this->assertContains($needle, $collection);
```

### Array Assertions

```php
$this->assertArrayHasKey($key, $array);
$this->assertArrayNotHasKey($key, $array);
$this->assertIsArray($value);
```

### String Assertions

```php
$this->assertStringContainsString($needle, $haystack);
$this->assertStringStartsWith($prefix, $string);
$this->assertStringEndsWith($suffix, $string);
$this->assertMatchesRegularExpression($pattern, $string);
```

### Numeric Assertions

```php
$this->assertGreaterThan($expected, $actual);
$this->assertGreaterThanOrEqual($expected, $actual);
$this->assertLessThan($expected, $actual);
$this->assertLessThanOrEqual($expected, $actual);
```

## Testing Best Practices

### 1. One Assertion Per Test (When Possible)

```php
// Good
public function test_film_has_title()
{
    $film = Streams::entries('films')->first();
    $this->assertNotEmpty($film->title);
}

public function test_film_has_director()
{
    $film = Streams::entries('films')->first();
    $this->assertNotEmpty($film->director);
}
```

### 2. Use Descriptive Test Names

```php
// Bad
public function test_film() { }

// Good
public function test_creates_film_with_all_required_fields() { }
```

### 3. Follow the AAA Pattern

```php
public function test_updates_film_release_date()
{
    // Arrange
    $film = Streams::entries('films')->first();
    $newDate = '2025-12-25';
    
    // Act
    $film->update(['release_date' => $newDate]);
    
    // Assert
    $this->assertEquals($newDate, $film->fresh()->release_date);
}
```

### 4. Test Edge Cases

```php
public function test_handles_empty_query_results()
{
    $films = Streams::entries('films')
        ->where('director', 'Nonexistent Director')
        ->get();
    
    $this->assertCount(0, $films);
}
```

### 5. Keep Tests Independent

Each test should work in isolation:

```php
public function test_first_test()
{
    $film = Streams::make('films')->create(['title' => 'Test']);
    $this->assertNotNull($film);
}

public function test_second_test()
{
    // Don't rely on data from test_first_test
    $count = Streams::entries('films')->count();
    $this->assertEquals(7, $count); // Original test data
}
```

## Debugging Tests

### Using dump() and dd()

```php
public function test_debugging_example()
{
    $films = Streams::entries('films')->get();
    
    dump($films); // Output without stopping
    
    // dd($films); // Dump and die
    
    $this->assertNotEmpty($films);
}
```

### Verbose Output

Run tests with verbose output:

```bash
vendor/bin/phpunit --testdox --verbose
```

### Running Single Tests

Test specific methods:

```bash
vendor/bin/phpunit --filter test_specific_method
```

## Next Steps

- [Assertions](assertions.md) - Comprehensive assertion reference
- [Advanced Testing](advanced-testing.md) - Complex testing scenarios
- [Configuration](configuration.md) - Customize your test environment
