# Filament File Browser

[![Latest Version on Packagist](https://img.shields.io/packagist/v/mydnic/filament-file-browser.svg?style=flat-square)](https://packagist.org/packages/mydnic/filament-file-browser)
[![GitHub Tests Action Status](https://img.shields.io/github/actions/workflow/status/mydnic/filament-file-browser/run-tests.yml?branch=main&label=tests&style=flat-square)](https://github.com/mydnic/filament-file-browser/actions?query=workflow%3Arun-tests+branch%3Amain)
[![GitHub Code Style Action Status](https://img.shields.io/github/actions/workflow/status/mydnic/filament-file-browser/fix-php-code-style-issues.yml?branch=main&label=code%20style&style=flat-square)](https://github.com/mydnic/filament-file-browser/actions?query=workflow%3A"Fix+PHP+code+styling"+branch%3Amain)
[![Total Downloads](https://img.shields.io/packagist/dt/mydnic/filament-file-browser.svg?style=flat-square)](https://packagist.org/packages/mydnic/filament-file-browser)

A powerful file browser plugin for Filament that allows users to browse, upload, download, and manage files on any configured Laravel filesystem disk.

![Filament File Browser Screenshot](https://github.com/mydnic/filament-file-browser/raw/main/art/screenshot.png)

## Features

- Browse files and directories on any configured filesystem disk
- Upload files to the current directory
- Download individual files or multiple files as a ZIP archive
- Delete files and directories
- Navigate through directories with breadcrumbs
- List view with folders first, then files
- Multi-selection with checkboxes for bulk actions
- "Open in new tab" action for files
- Compatible with S3 and other Laravel filesystem drivers

## Installation

You can install the package via composer:

```bash
composer require mydnic/filament-file-browser
```

After installing the package, run the installation command:

```bash
php artisan filament-file-browser:install
```

This will:
- Publish the configuration file
- Create the necessary directories
- Set up symbolic links if needed

## Configuration

You can publish the config file with:

```bash
php artisan vendor:publish --tag="filament-file-browser-config"
```

This is the contents of the published config file:

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

## Usage

### Register the plugin with Filament

Add the plugin to your panel provider:

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

### Access the file browser

Once installed and registered, the file browser will be available in your Filament panel under the "Files" navigation group (or the group you configured in the config file).

### Using the Facade

You can also use the `FilamentFileBrowser` facade to interact with the file browser programmatically:

```php
use Mydnic\FilamentFileBrowser\Facades\FilamentFileBrowser;

// Get files and directories
$items = FilamentFileBrowser::getFilesAndDirectories('public', '/');

// Upload a file
$path = FilamentFileBrowser::uploadFile('public', '/', $uploadedFile);

// Delete items
FilamentFileBrowser::deleteItems('public', ['/path/to/file.txt', '/path/to/directory']);

// Create a ZIP from items
$zipPath = FilamentFileBrowser::createZipFromItems('public', ['/path/to/file.txt', '/path/to/directory']);

// Get available disks
$disks = FilamentFileBrowser::getAvailableDisks();
```

## Customization

### Changing the navigation group

You can change the navigation group by updating the `navigation_group` value in the config file.

### Restricting disks

By default, the file browser shows all configured disks. You can restrict which disks are available by setting the `disks` array in the config file:

```php
'disks' => ['public', 's3'],
```

### File upload restrictions

You can restrict the file types that can be uploaded by setting the `allowed_extensions` array in the config file:

```php
'allowed_extensions' => ['jpg', 'png', 'pdf', 'doc', 'docx'],
```

## Testing

```bash
composer test
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Contributing

Please see [CONTRIBUTING](.github/CONTRIBUTING.md) for details.

## Security Vulnerabilities

Please review [our security policy](../../security/policy) on how to report security vulnerabilities.

## Credits

- [Mydnic](https://github.com/mydnic)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
