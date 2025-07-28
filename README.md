# Filament File Browser

A clean and modern file browser plugin for Filament that allows you to browse, upload, and manage files across different filesystem disks, including S3 buckets. The interface mimics traditional OS file explorers like Dolphin on Linux.

## Features

- 🗂️ **Multi-disk Support**: Browse files on any configured Laravel filesystem disk (local, S3, etc.)
- 📁 **Folder-first Listing**: Displays folders first, then files (like traditional file explorers)
- ✅ **Multi-selection**: Select multiple files/folders with checkboxes for bulk operations
- 📤 **File Upload**: Upload files directly to the current directory
- 📥 **Download**: Download individual files or multiple files as ZIP
- 🗑️ **Delete**: Delete files and folders with confirmation
- 🔗 **Open in New Tab**: Open files in a new browser tab
- 🧭 **Breadcrumb Navigation**: Easy navigation through directory structure
- 🎨 **Native Filament Components**: Uses Filament's native UI components for consistency

## Installation

Install the package via Composer:

```bash
composer require mydnic/filament-file-browser
```

Publish the configuration file:

```bash
php artisan vendor:publish --tag="filament-file-browser-config"
```

## Usage

### Register the Plugin

Add the plugin to your Filament panel in your `PanelProvider`:

```php
use Mydnic\FilamentFileBrowser\FilamentFileBrowserPlugin;

public function panel(Panel $panel): Panel
{
    return $panel
        // ... other configuration
        ->plugins([
            FilamentFileBrowserPlugin::make(),
        ]);
}
```

### Configuration

The plugin can be configured via the published configuration file `config/filament-file-browser.php`:

```php
return [
    // The default disk to use when the file browser is first loaded
    'default_disk' => 'public',

    // Navigation settings
    'navigation_group' => 'Files',
    'navigation_sort' => 0,

    // Temporary directory for zip downloads
    'temp_directory' => storage_path('app/temp'),

    // Maximum upload file size in MB
    'max_upload_size' => 10,

    // Allowed file extensions for upload (empty array means all extensions are allowed)
    'allowed_extensions' => [],

    // Disks to show in the file browser (empty array means all disks are shown)
    'disks' => [],
];
```

### Filesystem Configuration

Make sure your filesystem disks are properly configured in `config/filesystems.php`. For S3:

```php
'disks' => [
    's3' => [
        'driver' => 's3',
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION'),
        'bucket' => env('AWS_BUCKET'),
        'url' => env('AWS_URL'),
        'endpoint' => env('AWS_ENDPOINT'),
        'use_path_style_endpoint' => env('AWS_USE_PATH_STYLE_ENDPOINT', false),
    ],
],
```

## Architecture

The plugin follows a clean, modern architecture:

- **FileBrowserPage**: Main Filament page using native components
- **FileBrowserService**: Handles all file operations (upload, delete, zip creation)
- **Native Filament Components**: Uses Filament's built-in form components, actions, and UI elements
- **Custom Table View**: Only the file browser table uses a custom Blade view for optimal UX

## Requirements

- PHP 8.1+
- Laravel 10.0+
- Filament 3.0+

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
