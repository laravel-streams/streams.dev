---
title: Repositories
description: 'Working with stream repositories for data access.'
sort_order: 3
category: core-concepts
status: live
---

# Repositories

Repositories in Streams Core provide a clean, consistent interface for accessing and manipulating stream data. They abstract the underlying data source and provide a rich querying API.

## Getting Repository Instances

### Basic Access

```php
// Get repository by stream handle
$users = Streams::repository('users');

// Alternative syntax
$users = Streams::entries('users');

// Direct instantiation
$users = app('streams')->repository('users');
```

### Repository Factory

```php
// Using the repository factory
$users = Repository::make('users');

// With custom configuration
$users = Repository::make('users', [
    'cache' => ['ttl' => 3600]
]);
```

## CRUD Operations

### Creating Entries

#### Single Entry

```php
$user = $users->create([
    'name' => 'John Doe',
    'email' => 'john@example.com',
    'active' => true
]);
```

#### Multiple Entries

```php
$users->createMany([
    ['name' => 'Jane Smith', 'email' => 'jane@example.com'],
    ['name' => 'Bob Wilson', 'email' => 'bob@example.com']
]);
```

#### With Validation

```php
try {
    $user = $users->create($data);
} catch (ValidationException $e) {
    // Handle validation errors
    $errors = $e->errors();
}
```

### Reading Entries

#### Find by ID

```php
// Find single entry
$user = $users->find('user-123');

// Find or fail
$user = $users->findOrFail('user-123');

// Find with default
$user = $users->find('user-123') ?: $users->newInstance();
```

#### Get All Entries

```php
// Get all entries
$allUsers = $users->all();

// Get collection
$userCollection = $users->get();

// Get first entry
$firstUser = $users->first();
```

### Updating Entries

#### Update Single Entry

```php
// Update by ID
$users->update('user-123', ['name' => 'Updated Name']);

// Update entry instance
$user = $users->find('user-123');
$user->update(['email' => 'new@example.com']);
```

#### Bulk Updates

```php
// Update multiple entries
$users->where('active', false)
      ->update(['status' => 'inactive']);
```

### Deleting Entries

#### Delete Single Entry

```php
// Delete by ID
$users->delete('user-123');

// Delete entry instance
$user = $users->find('user-123');
$user->delete();
```

#### Bulk Deletes

```php
// Delete multiple entries
$users->where('last_login', '<', now()->subYear())
      ->delete();
```

## Querying with Criteria

### Basic Where Clauses

```php
// Simple where
$activeUsers = $users->where('active', true)->get();

// Multiple conditions
$results = $users
    ->where('active', true)
    ->where('role', 'admin')
    ->get();

// Where with operator
$recentUsers = $users->where('created_at', '>', now()->subDays(30))->get();
```

### Advanced Where Clauses

```php
// Where in
$specificUsers = $users->whereIn('id', ['user-1', 'user-2', 'user-3'])->get();

// Where not
$nonAdmins = $users->whereNot('role', 'admin')->get();

// Where null
$incompleteProfiles = $users->whereNull('bio')->get();

// Where between
$ageRange = $users->whereBetween('age', [18, 65])->get();
```

### Or Conditions

```php
$users->where('role', 'admin')
      ->orWhere('role', 'moderator')
      ->get();

// Complex or conditions
$users->where(function ($query) {
    $query->where('active', true)
          ->orWhere('role', 'admin');
})->get();
```

### Nested Conditions

```php
$users->where('active', true)
      ->where(function ($query) {
          $query->where('role', 'admin')
                ->orWhere('permissions', 'like', '%manage%');
      })
      ->get();
```

## Ordering and Limiting

### Ordering Results

```php
// Order by single field
$users->orderBy('name')->get();

// Order by multiple fields
$users->orderBy('last_name')
      ->orderBy('first_name')
      ->get();

// Order by direction
$users->orderBy('created_at', 'desc')->get();
```

### Limiting Results

```php
// Limit number of results
$users->limit(10)->get();

// Skip and take (pagination)
$users->skip(20)->take(10)->get();

// Latest and oldest
$latestUsers = $users->latest()->get();
$oldestUsers = $users->oldest()->get();
```

## Aggregation

### Counting

```php
// Count all entries
$totalUsers = $users->count();

// Count with conditions
$activeUserCount = $users->where('active', true)->count();
```

### Other Aggregates

```php
// Sum
$totalCredits = $users->sum('credits');

// Average
$averageAge = $users->avg('age');

// Min/Max
$youngestAge = $users->min('age');
$oldestAge = $users->max('age');
```

## Relationships

### Loading Relationships

```php
// Eager load relationships
$posts = Streams::entries('posts')
    ->with('author')
    ->with('categories')
    ->get();

// Nested relationships
$posts = Streams::entries('posts')
    ->with('author.profile')
    ->get();
```

### Querying Relationships

```php
// Has relationship
$postsWithAuthor = Streams::entries('posts')
    ->has('author')
    ->get();

// Where has
$postsWithActiveAuthor = Streams::entries('posts')
    ->whereHas('author', function ($query) {
        $query->where('active', true);
    })
    ->get();
```

## Caching

### Repository-Level Caching

```php
// Enable caching for queries
$users = $users->cache(3600)->get();

// Cache with tags
$users = $users->cache(3600, ['users', 'active'])->get();

// Remember cache
$users = $users->remember(3600)->get();
```

### Cache Management

```php
// Clear repository cache
$users->clearCache();

// Refresh cache
$users = $users->fresh()->get();
```

## Custom Repository Methods

### Extending Repositories

```php
// app/Repositories/UserRepository.php
namespace App\Repositories;

use Streams\Core\Repository\Repository;

class UserRepository extends Repository
{
    public function active()
    {
        return $this->where('active', true);
    }
    
    public function admins()
    {
        return $this->where('role', 'admin');
    }
    
    public function recentlyActive($days = 30)
    {
        return $this->where('last_login', '>', now()->subDays($days));
    }
}
```

Configure the custom repository in your stream:

```json
{
    "config": {
        "repository": "App\\Repositories\\UserRepository"
    }
}
```

### Repository Macros

```php
// In a service provider
Repository::macro('published', function () {
    return $this->where('published', true);
});

// Usage
$publishedPosts = Streams::entries('posts')->published()->get();
```

## Events and Hooks

### Repository Events

Listen to repository events:

```php
// Before querying
Event::listen('repository.querying:users', function ($repository, $criteria) {
    // Log query for debugging
    Log::info('Querying users', $criteria->toArray());
});

// After results fetched
Event::listen('repository.fetched:users', function ($repository, $results) {
    // Process results
});
```

### Entry Lifecycle Events

```php
// Entry events
Event::listen('entry.creating:users', function ($entry) {
    $entry->created_by = auth()->id();
});

Event::listen('entry.updating:users', function ($entry) {
    $entry->updated_by = auth()->id();
});
```

## Performance Optimization

### Query Optimization

```php
// Select only needed fields
$users->select(['id', 'name', 'email'])->get();

// Eager load to avoid N+1 queries
$posts->with(['author', 'categories'])->get();

// Use indexes for common queries
$users->where('email', $email)->first(); // Ensure email is indexed
```

### Caching Strategies

```php
// Cache expensive queries
$popularPosts = Streams::entries('posts')
    ->where('views', '>', 1000)
    ->orderBy('views', 'desc')
    ->cache(3600)
    ->get();

// Use cache tags for selective clearing
$users->cache(3600, ['users'])->get();
```

### Chunking Large Datasets

```php
// Process large datasets in chunks
$users->chunk(100, function ($users) {
    foreach ($users as $user) {
        // Process each user
        $user->processData();
    }
});
```

## Testing Repositories

### Mocking Repositories

```php
// In tests
$mockRepository = Mockery::mock(Repository::class);
$mockRepository->shouldReceive('find')
               ->with('user-123')
               ->andReturn($expectedUser);

App::instance('streams.repository.users', $mockRepository);
```

### Factory Pattern

```php
// Create test data
$users = factory('users', 10)->create();

// With specific attributes
$admin = factory('users')->create(['role' => 'admin']);
```

## Best Practices

1. **Use appropriate methods**: Choose the right method for your use case
2. **Optimize queries**: Use eager loading and select only needed fields
3. **Cache wisely**: Cache expensive queries with appropriate TTL
4. **Handle errors**: Always handle potential exceptions
5. **Use transactions**: Wrap multiple operations in transactions
6. **Test thoroughly**: Write tests for custom repository methods
7. **Monitor performance**: Profile queries and optimize slow ones

## Repository API Reference

### Core Methods

| Method | Description | Example |
|--------|-------------|---------|
| `find($id)` | Find entry by ID | `$users->find('123')` |
| `findOrFail($id)` | Find or throw exception | `$users->findOrFail('123')` |
| `all()` | Get all entries | `$users->all()` |
| `get()` | Get query results | `$users->where('active', true)->get()` |
| `first()` | Get first result | `$users->orderBy('name')->first()` |
| `create($data)` | Create new entry | `$users->create(['name' => 'John'])` |
| `update($id, $data)` | Update entry | `$users->update('123', ['name' => 'Jane'])` |
| `delete($id)` | Delete entry | `$users->delete('123')` |

### Query Methods

| Method | Description | Example |
|--------|-------------|---------|
| `where($field, $value)` | Add where condition | `$users->where('active', true)` |
| `orWhere($field, $value)` | Add or where condition | `$users->orWhere('role', 'admin')` |
| `whereIn($field, $values)` | Where in array | `$users->whereIn('id', [1,2,3])` |
| `orderBy($field, $dir)` | Order results | `$users->orderBy('name', 'asc')` |
| `limit($count)` | Limit results | `$users->limit(10)` |
| `with($relations)` | Eager load relations | `$users->with('profile')` |
| `count()` | Count results | `$users->where('active', true)->count()` |
