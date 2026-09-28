---
title: TALL Components
nav_title: TALL Components
description: Tailwind, Alpine, Laravel, and Livewire patterns in the SDK.
section: packages
package: sdk
order: 50
tags: [sdk, tall, components]
status: ready
---

# TALL Stack Components

This guide covers how the Laravel Streams SDK generates and works with TALL stack components (Tailwind CSS, Alpine.js, Laravel, and Livewire).

## Overview

The SDK automatically generates:
- **Tailwind CSS**: Utility-first styling with pre-built component classes
- **Alpine.js**: Lightweight JavaScript framework for interactivity
- **Laravel**: Backend controllers, routes, and middleware
- **Livewire**: Full-stack reactive components

## Generated Components

### Livewire Components

#### Index Component (List View)
```php
<?php

namespace App\Http\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use Streams\Core\Support\Facades\Streams;

class BlogPostIndex extends Component
{
    use WithPagination;

    public $search = '';
    public $sortField = 'created_at';
    public $sortDirection = 'desc';
    public $perPage = 15;

    protected $queryString = [
        'search' => ['except' => ''],
        'sortField' => ['except' => 'created_at'],
        'sortDirection' => ['except' => 'desc'],
    ];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function sortBy($field)
    {
        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortDirection = 'asc';
        }
        
        $this->sortField = $field;
    }

    public function render()
    {
        $stream = Streams::make('blog_posts');
        
        $entries = $stream->entries()
            ->when($this->search, function ($query) {
                $query->where('title', 'like', '%' . $this->search . '%')
                      ->orWhere('content', 'like', '%' . $this->search . '%');
            })
            ->orderBy($this->sortField, $this->sortDirection)
            ->paginate($this->perPage);

        return view('livewire.blog-post-index', [
            'entries' => $entries,
            'stream' => $stream,
        ]);
    }
}
```

#### Form Component (Create/Edit)
```php
<?php

namespace App\Http\Livewire;

use Livewire\Component;
use Livewire\WithFileUploads;
use Streams\Core\Support\Facades\Streams;

class BlogPostForm extends Component
{
    use WithFileUploads;

    public $entryId;
    public $title = '';
    public $slug = '';
    public $content = '';
    public $featured_image;
    public $status = 'draft';
    public $published_at;

    protected $rules = [
        'title' => 'required|min:3|max:255',
        'slug' => 'required|unique:blog_posts,slug',
        'content' => 'required|min:10',
        'featured_image' => 'nullable|image|max:2048',
        'status' => 'required|in:draft,published,archived',
        'published_at' => 'nullable|date',
    ];

    public function mount($entryId = null)
    {
        if ($entryId) {
            $this->entryId = $entryId;
            $this->loadEntry();
        }
    }

    public function loadEntry()
    {
        $stream = Streams::make('blog_posts');
        $entry = $stream->repository()->find($this->entryId);
        
        if ($entry) {
            $this->fill($entry->toArray());
        }
    }

    public function updatedTitle()
    {
        if (!$this->entryId) {
            $this->slug = str()->slug($this->title);
        }
    }

    public function save()
    {
        $this->validate();

        $stream = Streams::make('blog_posts');
        
        $data = [
            'title' => $this->title,
            'slug' => $this->slug,
            'content' => $this->content,
            'status' => $this->status,
            'published_at' => $this->published_at,
        ];

        if ($this->featured_image) {
            $data['featured_image'] = $this->featured_image->store('blog-images', 'public');
        }

        if ($this->entryId) {
            $entry = $stream->repository()->find($this->entryId);
            $entry->update($data);
        } else {
            $entry = $stream->repository()->create($data);
        }

        session()->flash('message', 'Blog post saved successfully!');
        
        return redirect()->route('blog-posts.index');
    }

    public function render()
    {
        return view('livewire.blog-post-form');
    }
}
```

### Blade Templates

#### Index View
```blade
{{-- resources/views/livewire/blog-post-index.blade.php --}}
<div class="space-y-6">
    {{-- Header --}}
    <div class="sm:flex sm:items-center sm:justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Blog Posts</h1>
            <p class="mt-1 text-sm text-gray-500">Manage your blog content</p>
        </div>
        <div class="mt-4 sm:mt-0">
            <a href="{{ route('blog-posts.create') }}" 
               class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                <svg class="-ml-1 mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                </svg>
                New Post
            </a>
        </div>
    </div>

    {{-- Filters --}}
    <div class="bg-white shadow rounded-lg p-6">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <label for="search" class="block text-sm font-medium text-gray-700">Search</label>
                <input wire:model.debounce.300ms="search" 
                       type="text" 
                       id="search"
                       placeholder="Search posts..." 
                       class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
            </div>
            <div>
                <label for="perPage" class="block text-sm font-medium text-gray-700">Per Page</label>
                <select wire:model="perPage" 
                        id="perPage"
                        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                    <option value="10">10</option>
                    <option value="15">15</option>
                    <option value="25">25</option>
                    <option value="50">50</option>
                </select>
            </div>
        </div>
    </div>

    {{-- Table --}}
    <div class="bg-white shadow overflow-hidden sm:rounded-lg">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th wire:click="sortBy('title')" 
                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer hover:bg-gray-100">
                        <div class="flex items-center space-x-1">
                            <span>Title</span>
                            @if($sortField === 'title')
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    @if($sortDirection === 'asc')
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"/>
                                    @else
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                    @endif
                                </svg>
                            @endif
                        </div>
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                    <th wire:click="sortBy('published_at')" 
                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer hover:bg-gray-100">
                        Published At
                    </th>
                    <th class="relative px-6 py-3"><span class="sr-only">Actions</span></th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($entries as $entry)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                @if($entry->featured_image)
                                    <img class="h-10 w-10 rounded-lg object-cover mr-3" 
                                         src="{{ Storage::url($entry->featured_image) }}" 
                                         alt="{{ $entry->title }}">
                                @endif
                                <div>
                                    <div class="text-sm font-medium text-gray-900">{{ $entry->title }}</div>
                                    <div class="text-sm text-gray-500">{{ $entry->slug }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full 
                                @if($entry->status === 'published') bg-green-100 text-green-800
                                @elseif($entry->status === 'draft') bg-yellow-100 text-yellow-800
                                @else bg-gray-100 text-gray-800 @endif">
                                {{ ucfirst($entry->status) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ $entry->published_at ? $entry->published_at->format('M j, Y') : 'Not published' }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                            <div class="flex items-center space-x-2">
                                <a href="{{ route('blog-posts.show', $entry) }}" 
                                   class="text-indigo-600 hover:text-indigo-900">View</a>
                                <a href="{{ route('blog-posts.edit', $entry) }}" 
                                   class="text-indigo-600 hover:text-indigo-900">Edit</a>
                                <button wire:click="delete({{ $entry->id }})" 
                                        wire:confirm="Are you sure you want to delete this post?"
                                        class="text-red-600 hover:text-red-900">Delete</button>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="px-6 py-12 text-center text-gray-500">
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                            <h3 class="mt-2 text-sm font-medium text-gray-900">No blog posts</h3>
                            <p class="mt-1 text-sm text-gray-500">Get started by creating a new blog post.</p>
                            <div class="mt-6">
                                <a href="{{ route('blog-posts.create') }}" 
                                   class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700">
                                    New Post
                                </a>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Pagination --}}
    <div class="bg-white px-4 py-3 border-t border-gray-200 sm:px-6">
        {{ $entries->links() }}
    </div>
</div>
```

#### Form View
```blade
{{-- resources/views/livewire/blog-post-form.blade.php --}}
<div class="max-w-4xl mx-auto">
    <form wire:submit.prevent="save" class="space-y-6">
        {{-- Header --}}
        <div class="bg-white shadow rounded-lg p-6">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">
                        {{ $entryId ? 'Edit Blog Post' : 'Create Blog Post' }}
                    </h1>
                    <p class="mt-1 text-sm text-gray-500">
                        {{ $entryId ? 'Update your blog post information' : 'Create a new blog post' }}
                    </p>
                </div>
                <div class="flex space-x-3">
                    <button type="button" 
                            onclick="history.back()"
                            class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                        Cancel
                    </button>
                    <button type="submit" 
                            class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700">
                        <svg wire:loading wire:target="save" class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        {{ $entryId ? 'Update' : 'Create' }}
                    </button>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            {{-- Main Content --}}
            <div class="lg:col-span-2 space-y-6">
                {{-- Basic Information --}}
                <div class="bg-white shadow rounded-lg p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Basic Information</h3>
                    
                    <div class="space-y-4">
                        <div>
                            <label for="title" class="block text-sm font-medium text-gray-700">Title</label>
                            <input wire:model.lazy="title" 
                                   type="text" 
                                   id="title"
                                   class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm @error('title') border-red-300 @enderror">
                            @error('title') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label for="slug" class="block text-sm font-medium text-gray-700">Slug</label>
                            <input wire:model="slug" 
                                   type="text" 
                                   id="slug"
                                   class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm @error('slug') border-red-300 @enderror">
                            @error('slug') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label for="content" class="block text-sm font-medium text-gray-700">Content</label>
                            <div wire:ignore>
                                <textarea wire:model.defer="content" 
                                          id="content"
                                          rows="10"
                                          class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm @error('content') border-red-300 @enderror"
                                          x-data="{ 
                                              init() {
                                                  tinymce.init({
                                                      selector: '#content',
                                                      height: 400,
                                                      plugins: 'lists link image code',
                                                      toolbar: 'undo redo | blocks | bold italic | alignleft aligncenter alignright | bullist numlist | link image | code',
                                                      setup: function (editor) {
                                                          editor.on('change', function () {
                                                              @this.set('content', editor.getContent());
                                                          });
                                                      }
                                                  });
                                              }
                                          }"></textarea>
                            </div>
                            @error('content') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                        </div>
                    </div>
                </div>
            </div>

            {{-- Sidebar --}}
            <div class="space-y-6">
                {{-- Status --}}
                <div class="bg-white shadow rounded-lg p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Publishing</h3>
                    
                    <div class="space-y-4">
                        <div>
                            <label for="status" class="block text-sm font-medium text-gray-700">Status</label>
                            <select wire:model="status" 
                                    id="status"
                                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                <option value="draft">Draft</option>
                                <option value="published">Published</option>
                                <option value="archived">Archived</option>
                            </select>
                        </div>

                        <div>
                            <label for="published_at" class="block text-sm font-medium text-gray-700">Publish Date</label>
                            <input wire:model="published_at" 
                                   type="datetime-local" 
                                   id="published_at"
                                   class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                        </div>
                    </div>
                </div>

                {{-- Featured Image --}}
                <div class="bg-white shadow rounded-lg p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Featured Image</h3>
                    
                    <div class="space-y-4">
                        @if($featured_image)
                            <div class="relative">
                                <img src="{{ $featured_image->temporaryUrl() }}" 
                                     class="w-full h-32 object-cover rounded-lg">
                                <button type="button" 
                                        wire:click="$set('featured_image', null)"
                                        class="absolute top-2 right-2 bg-red-600 text-white rounded-full p-1 hover:bg-red-700">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                    </svg>
                                </button>
                            </div>
                        @endif
                        
                        <div>
                            <label for="featured_image" class="block text-sm font-medium text-gray-700">Upload Image</label>
                            <input wire:model="featured_image" 
                                   type="file" 
                                   id="featured_image"
                                   accept="image/*"
                                   class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100">
                            @error('featured_image') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
```

## Alpine.js Enhancements

### Interactive Components
```html
<!-- Dropdown Menu -->
<div x-data="{ open: false }" class="relative">
    <button @click="open = !open" 
            class="flex items-center text-sm font-medium text-gray-700 hover:text-gray-900">
        Actions
        <svg class="ml-1 h-4 w-4" :class="{ 'rotate-180': open }" 
             fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
        </svg>
    </button>
    
    <div x-show="open" 
         @click.away="open = false"
         x-transition
         class="absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg py-1 z-10">
        <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Edit</a>
        <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Delete</a>
    </div>
</div>

<!-- Modal -->
<div x-data="{ open: false }">
    <button @click="open = true" class="btn-primary">Open Modal</button>
    
    <div x-show="open" 
         x-transition.opacity
         class="fixed inset-0 bg-gray-600 bg-opacity-50 flex items-center justify-center z-50">
        <div @click.away="open = false"
             x-transition
             class="bg-white rounded-lg p-6 max-w-md w-full mx-4">
            <h3 class="text-lg font-medium mb-4">Confirm Action</h3>
            <p class="text-gray-600 mb-4">Are you sure you want to proceed?</p>
            <div class="flex justify-end space-x-3">
                <button @click="open = false" class="btn-secondary">Cancel</button>
                <button @click="open = false" class="btn-danger">Confirm</button>
            </div>
        </div>
    </div>
</div>
```

### Form Enhancements
```html
<!-- Auto-save -->
<div x-data="{ 
    saving: false, 
    saved: false,
    autoSave() {
        if (this.saving) return;
        this.saving = true;
        // Livewire save call
        setTimeout(() => {
            this.saving = false;
            this.saved = true;
            setTimeout(() => this.saved = false, 2000);
        }, 1000);
    }
}">
    <input @input.debounce.500ms="autoSave()" 
           class="form-input">
    
    <div class="flex items-center space-x-2">
        <span x-show="saving" class="text-yellow-600">Saving...</span>
        <span x-show="saved" x-transition class="text-green-600">Saved!</span>
    </div>
</div>

<!-- Character Counter -->
<div x-data="{ content: '', maxLength: 255 }">
    <textarea x-model="content" 
              :maxlength="maxLength"
              class="form-textarea"></textarea>
    <div class="flex justify-between text-sm">
        <span :class="{ 'text-red-500': content.length > maxLength * 0.9 }">
            <span x-text="content.length"></span>/<span x-text="maxLength"></span> characters
        </span>
    </div>
</div>
```

## Tailwind CSS Utilities

### Component Classes
```css
/* Custom component classes for streams */
.stream-card {
    @apply bg-white rounded-lg shadow-sm border border-gray-200 p-6 hover:shadow-md transition-shadow;
}

.stream-table {
    @apply min-w-full divide-y divide-gray-200;
}

.stream-table th {
    @apply px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider bg-gray-50;
}

.stream-table td {
    @apply px-6 py-4 whitespace-nowrap text-sm text-gray-900;
}

.stream-form {
    @apply space-y-6;
}

.stream-form .form-group {
    @apply space-y-1;
}

.stream-form label {
    @apply block text-sm font-medium text-gray-700;
}

.stream-form input[type="text"],
.stream-form input[type="email"],
.stream-form input[type="password"],
.stream-form textarea,
.stream-form select {
    @apply mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm;
}

.btn-primary {
    @apply inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500;
}

.btn-secondary {
    @apply inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500;
}

.btn-danger {
    @apply inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500;
}
```

## Usage with SDK Commands

Generate TALL components using the SDK:

```bash
# Generate full TALL stack for a stream
php artisan streams:tall blog_posts

# Generate specific components
php artisan streams:livewire blog_posts --type=index
php artisan streams:livewire blog_posts --type=form
php artisan streams:blade blog_posts --type=show

# Generate admin interface
php artisan streams:admin blog_posts --layout=sidebar
```

These commands create production-ready components with:
- Responsive Tailwind CSS styling
- Interactive Alpine.js behavior
- Livewire reactivity
- Proper Laravel integration
- Accessibility features
- SEO-friendly markup
