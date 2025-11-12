---
title: Asset Management
description: 'Managing assets and images in Streams Core.'
sort_order: 4
category: core-concepts
status: live
---

# Asset Management

Streams Core provides a comprehensive asset management system for handling files, images, and other media assets in your applications.

## Overview

The asset management system provides:

- **File Upload and Storage**: Handle file uploads to various storage disks
- **Image Processing**: Resize, crop, and manipulate images on-the-fly
- **Asset Organization**: Organize assets in folders and collections
- **CDN Integration**: Serve assets from CDNs for improved performance
- **Security**: Control access to sensitive files
- **Optimization**: Automatic optimization and compression

## Basic Usage

### File Uploads

```php
// Handle file upload
$file = request()->file('document');
$asset = Assets::upload($file);

// Upload to specific disk
$asset = Assets::disk('s3')->upload($file);

// Upload with custom path
$asset = Assets::upload($file, 'documents/contracts');
```

### Image Uploads

```php
// Upload image with automatic processing
$image = Images::upload(request()->file('photo'));

// Upload with resize
$image = Images::upload(request()->file('photo'))
    ->resize(800, 600)
    ->save();

// Upload with multiple sizes
$image = Images::upload(request()->file('photo'))
    ->sizes([
        'thumbnail' => '150x150',
        'medium' => '400x300',
        'large' => '800x600'
    ])
    ->save();
```

## Asset Field Types

### File Field

```json
{
    "handle": "document",
    "type": "file",
    "config": {
        "disk": "public",
        "path": "documents",
        "extensions": ["pdf", "doc", "docx", "txt"],
        "max_size": "10MB"
    }
}
```

### Image Field

```json
{
    "handle": "featured_image",
    "type": "image",
    "config": {
        "disk": "public",
        "path": "images",
        "extensions": ["jpg", "jpeg", "png", "gif", "webp"],
        "max_size": "5MB",
        "min_width": 300,
        "min_height": 200,
        "thumbnails": {
            "small": "150x150",
            "medium": "400x300",
            "large": "800x600"
        }
    }
}
```

### Multiple Files Field

```json
{
    "handle": "attachments",
    "type": "files",
    "config": {
        "multiple": true,
        "max_files": 5,
        "extensions": ["pdf", "doc", "xls", "zip"],
        "max_size": "20MB"
    }
}
```

## Image Processing

### Basic Operations

```php
// Get image
$image = Images::make($entry->featured_image);

// Resize image
$resized = $image->resize(400, 300);

// Crop image
$cropped = $image->crop(200, 200, 100, 50); // width, height, x, y

// Fit image (maintain aspect ratio)
$fitted = $image->fit(400, 300);

// Scale image
$scaled = $image->scale(0.5); // 50% of original size
```

### Advanced Operations

```php
// Apply filters
$filtered = $image
    ->brightness(20)
    ->contrast(15)
    ->gamma(1.2)
    ->blur(2);

// Add watermark
$watermarked = $image->watermark('logo.png', [
    'position' => 'bottom-right',
    'opacity' => 0.7,
    'margin' => 20
]);

// Convert format
$converted = $image->format('webp', 85); // 85% quality

// Auto-orient based on EXIF
$oriented = $image->orient();
```

### Responsive Images

```php
// Generate responsive image set
$responsive = $image->responsive([
    '480w' => ['width' => 480],
    '768w' => ['width' => 768],
    '1024w' => ['width' => 1024],
    '1440w' => ['width' => 1440]
]);

// Get srcset string
echo $responsive->srcset();

// Get picture element
echo $responsive->picture(['class' => 'responsive-image']);
```

## Asset Sources

### Local Storage

```php
// Configure local storage
Assets::disk('local', [
    'driver' => 'local',
    'root' => storage_path('app/assets'),
    'url' => '/storage/assets',
    'visibility' => 'public'
]);
```

### Amazon S3

```php
// Configure S3 storage
Assets::disk('s3', [
    'driver' => 's3',
    'key' => env('AWS_ACCESS_KEY_ID'),
    'secret' => env('AWS_SECRET_ACCESS_KEY'),
    'region' => env('AWS_DEFAULT_REGION'),
    'bucket' => env('AWS_BUCKET'),
    'url' => env('AWS_URL'),
    'endpoint' => env('AWS_ENDPOINT'),
]);
```

### CDN Integration

```php
// Use CDN for asset delivery
Assets::cdn('cloudfront', [
    'url' => 'https://d123456.cloudfront.net',
    'secure' => true
]);

// Get CDN URL
$url = $asset->cdn('cloudfront')->url();
```

## Asset Organization

### Folders and Collections

```php
// Create asset collection
$collection = Assets::collection('product-images');

// Add assets to collection
$collection->add($image1);
$collection->add($image2);

// Get collection assets
$assets = $collection->assets();

// Organize by folder
$asset->folder('products/electronics');
```

### Tagging

```php
// Tag assets
$asset->tag(['product', 'featured', 'electronics']);

// Find by tags
$taggedAssets = Assets::tagged(['featured'])->get();

// Tag management
$asset->addTag('new');
$asset->removeTag('old');
$asset->syncTags(['current', 'featured']);
```

## Asset Security

### Access Control

```php
// Restrict file access
$asset->access([
    'roles' => ['admin', 'editor'],
    'permissions' => ['view_files']
]);

// Check access
if ($asset->canAccess(auth()->user())) {
    // User has access
}

// Secure URLs (temporary access)
$secureUrl = $asset->secureUrl('+1 hour');
```

### File Validation

```php
// Validate file uploads
Assets::validate($file, [
    'extensions' => ['jpg', 'png', 'gif'],
    'max_size' => '5MB',
    'min_dimensions' => '300x200',
    'max_dimensions' => '2000x2000',
    'mime_types' => ['image/jpeg', 'image/png']
]);
```

## Asset Optimization

### Automatic Optimization

```php
// Enable automatic optimization
Images::optimize([
    'jpeg_quality' => 85,
    'png_compression' => 9,
    'webp_quality' => 80,
    'strip_metadata' => true
]);

// Lazy optimization
$image->lazy()->optimize();
```

### Compression

```php
// Compress image
$compressed = $image->compress(80); // 80% quality

// Lossless compression
$lossless = $image->compress('lossless');

// Progressive JPEG
$progressive = $image->progressive();
```

## Asset URLs and Serving

### URL Generation

```php
// Basic asset URL
$url = $asset->url();

// Versioned URL (cache busting)
$versionedUrl = $asset->version()->url();

// Signed URL (temporary access)
$signedUrl = $asset->signedUrl('+1 hour');

// Thumbnail URL
$thumbnailUrl = $image->thumbnail('medium')->url();
```

### Custom Routes

```php
// Custom asset routes
Route::get('/assets/{path}', function ($path) {
    $asset = Assets::find($path);
    
    if (!$asset || !$asset->canAccess()) {
        abort(404);
    }
    
    return $asset->response([
        'cache' => 3600,
        'headers' => [
            'Content-Disposition' => 'inline'
        ]
    ]);
})->where('path', '.*');
```

## Caching

### Asset Caching

```php
// Cache processed images
$cached = $image->cache('1 hour')->resize(400, 300);

// Cache with custom key
$cached = $image->cacheKey('product-thumb-' . $product->id)->thumbnail();

// Clear cache
$image->clearCache();
Assets::clearCache(); // Clear all asset cache
```

### Browser Caching

```php
// Set cache headers
$asset->headers([
    'Cache-Control' => 'public, max-age=31536000',
    'Expires' => now()->addYear()->toRfc822String()
]);
```

## Events

### Asset Events

```php
// Asset uploaded
Event::listen('asset.uploaded', function ($asset) {
    // Process uploaded asset
    Log::info('Asset uploaded: ' . $asset->filename);
});

// Image processed
Event::listen('image.processed', function ($image, $operation) {
    // Handle processed image
});

// Asset deleted
Event::listen('asset.deleted', function ($asset) {
    // Cleanup related data
});
```

## Configuration

### Asset Configuration

```php
// config/streams/assets.php
return [
    'default_disk' => 'public',
    
    'disks' => [
        'public' => [
            'driver' => 'local',
            'root' => storage_path('app/public/assets'),
            'url' => env('APP_URL') . '/storage/assets',
        ],
        
        's3' => [
            'driver' => 's3',
            'key' => env('AWS_ACCESS_KEY_ID'),
            'secret' => env('AWS_SECRET_ACCESS_KEY'),
            'region' => env('AWS_DEFAULT_REGION'),
            'bucket' => env('AWS_BUCKET'),
        ],
    ],
    
    'optimization' => [
        'enabled' => true,
        'quality' => [
            'jpeg' => 85,
            'webp' => 80,
            'png' => 9
        ]
    ],
    
    'cache' => [
        'enabled' => true,
        'ttl' => 3600,
        'disk' => 'local'
    ]
];
```

### Image Configuration

```php
// config/streams/images.php
return [
    'driver' => 'gd', // or 'imagick'
    
    'thumbnails' => [
        'small' => '150x150',
        'medium' => '400x300',
        'large' => '800x600'
    ],
    
    'formats' => [
        'webp' => ['quality' => 80],
        'jpeg' => ['quality' => 85],
        'png' => ['compression' => 9]
    ],
    
    'watermark' => [
        'source' => 'watermark.png',
        'position' => 'bottom-right',
        'opacity' => 0.7,
        'margin' => 20
    ]
];
```

## Best Practices

1. **Choose appropriate formats**: Use WebP for web images, PNG for transparency
2. **Optimize for performance**: Compress images and use appropriate sizes
3. **Use CDNs**: Serve assets from CDNs for better performance
4. **Secure sensitive files**: Implement access controls for private files
5. **Version assets**: Use versioning for cache busting
6. **Lazy load images**: Implement lazy loading for better page performance
7. **Generate responsive images**: Provide multiple sizes for different devices
8. **Monitor storage usage**: Keep track of storage costs and usage
9. **Backup assets**: Ensure assets are included in backup strategies
10. **Use appropriate storage**: Choose storage solutions based on your needs

## Examples

### Photo Gallery

```php
// Upload multiple photos
$photos = [];
foreach (request()->file('photos') as $file) {
    $photo = Images::upload($file)
        ->resize(1200, 800)
        ->thumbnail('small', '200x200')
        ->thumbnail('medium', '600x400')
        ->optimize()
        ->save();
        
    $photos[] = $photo;
}

// Display gallery
foreach ($photos as $photo) {
    echo '<img src="' . $photo->thumbnail('medium')->url() . '" 
               alt="Gallery image"
               loading="lazy">';
}
```

### Document Management

```php
// Upload document with validation
try {
    $document = Assets::upload(request()->file('document'))
        ->validate([
            'extensions' => ['pdf', 'doc', 'docx'],
            'max_size' => '10MB'
        ])
        ->folder('documents/' . auth()->id())
        ->save();
        
    // Generate secure download link
    $downloadUrl = $document->secureUrl('+24 hours');
    
} catch (ValidationException $e) {
    // Handle validation errors
}
```
