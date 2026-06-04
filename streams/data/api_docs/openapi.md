---
id: openapi
sort_order: 7
status: published
title: OpenAPI Documentation
description: Generate Swagger/OpenAPI documentation for your API automatically.
---

# OpenAPI Documentation

The Streams API can automatically generate OpenAPI 3.0 (Swagger) documentation for your API endpoints.

## Generating Documentation

### Generate Schema

Create an OpenAPI YAML schema:

```bash
php artisan api:schema
```

This creates `openapi.yaml` in your project root.

Custom output path:

```bash
php artisan api:schema storage/api/openapi.yaml
```

### Generate Swagger UI

Create a complete Swagger UI documentation site:

```bash
php artisan api:documentation
```

This creates a `docs/api/` directory with:
- Swagger UI HTML/CSS/JS files
- Auto-generated `openapi.yaml` schema
- Interactive API documentation

Custom output directory:

```bash
php artisan api:documentation public/api-docs
```

## Viewing Documentation

After running `api:documentation`, visit:

```
http://your-app.test/docs/api/index.html
```

You'll see an interactive Swagger UI where you can:
- Browse all endpoints
- View request/response schemas
- Test endpoints directly in the browser
- See authentication requirements

## Schema Structure

The generated OpenAPI schema includes:

### API Information

```yaml
openapi: 3.0.2
info:
  title: Streams API
  version: 1.0.0
  description: RESTful API for Streams platform
```

### Servers

```yaml
servers:
  - url: http://your-app.test/api
    description: Development server
```

### Paths

All registered routes with:
- HTTP methods
- Parameters
- Request bodies
- Response schemas
- Authentication requirements

### Components

Reusable schemas for:
- Entry models
- Stream definitions
- Error responses
- Pagination metadata

## Customizing Documentation

### Adding Descriptions

Use PHPDoc comments on your controllers:

```php
/**
 * Get featured posts
 *
 * Returns a list of posts marked as featured, sorted by featured date.
 *
 * @return \Illuminate\Http\JsonResponse
 */
public function featured()
{
    $posts = Streams::entries('posts')
        ->where('featured', true)
        ->orderBy('featured_at', 'desc')
        ->limit(10)
        ->get();
        
    return ApiResponse::make($posts);
}
```

### Adding Examples

Document expected responses:

```php
/**
 * Create a new post
 *
 * @param Request $request
 * @return \Illuminate\Http\JsonResponse
 *
 * @example
 * Request:
 * {
 *   "title": "My New Post",
 *   "content": "Post content here...",
 *   "status": "draft"
 * }
 *
 * Response:
 * {
 *   "data": {
 *     "id": "post-123",
 *     "title": "My New Post",
 *     "status": "draft",
 *     "created_at": "2024-01-15T10:30:00Z"
 *   }
 * }
 */
public function create(Request $request)
{
    // Implementation
}
```

## Publishing to External Services

### Export for API Gateway

The generated OpenAPI schema works with:
- AWS API Gateway
- Azure API Management
- Kong
- Apigee

Simply import the `openapi.yaml` file.

### Share with Frontend Team

The Swagger UI provides a shareable interface for frontend developers to:
- Understand available endpoints
- See request/response formats
- Test API calls
- Copy example code

## Versioning Documentation

Generate documentation for specific API versions:

```bash
# V1 API
php artisan api:schema storage/api/v1/openapi.yaml
php artisan api:documentation public/api-docs/v1

# V2 API
php artisan api:schema storage/api/v2/openapi.yaml
php artisan api:documentation public/api-docs/v2
```

## CI/CD Integration

Add to your deployment pipeline:

```yaml
# .github/workflows/deploy.yml
- name: Generate API Documentation
  run: |
    php artisan api:schema
    php artisan api:documentation
    
- name: Deploy Documentation
  run: |
    aws s3 sync docs/api/ s3://your-bucket/api-docs/
```

## Schema Validation

The generated schema follows OpenAPI 3.0.2 specification and can be validated with tools like:
- Swagger Editor
- Spectral
- OpenAPI Generator

## Example Schema

```yaml
openapi: 3.0.2
info:
  title: Streams API
  version: 1.0.0

servers:
  - url: http://localhost/api
    description: Development

paths:
  /streams:
    get:
      summary: List all streams
      responses:
        '200':
          description: Successful response
          content:
            application/json:
              schema:
                type: object
                properties:
                  data:
                    type: array
                    items:
                      $ref: '#/components/schemas/Stream'
                      
  /streams/{stream}/entries:
    get:
      summary: List entries
      parameters:
        - name: stream
          in: path
          required: true
          schema:
            type: string
        - name: page
          in: query
          schema:
            type: integer
        - name: per_page
          in: query
          schema:
            type: integer
      responses:
        '200':
          description: Successful response

components:
  schemas:
    Stream:
      type: object
      properties:
        id:
          type: string
        name:
          type: string
        description:
          type: string
          
    Entry:
      type: object
      properties:
        id:
          type: string
        created_at:
          type: string
          format: date-time
```

## Best Practices

1. **Regenerate regularly** - Keep docs in sync with code changes
2. **Add descriptions** - Use PHPDoc comments
3. **Include examples** - Show expected input/output
4. **Version your docs** - Maintain docs for each API version
5. **Automate generation** - Add to CI/CD pipeline
6. **Test endpoints** - Use Swagger UI to verify endpoints work

## Next Steps

- [Testing](testing) - Test your API endpoints
- [Deployment](deployment) - Deploy your API
- [Security](security) - Secure your API
