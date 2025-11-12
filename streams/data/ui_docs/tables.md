---
title: Tables and Lists
description: 'Data tables and listing components in Streams UI.'
sort_order: 2
category: core-concepts
status: live
---

# Tables and Lists

Streams UI provides powerful table and list components that automatically generate data tables from your streams with features like filtering, sorting, pagination, and bulk actions.

## Basic Table Generation

### Automatic Tables

Generate tables directly from stream configurations:

```php
// Simple table for a stream
{!! UI::table('users')->render() !!}

// Table with specific columns
{!! UI::table('users')->columns(['name', 'email', 'created_at'])->render() !!}

// Table with filtering
{!! UI::table('users')->filters(['role', 'active'])->render() !!}
```

### Table Builder

Use the table builder for more control:

```php
$table = UI::table('users')
    ->columns(['name', 'email', 'role', 'created_at'])
    ->filters(['role', 'active', 'created_at'])
    ->actions(['edit', 'delete'])
    ->bulkActions(['activate', 'deactivate', 'delete'])
    ->pagination(20)
    ->sortable(['name', 'email', 'created_at']);

return $table->render();
```

## Table Configuration

### Stream-Level Configuration

Configure tables in your stream definition:

```json
// streams/users.json
{
    "ui": {
        "tables": [
            {
                "handle": "default",
                "columns": [
                    {
                        "handle": "avatar",
                        "type": "image",
                        "width": "60px"
                    },
                    {
                        "handle": "name",
                        "sortable": true,
                        "searchable": true
                    },
                    {
                        "handle": "email",
                        "sortable": true
                    },
                    {
                        "handle": "role",
                        "type": "badge",
                        "colors": {
                            "admin": "red",
                            "user": "blue"
                        }
                    },
                    {
                        "handle": "active",
                        "type": "boolean"
                    }
                ],
                "filters": [
                    {
                        "handle": "role",
                        "type": "select"
                    },
                    {
                        "handle": "active",
                        "type": "boolean"
                    },
                    {
                        "handle": "created_at",
                        "type": "date_range"
                    }
                ],
                "actions": [
                    {
                        "handle": "edit",
                        "icon": "pencil",
                        "href": "/admin/users/{id}/edit"
                    },
                    {
                        "handle": "delete",
                        "icon": "trash",
                        "confirm": "Are you sure?",
                        "method": "DELETE"
                    }
                ]
            }
        ]
    }
}
```

## Column Types

### Text Columns

```php
// Basic text column
UI::column('name')->type('text')

// Text with formatting
UI::column('description')
    ->type('text')
    ->limit(100)
    ->format('upper')

// Text with custom template
UI::column('status')
    ->type('text')
    ->template('tables.columns.status')
```

### Number and Currency Columns

```php
// Number column
UI::column('quantity')
    ->type('number')
    ->format('integer')

// Currency column
UI::column('price')
    ->type('currency')
    ->currency('USD')
    ->decimals(2)

// Percentage column
UI::column('completion')
    ->type('percentage')
    ->decimals(1)
```

### Date and Time Columns

```php
// Date column
UI::column('created_at')
    ->type('date')
    ->format('M j, Y')

// DateTime column
UI::column('updated_at')
    ->type('datetime')
    ->format('M j, Y g:i A')

// Relative time
UI::column('last_login')
    ->type('time_ago')
```

### Boolean and Status Columns

```php
// Boolean column
UI::column('active')
    ->type('boolean')
    ->labels(['Active', 'Inactive'])

// Badge column
UI::column('status')
    ->type('badge')
    ->colors([
        'published' => 'green',
        'draft' => 'yellow',
        'archived' => 'gray'
    ])

// Progress bar
UI::column('progress')
    ->type('progress')
    ->max(100)
    ->color('blue')
```

### Relationship Columns

```php
// Simple relationship
UI::column('author')
    ->type('relationship')
    ->display('name')

// Relationship with link
UI::column('category')
    ->type('relationship')
    ->display('name')
    ->link('/admin/categories/{id}')

// Multiple relationships
UI::column('tags')
    ->type('relationships')
    ->display('name')
    ->separator(', ')
```

### Media Columns

```php
// Image column
UI::column('avatar')
    ->type('image')
    ->size('40x40')
    ->rounded(true)

// File column
UI::column('document')
    ->type('file')
    ->showSize(true)
    ->downloadable(true)
```

### Custom Columns

```php
// Custom column with callback
UI::column('full_name')
    ->callback(function ($entry) {
        return $entry->first_name . ' ' . $entry->last_name;
    })

// Custom column with view
UI::column('actions')
    ->view('tables.columns.actions')
    ->data(['permissions' => auth()->user()->permissions])
```

## Filtering

### Basic Filters

```php
$table = UI::table('users')
    ->filter('role', 'select', [
        'options' => ['admin', 'user', 'moderator']
    ])
    ->filter('active', 'boolean')
    ->filter('name', 'search');
```

### Advanced Filters

```php
// Date range filter
$table->filter('created_at', 'date_range', [
    'label' => 'Registration Date',
    'format' => 'Y-m-d'
]);

// Number range filter
$table->filter('age', 'number_range', [
    'min' => 18,
    'max' => 100
]);

// Multi-select filter
$table->filter('permissions', 'multiselect', [
    'options' => $permissionOptions,
    'searchable' => true
]);

// Custom filter
$table->filter('location', 'custom', [
    'template' => 'filters.location',
    'query' => function ($query, $value) {
        return $query->where('city', $value['city'])
                    ->where('country', $value['country']);
    }
]);
```

### Filter Groups

```php
$table = UI::table('users')
    ->filterGroup('Basic', ['name', 'email', 'role'])
    ->filterGroup('Advanced', ['created_at', 'last_login', 'permissions'])
    ->filterGroup('Location', ['city', 'country', 'timezone']);
```

## Sorting

### Column Sorting

```php
// Make columns sortable
$table = UI::table('users')
    ->column('name')->sortable()
    ->column('email')->sortable()
    ->column('created_at')->sortable()->sort('desc') // Default sort
    ->column('role')->sortable(['admin', 'moderator', 'user']); // Custom sort order
```

### Multi-Column Sorting

```php
// Enable multi-column sorting
$table = UI::table('users')
    ->multiSort(true)
    ->defaultSort([
        'role' => 'asc',
        'name' => 'asc'
    ]);
```

### Custom Sorting

```php
// Custom sort logic
$table->column('priority')
    ->sortable()
    ->sortUsing(function ($query, $direction) {
        return $query->orderByRaw("FIELD(priority, 'high', 'medium', 'low') $direction");
    });
```

## Actions

### Row Actions

```php
$table = UI::table('users')
    ->action('edit', [
        'icon' => 'pencil',
        'href' => '/admin/users/{id}/edit',
        'permission' => 'users.edit'
    ])
    ->action('view', [
        'icon' => 'eye',
        'href' => '/admin/users/{id}',
        'target' => '_blank'
    ])
    ->action('delete', [
        'icon' => 'trash',
        'confirm' => 'Are you sure you want to delete this user?',
        'method' => 'DELETE',
        'permission' => 'users.delete'
    ]);
```

### Conditional Actions

```php
// Show action based on entry data
$table->action('activate', [
    'icon' => 'check',
    'showIf' => function ($entry) {
        return !$entry->active;
    }
]);

// Multiple conditions
$table->action('promote', [
    'icon' => 'arrow-up',
    'showIf' => [
        'role' => ['user', 'moderator'],
        'active' => true
    ]
]);
```

### Bulk Actions

```php
$table = UI::table('users')
    ->bulkAction('activate', [
        'text' => 'Activate Selected',
        'icon' => 'check',
        'confirm' => 'Activate {count} users?'
    ])
    ->bulkAction('delete', [
        'text' => 'Delete Selected',
        'icon' => 'trash',
        'confirm' => 'Delete {count} users? This cannot be undone.',
        'permission' => 'users.delete'
    ])
    ->bulkAction('export', [
        'text' => 'Export Selected',
        'icon' => 'download',
        'href' => '/admin/users/export'
    ]);
```

## Pagination

### Basic Pagination

```php
$table = UI::table('users')
    ->paginate(25) // 25 items per page
    ->paginationView('pagination.custom');
```

### Advanced Pagination

```php
$table = UI::table('users')
    ->paginate(25)
    ->showPerPageOptions([10, 25, 50, 100])
    ->showPaginationInfo(true)
    ->paginationOnTop(true);
```

### Infinite Scroll

```php
$table = UI::table('users')
    ->infiniteScroll(true)
    ->loadMoreText('Load More Users');
```

## Search and Filtering

### Global Search

```php
$table = UI::table('users')
    ->searchable(['name', 'email', 'bio'])
    ->searchPlaceholder('Search users...')
    ->searchButton(true);
```

### Advanced Search

```php
$table = UI::table('users')
    ->advancedSearch([
        'name' => ['type' => 'text', 'operator' => 'like'],
        'email' => ['type' => 'text', 'operator' => 'exact'],
        'created_at' => ['type' => 'date_range'],
        'role' => ['type' => 'select', 'options' => $roles]
    ]);
```

## Responsive Design

### Mobile Configuration

```php
$table = UI::table('users')
    ->responsive(true)
    ->mobileColumns(['name', 'role']) // Show only these on mobile
    ->mobileStack(true) // Stack columns vertically
    ->mobileCards(true); // Show as cards instead of table
```

### Column Priorities

```php
$table = UI::table('users')
    ->column('name')->priority(1) // Always show
    ->column('email')->priority(2) // Show on tablet+
    ->column('role')->priority(3) // Show on desktop+
    ->column('created_at')->priority(4); // Show on large screens
```

## Styling and Theming

### CSS Classes

```php
$table = UI::table('users')
    ->class('table-custom')
    ->headerClass('bg-gray-50')
    ->rowClass(function ($entry) {
        return $entry->active ? 'bg-white' : 'bg-gray-100';
    })
    ->cellClass('px-4 py-2');
```

### Custom Templates

```php
$table = UI::table('users')
    ->template('tables.custom')
    ->headerTemplate('tables.header')
    ->rowTemplate('tables.row')
    ->cellTemplate('tables.cell');
```

## Performance Optimization

### Lazy Loading

```php
$table = UI::table('users')
    ->lazy(true)
    ->lazyPlaceholder('Loading users...')
    ->lazyHeight('400px');
```

### Caching

```php
$table = UI::table('users')
    ->cache(3600) // Cache for 1 hour
    ->cacheKey('users-table')
    ->cacheTags(['users']);
```

### Query Optimization

```php
$table = UI::table('users')
    ->with(['profile', 'roles']) // Eager load relationships
    ->select(['id', 'name', 'email', 'created_at']) // Select only needed columns
    ->chunk(100); // Process in chunks
```

## Events and Hooks

### Table Events

```javascript
// Row clicked
document.addEventListener('table:row:clicked', function(e) {
    console.log('Row clicked:', e.detail.entry);
});

// Bulk action executed
document.addEventListener('table:bulk:executed', function(e) {
    console.log('Bulk action:', e.detail.action, e.detail.entries);
});
```

### PHP Hooks

```php
// Before table renders
Event::listen('table.rendering:users', function ($table) {
    if (auth()->user()->cannot('view_all_users')) {
        $table->where('created_by', auth()->id());
    }
});

// After data loaded
Event::listen('table.loaded:users', function ($table, $entries) {
    // Process entries
});
```

## Export and Import

### Export Features

```php
$table = UI::table('users')
    ->exportable(['csv', 'excel', 'pdf'])
    ->exportFilename('users-{date}')
    ->exportColumns(['name', 'email', 'role', 'created_at']);
```

### Import Features

```php
$table = UI::table('users')
    ->importable(true)
    ->importTemplate('/admin/users/import-template')
    ->importRules([
        'name' => 'required',
        'email' => 'required|email|unique:users'
    ]);
```

## Best Practices

1. **Optimize queries**: Use select() and with() to load only needed data
2. **Use appropriate pagination**: Don't load too many records at once
3. **Cache when possible**: Cache expensive table queries
4. **Make it responsive**: Ensure tables work well on mobile devices
5. **Provide clear actions**: Use descriptive action labels and icons
6. **Handle empty states**: Show meaningful messages when no data exists
7. **Use bulk actions**: Provide efficient ways to perform mass operations
8. **Filter intelligently**: Provide relevant filters for your data
9. **Sort strategically**: Default to useful sort orders
10. **Test accessibility**: Ensure tables are accessible to all users

## Complete Example

```php
$table = UI::table('posts')
    // Columns
    ->column('featured_image', 'image', ['size' => '60x60'])
    ->column('title', 'text', ['sortable' => true, 'searchable' => true])
    ->column('author', 'relationship', ['display' => 'name'])
    ->column('category', 'relationship', ['display' => 'name'])
    ->column('status', 'badge', [
        'colors' => [
            'published' => 'green',
            'draft' => 'yellow',
            'archived' => 'gray'
        ]
    ])
    ->column('views', 'number', ['sortable' => true])
    ->column('published_at', 'date', ['format' => 'M j, Y'])
    
    // Filters
    ->filter('status', 'select', ['options' => $statusOptions])
    ->filter('author', 'relationship', ['related' => 'users'])
    ->filter('category', 'relationship', ['related' => 'categories'])
    ->filter('published_at', 'date_range')
    
    // Actions
    ->action('edit', ['icon' => 'pencil', 'href' => '/admin/posts/{id}/edit'])
    ->action('view', ['icon' => 'eye', 'href' => '/posts/{slug}', 'target' => '_blank'])
    ->action('duplicate', ['icon' => 'copy', 'method' => 'POST'])
    ->action('delete', ['icon' => 'trash', 'confirm' => true, 'method' => 'DELETE'])
    
    // Bulk actions
    ->bulkAction('publish', ['text' => 'Publish Selected'])
    ->bulkAction('archive', ['text' => 'Archive Selected'])
    ->bulkAction('delete', ['text' => 'Delete Selected', 'confirm' => true])
    
    // Configuration
    ->paginate(25)
    ->searchable(['title', 'content'])
    ->sortable(['title', 'published_at', 'views'])
    ->responsive(true)
    ->exportable(['csv', 'excel']);
```
