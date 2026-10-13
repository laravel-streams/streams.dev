---
title: AI Prompts
nav_title: AI Prompts
description: Patterns and prompts for AI-assisted Streams development.
section: packages
package: sdk
order: 60
tags: [sdk, ai, prompts]
status: ready
---

This guide provides AI assistants with specific prompts, patterns, and examples for effectively using the Laravel Streams SDK to help developers build applications.

## AI Assistant Workflow

When helping users with Laravel Streams, follow this structured approach:

### 1. Requirements Gathering
Ask clarifying questions to understand the project:

```text
Before we start building with Laravel Streams, I need to understand your requirements:

1. What type of application are you building? (blog, e-commerce, CRM, etc.)
2. What data entities do you need to manage?
3. Do you need an admin panel for content management?
4. What are the key relationships between your data?
5. Do you have any specific UI/UX requirements?
6. Will this be used by multiple user types/roles?
```

### 2. Data Structure Analysis
Help users design their stream structure:

```text
Based on your requirements, I recommend these streams:

For a blog application:
- blog_posts: Main content with title, content, author, category
- categories: Organize posts by topic
- tags: Flexible labeling system
- users: Authors and administrators

Let me create the stream definitions for you.
```

### 3. Implementation Steps
Provide a clear roadmap:

```text
Here's how we'll implement your application:

1. Create stream definitions with proper field types
2. Generate Livewire components for CRUD operations
3. Build an admin panel for content management
4. Customize the UI to match your design requirements
5. Add any custom business logic

Let's start with step 1...
```

## Common Prompts for Stream Creation

### Blog System
```bash
# Create the main blog stream
php artisan make:stream blog_posts

# Generate the index, form, and show Livewire components
php artisan streams:livewire blog_posts

# Publish blog example for reference
php artisan vendor:publish --tag=blog-example
```

### E-commerce System
```bash
# Create product catalog
php artisan make:stream products

# Generate the index and form components
php artisan streams:livewire products --type=index
php artisan streams:livewire products --type=form

# Publish e-commerce example
php artisan vendor:publish --tag=ecommerce-example
```

### CRM System
```bash
# Create contact management
php artisan make:stream contacts

# Generate CRM components (index, form, and show)
php artisan streams:livewire contacts

# Publish CRM example
php artisan vendor:publish --tag=crm-example
```

## Field Type Recommendations

### Content Management
When users need content management, recommend these field types. Field behavior is Core's ([Fields](/docs/core/fields)): length and range limits go in `rules`, long text is a `string` with an `input` hint, and type options go in `config`:

```json
{
    "fields": {
        "title": {
            "type": "string",
            "required": true,
            "rules": [
                "max:255"
            ]
        },
        "slug": {
            "type": "slug",
            "unique": true
        },
        "content": {
            "type": "string",
            "required": true,
            "input": {
                "type": "editor"
            }
        },
        "featured_image": {
            "type": "image"
        },
        "status": {
            "type": "select",
            "config": {
                "options": {
                    "draft": "Draft",
                    "published": "Published",
                    "archived": "Archived"
                }
            }
        }
    }
}
```

### E-commerce Fields
For product catalogs, suggest:

```json
{
    "fields": {
        "name": {
            "type": "string",
            "required": true
        },
        "sku": {
            "type": "string",
            "unique": true,
            "required": true
        },
        "price": {
            "type": "decimal",
            "required": true,
            "rules": [
                "min:0"
            ],
            "config": {
                "precision": 2
            }
        },
        "stock_quantity": {
            "type": "integer",
            "rules": [
                "min:0"
            ],
            "config": {
                "default": 0
            }
        },
        "is_featured": {
            "type": "boolean",
            "config": {
                "default": false
            }
        }
    }
}
```

### User Management
For user-related streams:

```json
{
    "fields": {
        "first_name": {
            "type": "string",
            "required": true,
            "rules": [
                "max:100"
            ]
        },
        "last_name": {
            "type": "string",
            "required": true,
            "rules": [
                "max:100"
            ]
        },
        "email": {
            "type": "email",
            "unique": true,
            "required": true
        },
        "avatar": {
            "type": "image"
        },
        "role": {
            "type": "select",
            "config": {
                "options": {
                    "user": "User",
                    "admin": "Administrator",
                    "moderator": "Moderator"
                }
            }
        }
    }
}
```

## Customization Prompts

### UI Customization
When users want to customize the interface:

```text
I can help you customize the generated components. Here are common customizations:

1. **Styling**: Modify Tailwind CSS classes in the Blade templates
2. **Layout**: Change the grid layout or add/remove sections
3. **Functionality**: Add custom Alpine.js interactions
4. **Validation**: Enhance form validation rules
5. **Navigation**: Customize the admin panel navigation

Which aspect would you like to customize first?
```

### Business Logic
For adding custom functionality:

```text
To add custom business logic to your streams:

1. **Custom Methods**: Add methods to your Livewire components
2. **Event Listeners**: Set up event handling for real-time updates
3. **Validation Rules**: Create custom validation logic
4. **Relationships**: Define complex data relationships
5. **APIs**: Add API endpoints for external integrations

Let me show you how to implement [specific feature]...
```

## Troubleshooting Prompts

### Common Issues
Help users resolve typical problems:

```text
Let me help you troubleshoot this issue. Here are the most common problems and solutions:

1. **Missing Stream**: Make sure the stream definition exists in streams/ directory
2. **Component Errors**: Check that all required fields are defined
3. **Route Conflicts**: Verify routes are properly namespaced
4. **Permission Issues**: Ensure proper file permissions for generated files
5. **Asset Issues**: Run `npm run dev` to compile Tailwind CSS

Can you share the specific error message you're seeing?
```

### Performance Optimization
When users need performance improvements:

```text
Here are performance optimization strategies for your streams:

1. **Eager Loading**: Load related data efficiently
2. **Caching**: Cache expensive queries and computations
3. **Pagination**: Implement proper pagination for large datasets
4. **Indexing**: Add database indexes for frequently queried fields
5. **Image Optimization**: Compress and resize images automatically

Let me show you how to implement these optimizations...
```

## Advanced Patterns

### Multi-tenant Applications
For SaaS applications:

```php
// Add tenant filtering to streams
public function render()
{
    $entries = $this->stream->entries()
        ->where('tenant_id', auth()->user()->tenant_id)
        ->paginate($this->perPage);
        
    return view('livewire.blog-posts-index', compact('entries'));
}
```

### API Integration
For headless/API-first applications:

```php
// Add API endpoints
Route::apiResource('blog-posts', BlogPostApiController::class);

// In controller
public function index()
{
    $stream = Streams::make('blog_posts');
    
    return BlogPostResource::collection(
        $stream->entries()->paginate(request('per_page', 15))
    );
}
```

### Real-time Updates
For live data updates:

```php
// Add broadcasting to Livewire components
protected $listeners = ['postUpdated' => 'refreshPosts'];

public function refreshPosts()
{
    $this->emit('$refresh');
}

// Broadcast events when data changes
public function save()
{
    // Save logic here
    
    broadcast(new PostUpdated($this->entry));
}
```

## Code Generation Examples

### Complete Blog Setup
```bash
# 1. Publish blog example
php artisan vendor:publish --tag=blog-example

# 2. Check the definition, then generate index, form, and show components
php artisan streams:validate streams/blog_posts.json
php artisan streams:livewire blog_posts

# 3. Add the printed routes to routes/web.php
```

### E-commerce Product Catalog
```bash
# 1. Publish product example
php artisan vendor:publish --tag=ecommerce-example

# 2. Generate product management
php artisan streams:livewire products --type=index
php artisan streams:livewire products --type=form

# 3. Add the printed routes to routes/web.php, then customize the
#    generated classes in app/Livewire and views in resources/views/livewire
```

## Best Practices for AI Assistants

### 1. Always Ask Context Questions
Before generating code, understand:
- User's experience level
- Project requirements
- Existing codebase
- Performance needs
- Security requirements

### 2. Provide Complete Solutions
Don't just generate partial code:
- Include all necessary files
- Add proper validation
- Include error handling
- Provide styling
- Add documentation

### 3. Explain Choices
When recommending field types or patterns:
- Explain why you chose specific options
- Mention alternatives
- Discuss trade-offs
- Provide examples

### 4. Follow Security Best Practices
Always include:
- Proper validation rules
- CSRF protection
- Authorization checks
- Input sanitization
- SQL injection prevention

### 5. Consider Performance
Optimize generated code:
- Use pagination for large datasets
- Implement caching where appropriate
- Add database indexes
- Optimize images and assets
- Use eager loading for relationships

## Sample Conversations

### Blog Setup Conversation
```text
User: I want to create a blog with Laravel Streams.

AI: I'd be happy to help you create a blog with Laravel Streams! Let me ask a few questions to ensure we build exactly what you need:

1. Will you have multiple authors or just yourself?
2. Do you need categories and tags for organizing posts?
3. Do you want an admin panel for content management?
4. Any specific design requirements or should we use the default TALL stack styling?

Based on your answers, I'll generate the complete blog system for you.
```

### E-commerce Conversation
```text
User: I need to build a product catalog.

AI: Great! A product catalog is perfect for Laravel Streams. To build the right solution, I need to understand your requirements:

1. What types of products will you sell? (physical, digital, or both)
2. Do you need inventory management?
3. Will you have product variants (size, color, etc.)?
4. Do you need categories and brands?
5. Any specific pricing features (sales, bulk pricing, etc.)?

Once I understand your needs, I'll create the complete product management system with admin panel.
```

This guide ensures AI assistants can effectively help users build robust applications with the Laravel Streams SDK while following best practices and providing comprehensive solutions.
