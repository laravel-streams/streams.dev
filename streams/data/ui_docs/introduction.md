---
title: Introduction
description: 'User interface and control panel components for Laravel Streams.'
sort_order: 0
category: core-concepts
status: live
---

# Streams UI

The Streams UI package (`streams/ui`) provides a comprehensive user interface layer for Laravel Streams applications, including a control panel, forms, tables, and reusable components.

## What is Streams UI?

Streams UI is a complete frontend solution that automatically generates:

- **Control Panel**: Admin interface for managing streams
- **Forms**: Automatic form generation based on stream fields
- **Tables**: Data tables with filtering, sorting, and pagination
- **Components**: Reusable UI components for rapid development
- **Layouts**: Responsive layouts and navigation systems
- **Themes**: Customizable themes and styling

## Key Features

### Automatic UI Generation
Generate complete admin interfaces from your stream configurations without writing frontend code.

### Component Library
Rich set of pre-built components including forms, tables, modals, notifications, and more.

### Responsive Design
Mobile-first, responsive layouts that work on all devices.

### Customizable Themes
Built on Tailwind CSS with customizable color schemes and styling.

### Livewire Integration
Real-time interactivity powered by Laravel Livewire.

### Asset Management
Integrated asset compilation and optimization.

## Installation

Install Streams UI via Composer:

```bash
composer require streams/ui
```

The package will automatically register and publish necessary assets.

## Configuration

### Publishing Assets

```bash
# Publish configuration
php artisan vendor:publish --provider="Streams\Ui\UiServiceProvider" --tag=config

# Publish views (optional)
php artisan vendor:publish --provider="Streams\Ui\UiServiceProvider" --tag=views

# Publish assets
php artisan vendor:publish --provider="Streams\Ui\UiServiceProvider" --tag=assets
```

### Configuration File

```php
// config/streams/ui.php
return [
    // Control panel settings
    'cp' => [
        'enabled' => true,
        'prefix' => 'admin',
        'middleware' => ['web', 'auth'],
        'theme' => 'default',
    ],
    
    // Component settings
    'components' => [
        'namespace' => 'ui',
        'prefix' => 'streams',
    ],
    
    // Asset settings
    'assets' => [
        'compile' => env('UI_COMPILE_ASSETS', true),
        'cache' => env('UI_CACHE_ASSETS', true),
    ],
    
    // Theme settings
    'theme' => [
        'default' => 'admin',
        'colors' => [
            'primary' => '#3B82F6',
            'secondary' => '#6B7280',
        ],
    ],
];
```

## Quick Start

### Control Panel Access

Once installed, access the control panel at `/admin` (or your configured prefix):

```
http://your-app.com/admin
```

### Stream Management

Your streams automatically appear in the control panel with:
- **Index pages**: List and manage entries
- **Form pages**: Create and edit entries
- **Detail pages**: View individual entries

### Basic Usage Example

```php
// Display a form for a stream
{!! UI::form('users')->render() !!}

// Display a table for a stream
{!! UI::table('users')->render() !!}

// Display a modal
{!! UI::modal('confirm-delete')->render() !!}
```

## Core Concepts

### UI Builder Pattern

Streams UI uses a builder pattern for constructing interface elements:

```php
// Form builder
$form = UI::form('users')
    ->fields(['name', 'email', 'role'])
    ->buttons(['save', 'cancel'])
    ->layout('horizontal');

// Table builder  
$table = UI::table('users')
    ->columns(['name', 'email', 'created_at'])
    ->filters(['role', 'active'])
    ->actions(['edit', 'delete']);
```

### Component System

Components are reusable UI elements:

```blade
{{-- Basic components --}}
<ui:button text="Save" type="primary" />
<ui:input name="email" type="email" />
<ui:card title="User Details">
    Content here
</ui:card>

{{-- Stream-specific components --}}
<ui:form stream="users" />
<ui:table stream="posts" />
```

### Livewire Integration

Real-time components powered by Livewire:

```php
// Create a Livewire component
class UserTable extends Component
{
    use WithPagination;
    
    public $search = '';
    
    public function render()
    {
        return UI::table('users')
            ->filter('search', $this->search)
            ->paginate(10)
            ->view();
    }
}
```

## Architecture Overview

### Layer Structure

```
┌─────────────────┐
│   Presentation  │  ← Blade Templates & Components
├─────────────────┤
│   UI Builders   │  ← Form/Table/Component Builders
├─────────────────┤
│   Livewire      │  ← Interactive Components
├─────────────────┤
│   Assets        │  ← CSS/JS Compilation
├─────────────────┤
│   Streams Core  │  ← Data Layer
└─────────────────┘
```

### Key Components

- **Builders**: Construct UI elements programmatically
- **Components**: Reusable Blade components
- **Panels**: Container for organized interfaces
- **Widgets**: Small, focused UI elements
- **Assets**: CSS/JS compilation and management
- **Themes**: Styling and appearance control

## Control Panel

### Automatic Generation

The control panel automatically generates interfaces for your streams:

```json
// streams/posts.json
{
    "name": "Posts",
    "fields": [...],
    "ui": {
        "cp": {
            "section": {
                "title": "Content",
                "description": "Manage blog posts"
            }
        }
    }
}
```

### Customization

Customize the control panel interface:

```json
{
    "ui": {
        "cp": {
            "section": {
                "icon": "document-text",
                "buttons": [
                    {
                        "text": "New Post",
                        "href": "/admin/posts/create",
                        "type": "primary"
                    }
                ]
            }
        },
        "tables": [
            {
                "handle": "default",
                "columns": ["title", "author", "status", "created_at"],
                "filters": ["status", "author"],
                "actions": ["edit", "delete"]
            }
        ],
        "forms": [
            {
                "handle": "default",
                "tabs": [
                    {
                        "title": "Content",
                        "fields": ["title", "content"]
                    },
                    {
                        "title": "Settings", 
                        "fields": ["status", "featured"]
                    }
                ]
            }
        ]
    }
}
```

## Next Steps

- [Forms and Inputs](forms)
- [Tables and Lists](tables)
- [Components](components)
- [Control Panel](control-panel)
- [Theming](theming)
- [Livewire Components](livewire)
