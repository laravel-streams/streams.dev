---
title: Admin Panels
nav_title: Admin Panels
description: Scaffold admin panels with the SDK.
section: packages
package: sdk
order: 60
tags: [sdk, admin, panels]
status: ready
---

`streams:admin` writes Blade and Livewire files. It does not register a Streams UI panel. The options that actually work, including the missing `topbar` stub and the unused `--theme` token, are in the [command reference](/docs/sdk/commands). For a configured control panel, use [Streams UI](/docs/ui/introduction) and [Theming](/docs/ui/theming).

The Laravel Streams SDK provides admin panel generation that scaffolds an administrative layout from stubs.

## Overview

The admin panel generator creates:
- **Responsive Admin Layout**: Sidebar navigation, header, and content areas
- **Dashboard**: Overview statistics and recent activity
- **CRUD Interfaces**: Complete create, read, update, delete functionality
- **Navigation**: Automatic menu generation
- **User Management**: Role-based access (when implemented)

## Quick Start

Generate a complete admin panel for any stream:

```bash
php artisan streams:admin blog_posts
```

This command creates:
- Admin layout template
- Dashboard with stream statistics
- Navigation with stream links
- Livewire components in admin namespace
- Admin-specific routes

## Layout Options

### Sidebar Layout (Default)
```bash
php artisan streams:admin products --layout=sidebar
```

Features:
- Collapsible sidebar navigation
- Mobile-responsive design
- Multi-level menu support
- Search and filter bars

### Top Navigation Layout
```bash
php artisan streams:admin products --layout=topbar
```

Features:
- Horizontal navigation bar
- Dropdown menus
- Breadcrumb navigation
- Compact design for simple interfaces

## Theme Options

### Light Theme (Default)
```bash
php artisan streams:admin products --theme=light
```

### Dark Theme
```bash
php artisan streams:admin products --theme=dark
```

## Generated Components

### Admin Layout
**File**: `resources/views/admin/layouts/app.blade.php`

The main layout includes:
- Responsive navigation
- User profile dropdown
- Notification areas
- Mobile menu toggle
- Content sections

### Dashboard
**File**: `resources/views/admin/dashboard.blade.php`

Dashboard features:
- Stream statistics cards
- Recent activity feed
- Quick action buttons
- Charts and graphs (when configured)

### Navigation
**File**: `resources/views/admin/partials/navigation.blade.php`

Navigation includes:
- Stream management links
- Settings section
- User management
- System tools

### Livewire Components
The admin generator moves standard Livewire components to the admin namespace:

- `App\Http\Livewire\Admin\{Stream}Index`
- `App\Http\Livewire\Admin\{Stream}Form`
- `App\Http\Livewire\Admin\{Stream}Show`

## Routes

The admin generator creates routes with the `/admin` prefix:

```php
// Admin Routes for blog_posts
Route::prefix('admin')->name('admin.')->group(function () {
    Route::get('/', function () { 
        return view('admin.dashboard'); 
    })->name('dashboard');
    
    Route::prefix('blog_posts')->name('blog_posts.')->group(function () {
        Route::get('/', App\Http\Livewire\Admin\BlogPostsIndex::class)->name('index');
        Route::get('/create', App\Http\Livewire\Admin\BlogPostsForm::class)->name('create');
        Route::get('/{id}', App\Http\Livewire\Admin\BlogPostsShow::class)->name('show');
        Route::get('/{id}/edit', App\Http\Livewire\Admin\BlogPostsForm::class)->name('edit');
    });
});
```

## Customization

### Custom Templates
Override default templates by creating your own stubs:

```bash
mkdir -p resources/stubs/admin
cp vendor/streams/sdk/src/Console/Commands/stubs/admin/* resources/stubs/admin/
```

### Custom Navigation
Modify the navigation partial to add custom menu items:

```blade
{{-- Add to resources/views/admin/partials/navigation.blade.php --}}
<div class="space-y-1">
    <h3 class="px-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Reports</h3>
    
    <a href="{{ route('admin.analytics') }}" class="group flex items-center px-2 py-2 text-sm font-medium rounded-md">
        {{-- Analytics icon --}}
        <svg class="mr-3 flex-shrink-0 h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
        </svg>
        Analytics
    </a>
</div>
```

### Dashboard Widgets
Add custom widgets to the dashboard:

```blade
{{-- Add to resources/views/admin/dashboard.blade.php --}}
<div class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-4">
    {{-- Custom Widget --}}
    <div class="bg-white overflow-hidden shadow rounded-lg">
        <div class="p-5">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 bg-blue-500 rounded-md flex items-center justify-center">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"/>
                        </svg>
                    </div>
                </div>
                <div class="ml-5 w-0 flex-1">
                    <dl>
                        <dt class="text-sm font-medium text-gray-500 truncate">Total Revenue</dt>
                        <dd class="text-lg font-medium text-gray-900">${{ number_format(12345, 2) }}</dd>
                    </dl>
                </div>
            </div>
        </div>
    </div>
</div>
```

## Authentication & Authorization

### Basic Authentication
Protect admin routes with authentication middleware:

```php
Route::prefix('admin')->name('admin.')->middleware(['auth'])->group(function () {
    // Admin routes
});
```

### Role-Based Access
Add role-based access control:

```php
Route::prefix('admin')->name('admin.')->middleware(['auth', 'role:admin'])->group(function () {
    // Admin routes
});
```

### Custom Guards
Use custom guards for admin users:

```php
Route::prefix('admin')->name('admin.')->middleware(['auth:admin'])->group(function () {
    // Admin routes
});
```

## Search & Filtering

### Global Search
Add global search functionality:

```blade
{{-- In admin layout header --}}
<div class="flex-1 flex justify-center lg:justify-end">
    <div class="w-full px-2 lg:px-6">
        <label for="global-search" class="sr-only">Search</label>
        <div class="relative text-gray-400 focus-within:text-gray-600">
            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
            </div>
            <input id="global-search" 
                   name="search" 
                   class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-md leading-5 bg-white placeholder-gray-500 focus:outline-none focus:placeholder-gray-400 focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" 
                   placeholder="Search everything..." 
                   type="search">
        </div>
    </div>
</div>
```

### Advanced Filters
Implement advanced filtering in list views:

```php
// In Livewire component
public $filters = [
    'status' => '',
    'category' => '',
    'date_from' => '',
    'date_to' => '',
];

public function applyFilters($query)
{
    if ($this->filters['status']) {
        $query->where('status', $this->filters['status']);
    }
    
    if ($this->filters['category']) {
        $query->where('category_id', $this->filters['category']);
    }
    
    if ($this->filters['date_from']) {
        $query->where('created_at', '>=', $this->filters['date_from']);
    }
    
    if ($this->filters['date_to']) {
        $query->where('created_at', '<=', $this->filters['date_to']);
    }
    
    return $query;
}
```

## Performance Optimization

### Caching
Implement caching for dashboard statistics:

```php
public function getDashboardStats()
{
    return Cache::remember('admin.dashboard.stats', 300, function () {
        return [
            'total_posts' => BlogPost::count(),
            'published_posts' => BlogPost::where('status', 'published')->count(),
            'total_views' => BlogPost::sum('view_count'),
            'recent_posts' => BlogPost::latest()->take(5)->get(),
        ];
    });
}
```

### Eager Loading
Optimize queries with eager loading:

```php
public function render()
{
    $entries = $this->stream->entries()
        ->with(['author', 'category', 'tags'])
        ->when($this->search, function ($query) {
            $query->search($this->search);
        })
        ->orderBy($this->sortField, $this->sortDirection)
        ->paginate($this->perPage);

    return view('livewire.admin.blog-posts-index', compact('entries'));
}
```

## Best Practices

1. **Consistent Design**: Follow established UI patterns throughout the admin
2. **Mobile-First**: Ensure all admin interfaces work on mobile devices
3. **Performance**: Cache expensive operations and use pagination
4. **Security**: Implement proper authentication and authorization
5. **Usability**: Provide clear feedback and intuitive navigation
6. **Documentation**: Document custom features and configurations
