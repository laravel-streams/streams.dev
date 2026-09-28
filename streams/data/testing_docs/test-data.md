---
title: Test Data
nav_title: Test Data
description: Sample streams and test data included in the package.
section: packages
package: testing
order: 30
tags: [testing, test, data]
status: ready
---

# Test Data

The Streams Testing package includes a rich set of sample data based on the Star Wars universe. This data provides realistic examples for testing and learning Streams concepts.

## Available Streams

The package includes these pre-configured streams:

### Films

**Stream**: `films`  
**Entries**: 7 Star Wars films  
**Key Field**: `episode_id`

```php
$films = Streams::entries('films')->get();
// Returns 7 film entries
```

#### Example Entry

```json
{
    "episode_id": 1,
    "title": "A New Hope",
    "director": "George Lucas",
    "producer": "Gary Kurtz, Rick McCallum",
    "release_date": "1977-05-25",
    "opening_crawl": "It is a period of civil war..."
}
```

#### Fields

- `episode_id` (integer) - Unique episode number
- `title` (string) - Film title
- `director` (string) - Director name
- `producer` (string) - Producer names
- `release_date` (datetime) - Release date
- `opening_crawl` (text) - Opening text
- `created` (datetime) - Creation timestamp
- `edited` (datetime) - Last edit timestamp

### People

**Stream**: `people`  
**Entries**: Multiple characters  
**Key Field**: `id`

```php
$characters = Streams::entries('people')
    ->where('homeworld', 'Tatooine')
    ->get();
```

#### Example Entry

```json
{
    "id": 1,
    "name": "Luke Skywalker",
    "height": "172",
    "mass": "77",
    "hair_color": "blond",
    "eye_color": "blue",
    "birth_year": "19BBY",
    "gender": "male",
    "homeworld": "Tatooine"
}
```

### Planets

**Stream**: `planets`  
**Entries**: Various planets  
**Key Field**: `id`

```php
$planets = Streams::entries('planets')
    ->where('climate', 'like', '%arid%')
    ->get();
```

#### Example Entry

```json
{
    "id": 1,
    "name": "Tatooine",
    "rotation_period": "23",
    "orbital_period": "304",
    "diameter": "10465",
    "climate": "arid",
    "gravity": "1 standard",
    "terrain": "desert",
    "population": "200000"
}
```

### Species

**Stream**: `species`  
**Entries**: Various species  
**Key Field**: `id`

```php
$species = Streams::entries('species')
    ->where('classification', 'mammal')
    ->get();
```

### Starships

**Stream**: `starships`  
**Entries**: Various starships  
**Key Field**: `id`

```php
$starships = Streams::entries('starships')
    ->where('manufacturer', 'like', '%Incom%')
    ->get();
```

### Vehicles

**Stream**: `vehicles`  
**Entries**: Various vehicles  
**Key Field**: `id`

```php
$vehicles = Streams::entries('vehicles')
    ->orderBy('cost_in_credits', 'desc')
    ->get();
```

### Files

**Stream**: `files`  
**Entries**: File handling examples  
**Key Field**: `id`

```php
$files = Streams::entries('files')->get();
```

## Using Test Data

### Querying Data

```php
public function test_query_films_by_director()
{
    $films = Streams::entries('films')
        ->where('director', 'George Lucas')
        ->get();
    
    $this->assertGreaterThan(0, $films->count());
}
```

### Accessing Relationships

```php
public function test_character_homeworld()
{
    $luke = Streams::entries('people')
        ->where('name', 'Luke Skywalker')
        ->first();
    
    $this->assertEquals('Tatooine', $luke->homeworld);
}
```

### Counting Entries

```php
public function test_all_films_present()
{
    $count = Streams::entries('films')->count();
    
    $this->assertEquals(7, $count);
}
```

### Sorting Data

```php
public function test_films_chronological_order()
{
    $films = Streams::entries('films')
        ->orderBy('release_date')
        ->get();
    
    $this->assertEquals('A New Hope', $films->first()->title);
}
```

## Data Management

### Automatic Restoration

The test data is automatically restored after each test. The TestCase handles this in the `tearDown()` method:

```php
protected function tearDown(): void
{
    $this->restoreStreamsData();
    parent::tearDown();
}
```

This ensures:
- Each test starts with clean data
- Tests don't interfere with each other
- You can modify data without permanent changes

### Manual Restoration

If needed, you can manually restore data:

```php
public function test_with_manual_restore()
{
    // Modify some data
    Streams::make('films')->create([...]);
    
    // Manually restore
    $this->restoreStreamsData();
    
    // Data is back to original state
    $this->assertCount(7, Streams::entries('films')->get());
}
```

### Data Location

Test data is stored in:
- **Active**: `vendor/streams/testing/laravel/streams/`
- **Backup**: `vendor/streams/testing/laravel/streams.bak/`

## Custom Test Data

### Adding Your Own Streams

Create custom stream definitions in your test setup:

```php
protected function setUp(): void
{
    parent::setUp();
    
    // Define a custom stream for testing
    Streams::build([
        'id' => 'custom_products',
        'name' => 'Products',
        'source' => [
            'type' => 'file',
            'format' => 'json',
        ],
        'fields' => [
            'id' => 'integer',
            'name' => 'string',
            'price' => 'decimal',
        ],
    ])->save();
}
```

### Loading Custom Fixtures

Load your own test data:

```php
public function test_with_custom_data()
{
    $products = [
        ['id' => 1, 'name' => 'Widget', 'price' => 9.99],
        ['id' => 2, 'name' => 'Gadget', 'price' => 19.99],
    ];
    
    foreach ($products as $product) {
        Streams::make('custom_products')->create($product);
    }
    
    $this->assertCount(2, Streams::entries('custom_products')->get());
}
```

## Best Practices

### 1. Don't Rely on Specific IDs

IDs may change. Use attributes instead:

```php
// Bad
$film = Streams::entries('films')->find(1);

// Good
$film = Streams::entries('films')
    ->where('title', 'A New Hope')
    ->first();
```

### 2. Use Factories for Complex Data

Create factories for reusable test data:

```php
class TestHelpers
{
    public static function createFilm($attributes = [])
    {
        return Streams::make('films')->create(array_merge([
            'title' => 'Test Film',
            'director' => 'Test Director',
            'release_date' => now(),
        ], $attributes));
    }
}
```

### 3. Test Data Integrity

Verify expected data exists:

```php
protected function setUp(): void
{
    parent::setUp();
    
    $this->assertGreaterThan(0, Streams::entries('films')->count(),
        'Test data not loaded properly');
}
```

## Next Steps

- [Writing Tests](/docs/testing/writing-tests)
- [Configuration](/docs/testing/configuration)
- [Troubleshooting](/docs/testing/troubleshooting)
