---
id: testing
sort_order: 8
status: published
title: Testing
description: Test your API endpoints with PHPUnit and Laravel's testing tools.
---

# Testing

Learn how to test your Streams API endpoints effectively.

## Setup

The Streams API includes a base test case for API testing:

```php
namespace Tests\Api;

use Streams\Api\Tests\ApiTestCase;

class MyApiTest extends ApiTestCase
{
    // Your tests here
}
```

The `ApiTestCase` automatically:
- Sets up the API environment
- Registers default routes
- Provides helper methods

## Basic Endpoint Testing

### Testing GET Requests

```php
public function test_it_lists_posts()
{
    // Create test data
    Streams::entries('posts')->create([
        'title' => 'Test Post',
        'status' => 'published',
    ]);
    
    // Make request
    $response = $this->getJson('/api/streams/posts/entries');
    
    // Assert response
    $response->assertStatus(200)
        ->assertJsonStructure([
            'data' => [
                '*' => ['id', 'title', 'status']
            ]
        ]);
}
```

### Testing POST Requests

```php
public function test_it_creates_a_post()
{
    $data = [
        'title' => 'New Post',
        'content' => 'Post content...',
        'status' => 'draft',
    ];
    
    $response = $this->postJson('/api/streams/posts/entries', $data);
    
    $response->assertStatus(201)
        ->assertJson([
            'data' => [
                'title' => 'New Post',
                'status' => 'draft',
            ]
        ]);
        
    // Verify in database
    $this->assertDatabaseHas('posts', [
        'title' => 'New Post',
    ]);
}
```

### Testing PUT/PATCH Requests

```php
public function test_it_updates_a_post()
{
    $post = Streams::entries('posts')->create([
        'title' => 'Original Title',
        'status' => 'draft',
    ]);
    
    $response = $this->patchJson("/api/streams/posts/entries/{$post->id}", [
        'title' => 'Updated Title',
    ]);
    
    $response->assertStatus(200)
        ->assertJson([
            'data' => [
                'title' => 'Updated Title',
            ]
        ]);
}
```

### Testing DELETE Requests

```php
public function test_it_deletes_a_post()
{
    $post = Streams::entries('posts')->create([
        'title' => 'Post to Delete',
    ]);
    
    $response = $this->deleteJson("/api/streams/posts/entries/{$post->id}");
    
    $response->assertStatus(204);
    
    $this->assertDatabaseMissing('posts', [
        'id' => $post->id,
    ]);
}
```

## Testing Query Parameters

### Filtering

```php
public function test_it_filters_posts_by_status()
{
    Streams::entries('posts')->create(['title' => 'Draft', 'status' => 'draft']);
    Streams::entries('posts')->create(['title' => 'Published', 'status' => 'published']);
    
    $response = $this->getJson('/api/streams/posts/entries?where[status]=published');
    
    $response->assertStatus(200)
        ->assertJsonCount(1, 'data')
        ->assertJsonPath('data.0.status', 'published');
}
```

### Pagination

```php
public function test_it_paginates_results()
{
    // Create 25 posts
    for ($i = 1; $i <= 25; $i++) {
        Streams::entries('posts')->create(['title' => "Post {$i}"]);
    }
    
    $response = $this->getJson('/api/streams/posts/entries?per_page=10&page=1');
    
    $response->assertStatus(200)
        ->assertJsonCount(10, 'data')
        ->assertJsonStructure([
            'data',
            'links' => ['first', 'last', 'prev', 'next'],
            'meta' => ['current_page', 'total', 'per_page'],
        ])
        ->assertJsonPath('meta.total', 25)
        ->assertJsonPath('meta.current_page', 1);
}
```

## Testing Custom Endpoints

```php
public function test_it_returns_featured_posts()
{
    Streams::entries('posts')->create(['title' => 'Regular Post', 'featured' => false]);
    Streams::entries('posts')->create(['title' => 'Featured Post', 'featured' => true]);
    
    $response = $this->getJson('/api/v1/posts/featured');
    
    $response->assertStatus(200)
        ->assertJsonCount(1, 'data')
        ->assertJsonPath('data.0.featured', true);
}
```

## Testing Authentication

### Without Authentication

```php
public function test_it_requires_authentication()
{
    $response = $this->postJson('/api/admin/posts', [
        'title' => 'New Post',
    ]);
    
    $response->assertStatus(401);
}
```

### With Sanctum

```php
use Laravel\Sanctum\Sanctum;

public function test_authenticated_user_can_create_post()
{
    $user = User::factory()->create();
    
    Sanctum::actingAs($user);
    
    $response = $this->postJson('/api/admin/posts', [
        'title' => 'New Post',
        'content' => 'Content...',
    ]);
    
    $response->assertStatus(201);
}
```

### With Specific Abilities

```php
public function test_user_needs_publish_ability()
{
    $user = User::factory()->create();
    
    Sanctum::actingAs($user, ['posts:publish']);
    
    $response = $this->postJson('/api/admin/posts/1/publish');
    
    $response->assertStatus(200);
}
```

## Testing Validation

### Validation Errors

```php
public function test_it_validates_required_fields()
{
    $response = $this->postJson('/api/streams/posts/entries', [
        'content' => 'Content without title',
    ]);
    
    $response->assertStatus(422)
        ->assertJsonValidationErrors(['title']);
}
```

### Multiple Validation Errors

```php
public function test_it_returns_multiple_validation_errors()
{
    $response = $this->postJson('/api/streams/posts/entries', []);
    
    $response->assertStatus(422)
        ->assertJsonValidationErrors(['title', 'content']);
}
```

## Testing Error Responses

### Not Found

```php
public function test_it_returns_404_for_missing_post()
{
    $response = $this->getJson('/api/streams/posts/entries/non-existent-id');
    
    $response->assertStatus(404)
        ->assertJson([
            'errors' => ['Post not found']
        ]);
}
```

### Conflict

```php
public function test_it_returns_409_for_duplicate_slug()
{
    Streams::entries('posts')->create(['title' => 'Existing Post', 'slug' => 'test-slug']);
    
    $response = $this->postJson('/api/streams/posts/entries', [
        'title' => 'Another Post',
        'slug' => 'test-slug',
    ]);
    
    $response->assertStatus(409);
}
```

## Testing with Factories

Create test data easily:

```php
use Illuminate\Database\Eloquent\Factories\Factory;

class PostFactory extends Factory
{
    public function definition()
    {
        return [
            'title' => $this->faker->sentence(),
            'content' => $this->faker->paragraphs(3, true),
            'status' => $this->faker->randomElement(['draft', 'published']),
        ];
    }
}

// In tests
public function test_with_factory()
{
    $posts = Post::factory()->count(10)->create();
    
    $response = $this->getJson('/api/streams/posts/entries');
    
    $response->assertStatus(200)
        ->assertJsonCount(10, 'data');
}
```

## Testing Rate Limiting

```php
public function test_it_rate_limits_requests()
{
    for ($i = 0; $i < 60; $i++) {
        $this->getJson('/api/streams/posts/entries');
    }
    
    $response = $this->getJson('/api/streams/posts/entries');
    
    $response->assertStatus(429);
}
```

## Testing Middleware

```php
public function test_admin_middleware_blocks_regular_users()
{
    $user = User::factory()->create(['is_admin' => false]);
    
    Sanctum::actingAs($user);
    
    $response = $this->getJson('/api/admin/stats');
    
    $response->assertStatus(403);
}

public function test_admin_middleware_allows_admins()
{
    $admin = User::factory()->create(['is_admin' => true]);
    
    Sanctum::actingAs($admin);
    
    $response = $this->getJson('/api/admin/stats');
    
    $response->assertStatus(200);
}
```

## Integration Tests

Test complete workflows:

```php
public function test_complete_blog_post_workflow()
{
    $user = User::factory()->create();
    Sanctum::actingAs($user);
    
    // Create draft
    $response = $this->postJson('/api/streams/posts/entries', [
        'title' => 'My Post',
        'content' => 'Content...',
        'status' => 'draft',
    ]);
    $postId = $response->json('data.id');
    $response->assertStatus(201);
    
    // Update draft
    $response = $this->patchJson("/api/streams/posts/entries/{$postId}", [
        'title' => 'Updated Title',
    ]);
    $response->assertStatus(200);
    
    // Publish
    $response = $this->postJson("/api/v1/posts/{$postId}/publish");
    $response->assertStatus(200)
        ->assertJsonPath('data.status', 'published');
    
    // Verify publicly visible
    $response = $this->getJson('/api/streams/posts/entries?where[status]=published');
    $response->assertStatus(200)
        ->assertJsonPath('data.0.id', $postId);
    
    // Delete
    $response = $this->deleteJson("/api/streams/posts/entries/{$postId}");
    $response->assertStatus(204);
}
```

## Running Tests

### Run all tests

```bash
php vendor/bin/phpunit
```

### Run specific test

```bash
php vendor/bin/phpunit --filter test_it_creates_a_post
```

### Run with coverage

```bash
XDEBUG_MODE=coverage php vendor/bin/phpunit --coverage-html=./coverage
```

### Run specific file

```bash
php vendor/bin/phpunit tests/Api/PostsTest.php
```

## Best Practices

1. **Test happy paths** - Verify successful operations
2. **Test error cases** - Ensure proper error handling
3. **Test validation** - Check all validation rules
4. **Test authentication** - Verify access control
5. **Use factories** - Generate test data efficiently
6. **Clean up** - Reset database between tests
7. **Be specific** - Test one thing per test method
8. **Name clearly** - Use descriptive test names

## Next Steps

- [Deployment](deployment) - Deploy your API
- [Security](security) - Secure your API
- [Performance](performance) - Optimize API performance
